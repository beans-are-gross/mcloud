<?php
require './header.php';

if (isset($_POST['login-button']) || isset($_POST['login-submit'])) {
    require './login.php';
    exit;
} else if (!isset($_COOKIE['pwd'])) {
    require './home.php';
    exit;
}

$internalDir = mysqli_real_escape_string($conn, $_GET['dir']);

if (empty($internalDir)) {
    header("Location: /?dir=/");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mCloud</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <div id="non-mobile">
        <div id="drives">
            <?php
            include "./drives/linux.php";
            ?>
        </div>
        <div id="divider"></div>
    </div>

    <div id="center">
        <form id="file-upload-form" method='post' enctype="multipart/form-data">
            <input type="file" id="file-upload" name="file-upload" hidden />
            <input type="hidden" name="file-submit">
            <div id="upload-buttons">
                <button type="button" onclick="document.getElementById('file-upload').click();">
                    <span class="material-symbols-rounded">cloud_upload</span>
                </button>
            </div>
            <?php
            if (isset($_POST['file-submit'])) {
                $file = $_FILES['file-upload'];

                if ($file == null) {
                    displayError("No file found. Please try again");
                } else {
                    $fileName = mysqli_real_escape_string($conn, $file['name']);
                    $fileTmp = $file['tmp_name'];
                    $fileError = $file['error'];
                    $fileType = $file['type'];
                    $fileIcon = explode("/", $fileType)[0];

                    $fileNameExploded = explode(".", $fileName);
                    $fileExt = strtolower(end($fileNameExploded));

                    if ($fileIcon == "text") {
                        $icon = "description";
                    } else if ($fileIcon == "image") {
                        $icon = "image";
                    } else if ($fileIcon == "video") {
                        $icon = "movie";
                    } else if ($fileIcon == "audio") {
                        $icon = "music_note";
                    } else if ($fileExt == "pdf") {
                        $icon = "picture_as_pdf";
                    } else if ($fileExt == "exe") {
                        $icon = "desktop_windows";
                    } else {
                        $icon = "unknown_document";
                    }

                    if (!$fileError === 1) {
                        displayError("An unknown error occured while uploading your file.");
                    } else {
                        $internalFileName = uniqid("", true) . ".$fileExt";
                        $fileDestination = "/mcloud/uploads/$internalFileName";
                        if(!move_uploaded_file($fileTmp, $fileDestination)){
                            displayError("Failed to transfer the file.<br>This error is common with Firefox.");
                            exit;
                        }
                        $sql = "INSERT INTO files (name, internalDir, externalDir, type, icon, accountCookie) VALUES(?, ?, ?, ?, ?, ?);";
                        $stmt = mysqli_stmt_init($conn);
                        mysqli_stmt_prepare($stmt, $sql);
                        mysqli_stmt_bind_param($stmt, "ssssss", $fileName, $internalDir, $fileDestination, $fileType, $icon, $accountCookie);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                }
            }
            ?>
            <script>
                document.getElementById("file-upload").addEventListener("change", function () {
                    if (document.getElementById("file-upload").value) {
                        document.getElementById("file-upload-form").submit();
                    }
                });
            </script>
        </form>
    </div>

    <div id="center">
        <div id="files-list">
            <form method="get">
                <?php
                $sql = "SELECT id, name, externalDir, dateAdded, icon FROM files WHERE accountCookie=? AND internalDir=? ORDER BY dateAdded DESC;";
                $stmt = mysqli_stmt_init($conn);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $accountCookie, $internalDir);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $fileId, $fileName, $fileExternalDir, $fileDateAdded, $icon);
                while (mysqli_stmt_fetch($stmt)) {
                    echo "<button name='view' value='$fileId' id='file'><span class='material-symbols-rounded'>$icon</span>$fileName</button>";
                }
                mysqli_stmt_close($stmt);
                $sql = "SELECT COUNT(name) FROM files WHERE accountCookie=? AND internalDir=?;";
                $stmt = mysqli_stmt_init($conn);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $accountCookie, $internalDir);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $totalFiles);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
                echo "
                <p>$totalFiles files</p>
                <input type='hidden' name='dir' value='$internalDir'>
                ";
                ?>
            </form>
        </div>
    </div>

    <?php
    function fileNotFound()
    {
        echo "
            <div id='view-file-popup'>
                <header>
                    <div id='view-file'>
                        <button type='button' name='back' onclick='window.location.href=`index.php`;'>
                            <span class='material-symbols-rounded' style='font-size: 40px;'>
                                arrow_back
                            </span>
                        </button>
                        <h1>Error</h1>
                    </div>
                </header>
                <div id='center'>
                    <div style='text-align: center; color: white;'>
                        <span class='material-symbols-rounded' style='font-size: 500px;'>
                            error
                        </span>
                        <h1>This file does not exist (404)</h1>
                    </div>
                </div>
            </div>
            ";
    }

    function showFile($fileName, $fileId){
        echo "
            <div id='view-file-popup'>
                <header>
                    <div id='view-file'>
                        <button type='button' name='back' onclick='window.location.href=`index.php`;'>
                            <span class='material-symbols-rounded' style='font-size: 40px;'>
                                arrow_back
                            </span>
                        </button>
                        <form method='post'>
                            <div id='center'>
                                <h1>$fileName</h1>
                                <button name='edit-file-name' value='$fileId'>
                                    <span class='material-symbols-rounded'>
                                        edit
                                    </span>
                                </button>
                            </div>
                        </form>
                        <form method='post'>                            
                            <button type='button' onclick='window.open(`/file.php?id=$fileId`);'>
                                <span class='material-symbols-rounded' style='font-size: 40px;'>
                                    download
                                </span>
                            </button>
                            <button type='submit' name='deleteFile' value='$fileId'>
                                <span class='material-symbols-rounded' style='font-size: 40px;'>
                                    delete
                                </span>
                            </button>
                        </form>
                    </div>
                </header>
                <div id='center'>
                    <embed src='/file.php?id=$fileId' id='view-file-embed' style='width: 90vw; height: 80vh;'>
                </div>
            </div>
            ";
    }

    if (isset($_POST['deleteFile'])) {
        $sql = "SELECT externalDir FROM files WHERE accountCookie=? AND id=?;";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $accountCookie, $_GET['view']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $externalDir);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM files WHERE accountCookie=? AND id=?;";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $accountCookie, $_GET['view']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        unlink($externalDir);

        echo "<script>location.href='index.php';</script>";
    } else if (isset($_POST['edit-file-name'])) {
        $sql = "SELECT id, name, externalDir FROM files WHERE accountCookie=? AND id=?;";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $accountCookie, $_GET['view']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $fileId, $fileName, $externalDir);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        if (empty($externalDir)) {
            fileNotFound();
        } else {
            echo "
            <div id='view-file-popup'>
                <header>
                    <div id='view-file'>
                        <button type='button' name='back' onclick='window.location.href=`index.php`;'>
                            <span class='material-symbols-rounded' style='font-size: 40px;'>
                                arrow_back
                            </span>
                        </button>
                        <form method='post'>
                            <div id='center'>
                                <h2><input name='new-file-name' value='$fileName'></h2>
                                <button name='submit-edit-file-name' value='$fileId'>
                                    <span class='material-symbols-rounded'>
                                        edit
                                    </span>
                                </button>
                            </div>
                        </form>
                        <form method='post'>
                            <button type='submit' name='deleteFile' value='$fileId'>
                                <span class='material-symbols-rounded' style='font-size: 40px;'>
                                    delete
                                </span>
                            </button>
                        </form>
                    </div>
                </header>
                <div id='center'>
                    <embed src='/file.php?id=$fileId' id='view-file-embed' style='width: 90vw; height: 80vh;'>
                </div>
            </div>
            ";
        }
    } else if (isset($_POST['submit-edit-file-name'])) {
        $sql = "UPDATE files SET name=? WHERE accountCookie=? AND id=?;";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $_POST['new-file-name'], $accountCookie, $_POST['submit-edit-file-name']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<script>location.href='index.php';</script>";
    } else if (isset($_GET['view'])) {
        $sql = "SELECT id, name, externalDir FROM files WHERE accountCookie=? AND id=?;";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $accountCookie, $_GET['view']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $fileId, $fileName, $externalDir);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        if (empty($externalDir)) {
            fileNotFound();
        } else {
            showFile($fileName, $fileId);
        }
    }
    ?>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)) {
            document.getElementById("non-mobile").innerHTML = "";
            input.style.fontSize = '1em';
        }
    </script>

</body>

</html>