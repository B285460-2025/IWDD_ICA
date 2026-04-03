<?php 
$page = 'search';
# Bring in the functions i've coded from another file & db login details
require_once 'my_functions.php';
 ?>
<html>
<head>
    <title>Search - Protein Explorer</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
    <body>
    <!-- Header & Sidebar stored in separate template file as will be reused in every page -->
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
    <h2>Do an Analysis</h2>
    <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
        <h3>What do I do?</h3>

        <p>All you need to do is enter two pieces of information:</p>
        <ol>
            <li>The type of protein sequences you're interested in (e.g. glucose-6-phosphatase, ABC transporters, kinases).</li>
            <li>The taxonomic group or oganism name you are interested in (e.g. Aves, Mouse, Mammalia).</li>
        </ol>

        <p>If no search terms are provided, a default search of glucose-6-phosphatase proteins and Aves taxonomic group will be performed for you.</p>
    </div>      

    <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
    <h3><u>Provide your inputs:</u></h3>
    <!-- Form for user inputs-->
    <form method="post" action="">
        <label for="protein"><b>Protein Family:</b></label><br>
        <input type="text" id="protein" name="protein" placeholder="glucose-6-phosphatase proteins" size=30><br><br>
        <!-- Size Specified above so the textbox can fit the whole placeholder value -->

        <label for="Organism"><b>Taxonomic Group:</b></label><br>
        <input type="text" id="Organism" name="Organism" placeholder="Aves"><br><br>

        <input type="submit" value="Submit" class="submit">
    </form>

    <!-- php to process the inputs-->
    <?php
    $defaultProtein = 'glucose-6-phosphatase proteins';
    $defaultOrgansim = 'Aves';

    # Take form input and assign to php vars
    # W3 schools page used for help - https://www.w3schools.com/php/php_superglobals_post.asp
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $protein = $_POST['protein'] ?? '';
        $organism = $_POST['Organism'] ?? '';
        $now = new DateTime("now");

        # Validate that the form had values entered
        # If no values given, set them to 'default' values
        if (isEmptyString($protein) && isEmptyString($organism)) {
            echo "<script>alert(\"Oops, looks like neither of your fields are filled in.\\nI'll do a default search for you!\\nProtein: " . $defaultProtein ."\\nOrganism: " . $defaultOrgansim . "\");</script>";
            $protein = $defaultProtein;
            $organism = $defaultOrgansim;
            $valid = TRUE;

        } elseif (isEmptyString($protein) || isEmptyString($organism)) {
            echo "<script>alert('Oops, looks like one of your fields is missing.');</script>";
            $valid = FALSE;
        } else {
            $valid = TRUE;
        }

        # If entries have been given to both fields, print summary and send to python script
        # Python script will do further validation of the provided inputs.
        if ($valid) {
            echo "<div style='border: 3px solid grey; border-radius: 8px; padding: 10px;'>
            <p><b>Search summary:</b></br>
            Protein Family of interest: <b>$protein</b></br>
            Taxonomic Group of interest: <b>$organism</b></br>
            Time of search: <b>" . $now->format(' H:i:s, F jS Y') . "</b></p>
            </div>";

            # Pass our input variables to a python script
            # Entrez email needed for python script, hardcoding my own email here to ensure one is always present
            # As this is in the php section it won't be viewable to anyone looking at the webpage - security reasons
            # TODO: add optional entrez email input
            $protein_escaped = escapeshellarg($protein);
            $organism_escaped = escapeshellarg($organism);
            $result = shell_exec("webpyenv/bin/python scripts/validate_input.py $protein_escaped $organism_escaped");
            $esearch = json_decode($result, true);

            if ($esearch['status'] == 0) {
                try{
                    # Check if the search_id is already in the database
                    $db_con = mySqlLogin();
                    $query = 'SELECT DISTINCT * FROM searches where search_id = ?';
                    $sql_result = $db_con->prepare($query) ;
                    $sql_result->execute([$esearch['hash']]);
                    $sql_result = $sql_result->fetchAll(PDO::FETCH_ASSOC);
                    # If in db, we already have the analysis so don't need to repeat - redurect to the results page
                    if (count($sql_result) > 0) {
                        echo "<script>alert('Looks like this analyis has already been run, I'll take you to the results');</script>";
                        echo "<script>window.location='results.php?search_id=" . urlencode($esearch['hash']) . "';</script>";
                        exit();

                    # If not then we need to kick off analysis
                    } else {
                        ########### Get the records from NCBI protein db ############
                        $efetch_result = shell_exec("webpyenv/bin/python scripts/efetch.py " . $esearch['hash']);
                        $efetch_result = json_decode($efetch_result, true);

                        # Validate response is successful via status
                        if ($efetch_result['status'] == 1) {
                            errorMessage($efetch_result['data']);
                        } else {
                            echo "Successfully Retrieved records from NCBI protein db </br>";
                        
                            ########## Sequence conservation Analysis ############
                            # Write json data into a tempfile becuase it's too complex to pass in via sys.argv
                            $tmpfile = "temp/" . $esearch['hash'] . "_temp_json_data";
                            file_put_contents($tmpfile, $efetch_result['data']);
                            $sca_result = shell_exec("webpyenv/bin/python scripts/conservation.py " . escapeshellarg($tmpfile));
                            $sca_result = json_decode($sca_result, true);

                            # Validate response is successful via status
                            if ($sca_result['status'] == 1) {
                                unlink($tmpfile); # Remove the tempfile
                                errorMessage($sca_result['data']);
                                
                            } else {
                                echo "Successfully Completed Sequence Conservation Analysis </br>";
                                ########## Motif Analysis ############
                                $motif_result = shell_exec("webpyenv/bin/python scripts/motifs.py " . escapeshellarg($tmpfile));
                                $motif_result = json_decode($motif_result, true);
                                if ($motif_result['status'] == 1) {
                                    unlink($tmpfile);
                                    errorMessage($motif_result['data']);
                                } else {
                                    
                                    echo "Successfully Conducted Motif Analysis </br>";

                                    ########## Pepstats Analysis ############
                                    $pepstats_result = shell_exec("webpyenv/bin/python scripts/pepstats.py " . escapeshellarg($tmpfile));
                                    $pepstats_result = json_decode($pepstats_result, true);
                                    

                                    if ($pepstats_result['status'] == 1) {
                                        unlink($tmpfile);
                                        errorMessage($pepstats_result['data']);
                                    } else { 
                                        echo "Successfully Conducted Pepstats Analysis </br>";
                                        unlink($tmpfile);

                                        ########### Put results into our local db ################## - any failures here will cause PDO exception which will trigger db cleanup for this search.
                                        # Searches table
                                        $query = 'INSERT INTO searches(search_id, protein, taxonomic_group) values (?, ?, ?)';
                                        $sql_result = $db_con->prepare($query) ;
                                        $sql_result->execute([$esearch['hash'], $protein, $organism]);

                                        # ncbi table - multiple rows per analysis here so we use the for each statement
                                        $query = 'INSERT INTO ncbi(ncbi_id, p_sequence, p_description, search_id, protein, organism) values (?, ?, ?, ?, ?, ?)';
                                        $sql_result = $db_con->prepare($query);
                                        # Decode the data portion of efetch results because this is also a dictionary
                                        $sequence_data = json_decode($efetch_result['data'], true);
                                        # For Each statement generated with the aid of AI
                                        foreach ($sequence_data as $ncbi_id => $record) {
                                            $sql_result->execute([$record['ncbi_id'], $record['sequence'], $record['description'], $esearch['hash'], $record['protein'], $record['organism']]);
                                        }
                                        # Conservation table - don't need to write id as it's auto generated
                                        $query = 'INSERT INTO conservation(plotcon_file, clustalo_file, search_id) values (?, ?, ?)';
                                        $sql_result = $db_con->prepare($query) ;
                                        $sql_result->execute([$sca_result['data']['plotcon_file'], $sca_result['data']['alignment_file'], $esearch['hash']]);

                                        # Motif table - Only need to fill if we have results
                                        if (count($motif_result['data']) > 0){
                                        $query = 'INSERT INTO motif(ncbi_id, seq_name, s_start, s_end, score, strand, motif, search_id) values (?, ?, ?, ?, ?, ?, ?, ?)';
                                        $sql_result = $db_con->prepare($query);

                                        foreach ($motif_result['data'] as $record) {
                                            $sql_result->execute([$record['id'], $record['SeqName'], $record['Start'], $record['End'], $record['Score'], $record['Strand'], $record['Motif'], $esearch['hash']]);
                                        }
                                        } else{
                                            echo "No motifs found";
                                        }
                                        # Pepstats table
                                        $query = 'INSERT INTO pepstats(ncbi_id, report_content, search_id) values (?, ?, ?)';
                                        $sql_result = $db_con->prepare($query);

                                        foreach ($pepstats_result['data'] as $ncbi_id => $record) {
                                            $sql_result->execute([$ncbi_id, $record, $esearch['hash']]);
                                        }

                                    }
                                    echo "<script>window.location='results.php?search_id=" . urlencode($esearch['hash']) . "';</script>";

                                }
                            }
                        }
                    }
                # Catch any db errors
                } catch(PDOException $e) {
                    # If anything fails writing to db, clear out db for search_id so we can re-run analysis
                    try{
                        $query = 'DELETE FROM searches where search_id = ?';
                        $sql_result = $db_con->prepare($query) ;
                        $sql_result->execute([$esearch['hash']]);
                    } catch(PDOException $e) { }
    
                    errorMessage("Database Connection Issue<br/>" . $e->getMessage());
                }
            } elseif ($esearch['status'] == 1) {
                 errorMessage(nl2br($esearch['message']));
            }
        }
    }
    ?>
    </div>
    </content>
<?php include 'templates/footer.html'; ?>
</body>
</html>
