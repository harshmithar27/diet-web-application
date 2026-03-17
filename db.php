<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "diet_db";

$conn =  new mysqli("localhost","root","","diet_db");

if (!$conn) {
    die("Database connection failed");
}
?>



