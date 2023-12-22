<?php
require "./conn.php";
$sql = "SELECT name, externalDir, type FROM files WHERE accountCookie=? AND id=?;";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "ss", $_COOKIE['pwd'], $_GET['id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name, $externalDir, $type);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if(empty($externalDir)){
    echo "Error: File not found";
    http_response_code(404);
} else {
    header("Content-type: " . $type);
    $fileExt = end(explode(".", $externalDir));
    header("Content-Disposition: inline;filename=$name.$fileExt");
    if(!readfile($externalDir)){
        echo "Error: File not found";
    }
}