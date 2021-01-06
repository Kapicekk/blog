<?php
require_once("database.php");
require_once("post.class.php");
$connection = new DBconnection();
$msg = "";

if (!empty($_SESSION)) {
    if ($_SESSION["ban"] == 1) {
        header("Location: ban.php");
    }
}


foreach ($connection->editPost($_GET["id"]) as $post) {
    $posts = $post;
}

$posts = new post($posts['id_creator'], $posts['id_kategory'], $posts['title'], $posts['content']);

if ($connection->showUserId($_SESSION["username"]) != $posts->id_creator) {
    if ($connection->adminCheck($_SESSION["username"]) != 1) {
        header("Location: index.php");
    }
}

if (isset($_POST["send"])) {
    $newPost = new post("", "", strip_tags($_POST["title"]), strip_tags($_POST["content"]));
    $connection->updatePost($_GET["id"], $newPost);
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Přidat příspěvek</title>
    <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>

<body>

    <div class="container mt-3">

        <form method="post" enctype="multipart/form-data">

        <?php if(empty($posts->title)){
            header("Location: index.php");
             }; ?>
        
            <h1>Nový příspěvek</h1>
            <h2><a href="index.php">Zpět</a></h2>
            <div class="form-group">
                <div class="form-group">
                    <label for="email">Upravte nadpis:</label>
                    <input class="form-control" type="text" name="title" placeholder="Upravte nadpis" required value="<?php echo "$posts->title"; ?>">
                </div>
                <div class="form-group">
                    <label for="pwd">Upravte obsah:</label>
                    <textarea class="form-control" type="text" name="content" placeholder="Upravte obsah příspěvku" style="height: 175px;" required><?php echo $posts->content; ?></textarea>
                </div>

                <input class="btn btn-primary" style="margin-top: 25px;" type="submit" name="send" value="Upravit příspěvek">
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