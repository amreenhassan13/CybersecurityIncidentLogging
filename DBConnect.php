<?php
// DBConnect.php — central DB connection (mysqli)
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "cybersecurity_incident_logging";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
