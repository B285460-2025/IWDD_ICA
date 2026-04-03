
<?php $page = 'about';?>
<html>
<head>
    <title>Help - Protein Explorer</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
<body>
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
        <h2>An Overview of implementation </h2>

         <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <p>This website follows a MVC (model, view, controller) style architecture:</p>
            
            <h4>Model:</h4>
            <p>
                A MySQL database housing 5 related tables. Each table contains data from a different output of the analysis pipeline, 
                however, each is linked together via the search_id which is housed in the searches table to assoicate all samples that were part of a search. An initialisation
                SQL script has been provided with the code to create the required tables. All SQL interactions between the database and the controller are done via PDO. 
            </p>

            <h4>View:</h4>
            <p>
                The view has been implemented via a combination of HTML and PHP. Styling has been done with a combination of inline CSS styling for individual
                elements and a global stylesheet for overall page layouts and elements that are used in multiple places (e.g. tables and buttons). Each page in this site has its own
                php file, the header and sidebar has been placed into one template and the footer into another, both are called via include statements in each php page. There are small
                elements of javascript used for functionality such as redirecting to results pages upon completion of the analysis pipeline and to make the tabs on the pepstats portion of the results page.
            </p>

            <h4>Controller:</h4>
            <p>
                The controller element is comprised of PHP and python. Within the search.php file a PHP script orchestrates the overall analysis pipeline. Each inidividual analysis step is 
                executed via a separate python script which is designed to work alone, but written under the assumption that it is called by the php pipeline in a defined way (as such some level of validation
                is excluded in the python scripts for simplicity under the assumption that the validation has been performed in the higher level php orchestratation script). All outputs from the python scripts follow
                a similar structure in that they are json objects containing a data element and a status element. If the status returned is 1, that means something has gone wrong and the pipeline will fail with the error
                message that was carried over from the python script, if the status is 0 then that step of the analysis pipeline has been completed successfully.  </br>

                All python scripts are run using a python virtual environment, any non-standard packages are listed in a requirements file.
            </p>
        </div>

         <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h3>Failures and Error Handling:</h3>
            <p>
                As mentioned above, if a python script fails for any reason it passes back a status code of 1 to the orchestrating php script along with the error message generated. This message is then displayed to the user
                and the analysis pipeline is halted. Any intermediate temporary files that have been generated along the way should be cleared up.</br></br>

                The pipeline follows a pattern of doing all of the analyses and then upon successful completion of them all, writing results to the database. Originally, the design idea involved writing the results of each analysis
                to the database as soon as that stage of the analysis was complete. However, this created more complications with clearing out data from the db related to the search if a downstream analysis failed. By doing the database
                interactions in one go, it ensures that all data is ready to be written, any failures in writing data are captured in a catch statement wherein the database is cleared down for any records relating to that search. This is easily 
                done as all records in all tables that relate to a certain search are connected by the search_id and use 'on delete cascade' to remove the associated records if the search table record is removed.
            </p>
        </div>

         <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <h3>Results Page</h3>
            <p>
                The results page is a standard template containing php to query the MySQL database and then display the retrived information. The search_id for a run is provided to the page via a html query string, so with a known 
                search_id the results page can be accessed via a url. This can be useful in cases where some level of automation to retrieve the generated reports is used.
            </p>
        </div>

</content>
 
    <?php include 'templates/footer.html'; ?>
</body>
</html>
