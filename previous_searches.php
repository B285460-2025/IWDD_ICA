<?php
$page = 'previous_searches';
require_once 'my_functions.php';
?>
<html>
<head>
    <title>Previous Searches - Protein Explorer</title>
    <link rel="stylesheet" href="css/global_layout.css" type="text/css">
</head>
    <body>
    <!-- Header & Sidebar stored in separate template file as will be reused in every page -->
    <?php include 'templates/header_and_sidebar.html';?>
    <content>
        <h2> Previous Searches:</h2>
        <div style="border: 2px solid #c9b8e8; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
        <?php 
        # Connect to db
            $db_con = mySqlLogin();
            # Get previous searches from the searches table
            $query = 'SELECT * FROM searches';
                $sql_result = $db_con->prepare($query);
                $sql_result->execute();
                $sql_result = $sql_result->fetchAll(PDO::FETCH_ASSOC);
                if (count($sql_result) > 0) {
                # Convert these into a table with links to each results page
                    echo "</br></br><table>";
                    echo "<tr><th>Protein Family Search Term</th><th>Taxonomic Group Search Term</th><th>View Results</th></tr>";
                    foreach ($sql_result as $entry) {
                        $search_id = htmlspecialchars($entry['search_id']);
                        echo "<tr onclick=\"window.location='results.php?search_id=" . $search_id . "'\" style='cursor: pointer;'>";
                        echo "<td>" . ucwords(htmlspecialchars($entry['protein'])) . "</td>";
                        echo "<td>" . ucwords(htmlspecialchars($entry['taxonomic_group'])) . "</td>";
                        echo "<td><a href='results.php?search_id=" . $search_id . "'>View</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No previous searches found.</p>";
                }
        ?>
        </div>
</content>
<?php include 'templates/footer.html'; ?>
</body>

</html>
