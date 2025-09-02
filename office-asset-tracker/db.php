<?php
$host = "localhost";
$user = "root";  // change if needed
$pass = "";      // your DB password
$db   = "office_asset_tracker";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
