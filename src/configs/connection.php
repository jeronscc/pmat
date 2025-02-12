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

// ILCDB CONNECTION
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "ilcdb"; 


$conn_ilcdb = new mysqli($servername, $username, $password, $database);

if ($conn_ilcdb->connect_error) {
    die("Connection failed: " . $conn_ilcdb->connect_error);
}

echo "Connected successfully";

// DTC CONNECTION
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "dtc"; 


$conn_dtc = new mysqli($servername, $username, $password, $database);

if ($conn_dtc->connect_error) {
    die("Connection failed: " . $conn_dtc->connect_error);
}

echo "Connected successfully";

// SPARK CONNECTION
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "spark"; 


$conn_spark = new mysqli($servername, $username, $password, $database);

if ($conn_spark->connect_error) {
    die("Connection failed: " . $conn_spark->connect_error);
}

echo "Connected successfully";

// PROJECT CLICK CONNECTION
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "project_click"; 


$conn_project_click = new mysqli($servername, $username, $password, $database);

if ($conn_project_click->connect_error) {
    die("Connection failed: " . $conn_project_click->connect_error);
}

echo "Connected successfully";

?>
