<?php
# https://stackoverflow.com/questions/381265/better-way-to-check-variable-for-null-or-empty-string
# Below is a function to check if a string is empty - check if it null, or if trimmed check if it is an empty string
# Adapted from the above stackoverflow link
function isEmptyString(string|null $str){
    return $str === null || trim($str) === '';
}

function mySqlLogin(){
    require_once 'login.php';
    $db_server = "mysql:host=$hostname;dbname=$database;charset=utf8mb4";
    $conn = new PDO($db_server, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "\nConnected you successfully to the <b>$database</b> database!<br/>";
    return $conn;
}

function errorMessage(string $str){
    echo "<div style='border: 3px solid red; border-radius: 8px; padding: 10px; margin-top: 5px '>"; # margin-top used here to create a separation from the summary div for more appealing look
    echo "<p><b>Error:</b></br> " . $str . "</p>"; # nl2br() function converts the python /n to html </br> - found here: https://stackoverflow.com/questions/5946114/how-can-i-replace-newline-or-r-n-with-br
    echo "</div>";
}

?>
