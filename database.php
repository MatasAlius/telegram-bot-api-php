<?php
// Change database credentials
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Failed: " . $conn->connect_error);
}
mysqli_set_charset($conn,"utf8");
?> 