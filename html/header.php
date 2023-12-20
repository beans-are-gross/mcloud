<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require './conn.php';

if (isset($_COOKIE['pwd'])){
    $sql = "SELECT id FROM login WHERE pwd=?;";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_COOKIE['pwd']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    if(empty($id)){
        setcookie("pwd", "", time() - 3600, $path = "", $domain = "", $secure = false);
        echo "<script>alert('security failed');</script>";
        header("Location: /");
    } else {
        echo "<script>alert('security passed');</script>";
        $accountCookie = $_COOKIE['pwd'];
        $uri = $_SERVER['REQUEST_URI'];
    }
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
