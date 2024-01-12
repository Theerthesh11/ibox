<?php
//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$db_username = "root";
$password = "";
$db="inboxflow";
// Create connection
$conn =new mysqli($servername, $db_username, $password,$db);
// Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . mysqli_connect_error());
// }else{
// echo "Connected successfully";
// }
?>
