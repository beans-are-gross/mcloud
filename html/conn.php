<?php
$conn = mysqli_connect("127.0.0.1", "mcloud", file_get_contents("/mcloud.key"), "mcloud");

if(mysqli_connect_errno()){
    die("Failed to connect to the database.<br>".mysqli_connect_error());
}