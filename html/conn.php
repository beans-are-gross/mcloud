<?php
$conn = mysqli_connect("localhost", "mcloud", file_get_contents("/mcloud.key"), "mcloud");

if(mysqli_connect_errno()){
    die("Failed to connect to the database.<br>".mysqli_connect_error());
}