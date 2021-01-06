<?php
require_once("login.class.php");
$connection = new DBconnection();
$msg = "";
$username = $_SESSION["username"];

if (!empty($_SESSION)) {
    if ($_SESSION["ban"] == 1) {
        header("Location: ban.php");
    }
}

try {
    if (isset($_GET["id"])) {
        $msg = $_GET["id"];
    }
    if (isset($_POST["send"])) {
        $password = strip_tags($_POST['oldPassword']);
        if (strlen($_POST['newPassword']) <= 4) {
            header("Location: edit.profile.php?id=Heslo je příliš krátké! Zadejte alespoň 5 znaků.");
        } else {
            $pass = $connection->passwordCheck($username);
            if (password_verify($password, $pass)) {
                if ($_POST['newPassword'] == $_POST['newPasswordAgain']) {
                    $passHash = password_hash($_POST["newPassword"], PASSWORD_DEFAULT);
                    $change = $connection->changePassword($passHash, $username);
                    $msg = "heslo bylo úspěšně změněno";
                } else {
                    $msg = "nová hesla se neschodují";
                }
            } else {
                $msg = "zadané heslo je špatně";
            }
        }
    }
} catch (PDOException $exception) {
    $msg = "Chyba s databází zkuste to později " . $exception->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Změna hesla</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>

<body>

    <div class="container mt-3">
        <form method="post">
            <h1>Změna hesla</h1>
            <h2><a href="user.php">Zpět</a></h2>
            <div class="form-group">
                <div class="form-group">
                    <label for="email">Staré heslo:</label>
                    <input class="form-control" type="password" name="oldPassword" placeholder="zadejte staré heslo" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Nové heslo:</label>
                    <input class="form-control" type="password" name="newPassword" placeholder="zadejte nové heslo" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Ověření nového hesla:</label>
                    <input class="form-control" type="password" name="newPasswordAgain" placeholder="zadejte nové heslo znovu" required>
                </div>
                <input class="btn btn-primary" type="submit" name="send" value="Změnit heslo">
                <p><?php if ($msg != "") {
                        echo $msg;
                    } ?></p>
                <div class="form-group form-check">
                </div>
            </div>
        </form>
    </div>
</body>