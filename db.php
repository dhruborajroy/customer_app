<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customer_app";
date_default_timezone_set("Asia/Dhaka");
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
