<?php
$host = "localhost";
$user = "root";
$password = "";   // leave empty if no password set
$dbname = "users";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
