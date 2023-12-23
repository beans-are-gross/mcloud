<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require './conn.php';

if (isset($_COOKIE['userId'])) {
    $sql = "SELECT id FROM login WHERE cookie=?;";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_COOKIE['userId']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (empty($userId)) {
        setcookie("userId", "", time() - 3600, $path = "", $domain = "", $secure = false);
        header("Location: /?bad-cookie");
    } else {
        $userId = $_COOKIE['userId'];
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
            <select id="mode">
                <option value="light" id="light">Light</option>
                <option value="dark" id="dark">Dark</option>
            </select>
            <script>
                function lightMode() {
                    document.body.style.backgroundColor = "#F8F8FF";
                    document.body.style.color = "black";

                    localStorage.setItem("mode", "light");

                    document.getElementById("light").selected = true;
                }

                function darkMode() {
                    document.body.style.backgroundColor = "#343434";
                    document.body.style.color = "white";

                    localStorage.setItem("mode", "dark");

                    document.getElementById("dark").selected = true;
                }

                var color = localStorage.getItem("mode");

                if (color == "light") {
                    lightMode();
                } else if (color == "dark") {
                    darkMode();
                }

                const mode = document.getElementById("mode");
                mode.addEventListener("change", function () {
                    var color = mode.value;

                    console.info("Changing theme to: " + color);

                    if (color == "light") {
                        lightMode();
                    } else if (color == "dark") {
                        darkMode();
                    }
                });
            </script>
        </div>
    </header>
</body>

</html>