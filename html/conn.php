<?php
if(!$conn = mysqli_connect("localhost", "mcloud", file_get_contents("./key/.key"), "mcloud")){
    die("Failed to connect to the database.<br>".mysqli_connect_error());
}