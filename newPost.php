<?php
require_once("database.php");
require_once("post.class.php");
$connection = new DBconnection();
$username = $_SESSION["username"];
$msg = "";

if (!empty($_SESSION)) {
    if ($_SESSION["ban"] == 1) {
        header("Location: ban.php");
    }
}

try {

    if (empty($_SESSION["username"])) {
        header("Location: registration.php");
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

        if (/*$result['success']*/1==1) {


            if(strlen($_POST["content"]) >= 3000){
                header("Location: newPost.php?msg=Příspěvek přesáhl hranici 3000 znaků");
            }
            elseif(strlen($_POST["title"]) > 40){
                header("Location: newPost.php?msg=Nadpis přesáhl hranici 40 znaků");
            }
            else{

            if (empty($_FILES['img']['tmp_name']) || !is_uploaded_file($_FILES['img']['tmp_name'][0])) {
                $post = new post($connection->showUserID($username), strip_tags($_POST['kateg']), strip_tags($_POST['title']), strip_tags($_POST['content']));
                if ($connection->addPost($post)) {
                    header("Location: newPost.php?msg=Příspěvek byl úspěšně přidán!");
                } else {
                    header("Location: newPost.php?msg=Nastala chyba při přidávání");
                }
            } else {
                $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "JPG" => "image/JPG", "PNG" => "image/PNG", "JPEG" => "image/JPEG");
                $images = [];
                for ($i = max(array_keys($_FILES["img"]["name"])); $i >= 0; $i--) {
                    $filename = uniqid('', true) . "." . $_FILES["img"]["name"][$i];
                    $images[] = $filename;
                    $filetype = $_FILES["img"]["type"][$i];
                    $filesize = $_FILES['img']['size'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!array_key_exists($ext, $allowed)) {
                        header("Location: newPost.php?msg=Obrázek není v povoleném formátu");
                    }else if($filesize[0] > 1000000){
                        header("Location: newPost.php?msg=Obrázek je příliš veliký. Maximální povolená velikost obrázku je 1MB");
                    } else {
                        $stripIMG = strip_tags($_FILES["img"]["tmp_name"][$i]);
                        move_uploaded_file($stripIMG, "./Images/" . $filename);
                        $post = new post($connection->showUserID($username), strip_tags($_POST['kateg']), strip_tags($_POST['title']), strip_tags($_POST['content']));
                    }
                }            

                        if ($connection->addPost($post)) {
                            $id = $connection->lastInsertID();  
                            header("Location: newPost.php?msg=Příspěvek byl úspěšně přidán!");
                            foreach ($images as $image) {
                                $connection->insertImages($image, $id);
                            }
                        } else {
                            header("Location: newPost.php?msg=Nastala chyba při přidávání");
                        }

            }
        }
        } else {
            header("Location: newPost.php?msg=Nastala chyba při rozpoznávání kontrolou reCAPTHA");
        }
    }
} catch (PDOException $exception) {
    $msg = "Chyba s databází, zkuste to později";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Přidat příspěvek</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>

<body>

    <div class="container mt-3">

        <form method="post" enctype="multipart/form-data">

            <h1>Nový příspěvek</h1>
            <h2><a href="index.php">Zpět</a></h2>
            <div class="form-group">

                <div>
                    <label for="email">Vyberte kategorii:</label>


                    <select class="custom-select my-1 mr-sm-2" name="kateg" id="" placeholder="kategorie">
                        <?php
                        foreach ($connection->showKategory() as $kategory) :
                        ?>
                            <option value="<?php echo $kategory["id_kategory"] ?>"><?php echo $kategory["kategory_name"] ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>

                    <b></b>

                </div>
                <div class="form-group">
                    <label for="email">Zadejte nadpis:</label>
                    <input class="form-control" type="text" name="title" placeholder="Zadejte nadpis (maximálně 40 znaků)" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Obsah:</label>
                    <textarea class="form-control" type="text" name="content" placeholder="Obsah příspěvku" style="height: 175px;" required></textarea>
                </div>

                <div>
                    <label for="pwd">Vyberte obrázky:</label>
                    <input type="file" name="img[]" multiple>
                </div>
                <div class="g-recaptcha" data-sitekey="6LfHvNkUAAAAAOnJCGyO_CcsxpV6aiJ2kRhJ2nb3"></div>
                <input class="btn btn-primary" style="margin-top: 25px;" type="submit" name="send" value="Přidat příspěvek">
                <div class="form-group form-check">
                </div>
                <?php if (!empty($_GET["msg"])) {
                    echo $_GET["msg"];
                } ?>
            </div>
        </form>
    </div>
</body>

</html>