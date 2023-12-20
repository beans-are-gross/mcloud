<?php
$sql = "SELECT externalDir, type FROM files WHERE accountCookie=? AND id=?;";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "ss", $_COOKIE['pwd'], $_GET['id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $externalDir, $type);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if(empty($externalDir)){
    echo "File not found";
    http_response_code(404);
} else {
    header("Content-type: " . $type);
    readfile($externalDir);
}