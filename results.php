<?php
$page = 'results';
require_once 'my_functions.php';
?>
<html>
<head>
    <title>Results - Protein Explorer</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
    <body>
     <!-- Header & Sidebar stored in separate template file as will be reused in every page -->
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
        <h2> Results:</h2>
         <?php
            $db_con = mySqlLogin();
            $search_id = $_GET['search_id'] ?? null;
            if (!$search_id) {
                errorMessage("No search ID provided, please provide it via the ?search_id= in url or select from the history page");
            } else {

            $query = 'SELECT * FROM searches where search_id = ?';
            $sql_result = $db_con->prepare($query) ;
            $sql_result->execute([htmlspecialchars($search_id)]);
            $sql_result = $sql_result->fetchAll(PDO::FETCH_ASSOC);
            $sql_result = $sql_result[0];
            // $sql_result = json_decode($sql_result, true);
            echo "<h3><b>Protein Family:</b> " . ucwords($sql_result['protein']) . "</br><b>Taxonomic Group:</b> " .  ucwords($sql_result['taxonomic_group']) . "</br></h3>";
            }
        ?>
            <!-- Display the sequences in a table with links to ncbi records -->
            <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <h4><b>Sequences in this analysis:</b></h4>
                <?php
                if ($search_id) {
                    $query = 'SELECT ncbi_id, p_description, p_length, protein, organism FROM ncbi WHERE search_id = ?';
                    $sql_result = $db_con->prepare($query);
                    $sql_result->execute([htmlspecialchars($search_id)]);
                    $sequences = $sql_result->fetchAll(PDO::FETCH_ASSOC);

                    if (count($sequences) > 0) {
                        echo "<table>";
                        echo "<tr><th>NCBI ID</th><th>Protein</th><th>Organism</th><th>Length</th></tr>";
                        foreach ($sequences as $entry) {
                            $ncbi = htmlspecialchars($entry['ncbi_id']);
                            echo "<tr onclick=\"window.location='https://www.ncbi.nlm.nih.gov/protein/" . $ncbi . "'\" style='cursor: pointer;'>";  # Turns whole row into link to orginal ncbi entry -  https://stackoverflow.com/questions/5319638/tr-onclick-not-working
                            echo "<td><a href='https://www.ncbi.nlm.nih.gov/protein/" . $ncbi . "'>" . $ncbi . "</a></td>";
                            echo "<td>" . htmlspecialchars($entry['protein']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['organism']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['p_length']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No sequences found for this search</p>";
                    }
                }
            ?>
            </div>
            <!-- Display the conservation plot -->
            <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <h4><b>Sequence Conservation Analysis:</b></h4>
                <?php
                if ($search_id) {
                    $query = 'SELECT plotcon_file, clustalo_file FROM conservation WHERE search_id = ?';
                    $sql_result = $db_con->prepare($query);
                    $sql_result->execute([htmlspecialchars($search_id)]);
                    $files = $sql_result->fetchAll(PDO::FETCH_ASSOC);
                    if (count($files) > 0) {
                        echo '<img src="' . $files[0]['plotcon_file'] . '">';
                        # Download button for alignment file
                        echo "<h5> Raw Alignment File</h5>";
                        echo '<a href="' . htmlspecialchars($files[0]["clustalo_file"]) . '" download><input type="button" value="Download" class="submit"></a>';

                    } else {
                        echo "<p>Error: No Clustalo file found for this search</p>";
                    }
                }
                ?>
            </div>
            <!--  Display the results from motif analysis (if any were found) -->
            <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <h4><b>Motif Analysis:</b></h4>
                <?php
                if ($search_id) {
                    $query = 'SELECT * FROM motif WHERE search_id = ?';
                    $sql_result = $db_con->prepare($query);
                    $sql_result->execute([htmlspecialchars($search_id)]);
                    $motif_results = $sql_result->fetchAll(PDO::FETCH_ASSOC);

                    if (count($motif_results) > 0) {
                        echo "<table>";
                        echo "<tr><th>Sequence Name</th><th>Start</th><th>End</th><th>Score</th><th>Strand</th><th>Motif</th></tr>";
                        foreach ($motif_results as $entry) {
                            echo "<td>" . htmlspecialchars($entry['seq_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['s_start']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['s_end']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['score']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['strand']) . "</td>";
                            echo "<td>" . htmlspecialchars($entry['motif']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No motifs were found for any of the sequences in this analysis</p>";
                    }
                }
                ?>
            <!-- Show pepstats results with each sequence having its own tab -->
             <!-- Code for this bit was adpated from https://www.w3schools.com/howto/howto_js_tabs.asp -->
            </div>
                <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <h4><b>Pepstats Analysis:</b></h4>
                <p>Please click on an ID to view the results</p>
                
                <?php
                if ($search_id) {
                    $query = 'SELECT * FROM pepstats WHERE search_id = ?';
                    $sql_result = $db_con->prepare($query);
                    $sql_result->execute([htmlspecialchars($search_id)]);
                    $pepstats_results = $sql_result->fetchAll(PDO::FETCH_ASSOC);

                    # Adapted from https://www.w3schools.com/howto/howto_js_tabs.asp with AI help
                    echo '<div class="tab">';
                    foreach ($pepstats_results as $index => $entry) {
                        echo '<button class="tablinks" onclick="openTab(event, \'' . $entry['ncbi_id'] . '\')">' . $entry['ncbi_id'] . '</button>';
                    }
                    echo '</div>';

                    foreach ($pepstats_results as $index => $entry) {
                        echo '<div id="' . $entry['ncbi_id'] . '" class="tabcontent">';
                        echo '<h5>Sequence: ' . $entry['ncbi_id'] . '</h5>';
                        echo '<pre>' . $entry['report_content'] . '</pre>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

</content>
<?php include 'templates/footer.html'; ?>
<script src="scripts/pepstats_tabs.js"></script> <!-- Housing the js script in a different file to reduce clutter -->
</body>

</html>
