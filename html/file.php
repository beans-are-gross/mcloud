<?php
require "./conn.php";

$sql = "SELECT id FROM login WHERE cookie=?;";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "s", $_COOKIE['userId']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $userId);
if(!mysqli_stmt_fetch($stmt)){
    http_response_code(403);
    die("Error: Cookie unauthorized");
}
mysqli_stmt_close($stmt);

$sql = "SELECT name, externalDir, type FROM files WHERE userId=? AND id=?;";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "ss", $userId, $_GET['id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name, $externalDir, $type);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if(empty($externalDir)){
    http_response_code(404);
    die("Error: File not found");
} else {
    header("Content-type: " . $type);
    $fileExt = end(explode(".", $externalDir));
    header("Content-Disposition: inline;filename=$name.$fileExt");
    if(!readfile($externalDir)){
        http_response_code(404);
        die("Error: File lost");
    }
}