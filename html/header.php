<?php
require './conn.php';

$internalDir = mysqli_real_escape_string($conn, $_GET['dir']);
$accountCookie = $_COOKIE['pwd'];
$uri = $_SERVER['REQUEST_URI'];

if (!isset($_COOKIE['pwd']) && $uri !== "/index.php") {
    header("Location: /index.php");
}
function displayError($error)
{
    echo "<p style='color: red;'>$error</p>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mCloud</title>
    <title>Drive info | mCloud</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <header>
        <div id="center">
            <span class="material-symbols-rounded">
                cloud
            </span>
            <h3>mCloud</h3>
        </div>
    </header>
</body>

</html>