<?php
$host = "127.0.0.1";
$username = "mcloud";
$password = trim(file_get_contents("/mcloud.key"), " ");
$database = "mcloud";

$conn = mysqli_connect($host, $username, $password, $database);

if(!$conn){
    die("Failed to connect to the database. More info: " . mysqli_connect_error());
}