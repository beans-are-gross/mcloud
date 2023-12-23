<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | mCloud</title>
</head>

<body>
    <div id="center">
        <form method="post" id="login">
            <h1>Login</h1>
            <?php
            if (isset($_POST['login-submit'])) {
                if (empty($_POST['uid']) || empty($_POST['pwd'])) {
                    displayError("Some required feilds are missing.");
                } else {
                    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
                    $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);

                    $sql = "SELECT id, pwd FROM login WHERE uid=?;";
                    $stmt = mysqli_stmt_init($conn);
                    mysqli_stmt_prepare($stmt, $sql);
                    mysqli_stmt_bind_param($stmt, "s", $uid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $idSql, $pwdSql);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (!password_verify($pwd, $pwdSql)) {
                            displayError("Incorrect username or password.");
                        } else {
                            $characters = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
                            $letterIndex = 0;
                            $newCookie = "";
                            while($letterIndex < 17){
                                $newCookie .= strval($characters[array_rand($characters)]);
                                $letterIndex ++;
                            }
                            if (!setcookie("userId", $newCookie, $expires_or_options = 0, $path = "", $domain = "", $secure = false)) {
                                displayError("Failed to set session cookie. (500)");
                                exit;
                            } else {
                                mysqli_stmt_close($stmt);
                                $sql = "UPDATE login SET cookie=? WHERE id=?;";
                                mysqli_stmt_prepare($stmt, $sql);
                                mysqli_stmt_bind_param($stmt, "s", $newCookie, $idSql);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_close($stmt);
                                header("Location: /");
                            }
                        }
                    } else {
                        displayError("Incorrect username or password.");
                    }
                }
            }
            ?>
            <div id="login-inputs">
                <input type="text" name="uid" id="uid" placeholder="Username" required>
                <br>
                <input type="password" name="pwd" id="pwd" placeholder="Password" required>
            </div>
            <input type="hidden" name="login-submit">
            <br>
            <div id="center">
                <button type="submit">
                    <span class="material-symbols-rounded">
                        lock_open
                    </span>
                </button>
            </div>
        </form>
    </div>

    <script>
        if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)) {
            document.getElementById("uid").style.fontSize = '1em';
            document.getElementById("pwd").style.fontSize = '1em';
        }
    </script>
</body>

</html>