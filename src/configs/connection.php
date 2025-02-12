<?php

// USER CONNECTION
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "user"; 


$conn_user = new mysqli($servername, $username, $password, $database);

if ($conn_user->connect_error) {
    die("Connection failed: " . $conn_user->connect_error);
}

echo "Connected successfully";

?>
