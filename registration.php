<?php
require_once("user.class.php");
require_once("database.php");
$msg = "";
$connection = new DBconnection();

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

        function CheckCaptcha($userResponse)
        {
            $fields_string = '';
            $fields = array(
                'secret' => '6LfHvNkUAAAAANYnUO1WGAIVYHQUjs8Vh8ghX_0R',
                'response' => $userResponse
            );
            foreach ($fields as $key => $value)
                $fields_string .= $key . '=' . $value . '&';
            $fields_string = rtrim($fields_string, '&');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);

            $res = curl_exec($ch);
            curl_close($ch);

            return json_decode($res, true);
        }


        $result = CheckCaptcha($_POST['g-recaptcha-response']);

        if ($result['success']) {


            $log = strip_tags($_POST['username']);
            $mail = strip_tags($_POST['mail']);
            $pass = strip_tags($_POST['password']);
            $passVer = strip_tags($_POST['password_check']);

            if (strlen($pass) <= 4) {
                $msg = "heslo je příliš krátké, zadejte alespoň 5 znaků";
            } else if (strlen($log) <= 2) {
                $msg = "uživatelské jméno je příliš krátké, zadejte alespoň 3 znaky";
            } else {
                $user = new user($log, $mail, password_hash($pass, PASSWORD_DEFAULT));
                if ($connection->addUser($user)) {
                    header("Location: index.php?id=Registrace proběhla úspěšně!");
                }
            }
        } else {
            $msg = "Nastala chyba při rozpoznávání kontrolou reCAPTHA";
        }
    }
} catch (PDOException $exception) {
    $msg = "Chyba s databází zkuste to později";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Registrace</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>

<body>

    <div class="container mt-3">
        <form method="post">
            <h1>Registrace</h1>
            <h2><a href="index.php">Zpět</a></h2>
            <div class="form-group">
                <div class="form-group">
                    <label for="email">Uživatelské jméno:</label>
                    <input class="form-control" type="text" name="username" placeholder="Zadejte uživatelské jméno" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Mail:</label>
                    <input class="form-control" type="text" name="mail" placeholder="Zadejte vaši E-mailovou adresu" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Heslo:</label>
                    <input class="form-control" type="password" name="password" placeholder="Zadejte heslo" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Ověření hesla:</label>
                    <input class="form-control" type="password" name="password_check" placeholder="Zadejte heslo znovu" required>
                </div>
                <div class="g-recaptcha" data-sitekey="6LfHvNkUAAAAAOnJCGyO_CcsxpV6aiJ2kRhJ2nb3"></div>
                <input class="btn btn-primary" type="submit" name="send" value="Zaregistrovat se">
                <p><?php if ($msg != "") {
                        echo $msg;
                    } ?></p>
                <div class="form-group form-check">
                </div>
            </div>
        </form>
    </div>
</body>

</html>