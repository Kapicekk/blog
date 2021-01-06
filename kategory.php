<?php
require_once("login.class.php");
$connection = new DBconnection();
$msg = "";

if (!empty($_SESSION)) {
  if ($_SESSION["ban"] == 1) {
      header("Location: ban.php");
  }
}
try {
  $err_msg = "";
  if (isset($_POST['send'])) {
    $log = $_POST['username'];
    $pass = $_POST['password'];
    $login = new Login($log, $pass);
    if ($login->login()) {
      $_SESSION["username"] = $log;
      $_SESSION["ban"] = $connection->banCheck($_SESSION["username"]);
    } else {
      $err_msg = "<p class='err'>Heslo nebo jméno jsou nesprávné</p>";
    }
  }
  if (isset($_SESSION["username"])) {
    $opravneni = $connection->adminCheck($_SESSION["username"]);
  }
} catch (PDOException $exception) {
  echo "<p class='err'>Chyba s databází zkuste to později</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Blog</title>

  <link href="Styles/bootstrap.min.css" rel="stylesheet">
  <link href="Styles/style.css" rel="stylesheet">

</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
      <?php
      if (!empty($_SESSION)) :
      ?>
        <a class="navbar-brand" href="user.php"><?php echo strip_tags($_SESSION["username"]); ?></a>
      <?php
      endif;
      ?>

      <?php
      if (empty($_SESSION)) :
      ?>
        <div class="navbar-brand-log">
          <a class="navbar-brand" onclick="document.getElementById('trigger').style.display='block'">Přihlásit se</a>
        </div>
      <?php
      endif;
      ?>

      <div id="trigger" class="menu">
        <form class="form-menu" method="post">
          <div class="container2">
            <p class="form-p">Zadejte uživatelské jméno</p>
            <input type="text" placeholder="Uživatelské jméno" name="username" required>
            <p class="form-p">Zadejte heslo</p>
            <input type="password" placeholder="Zadejte heslo" name="password" required>
            <button class="confirm-button" name="send" type="submit">Přihlásit se</button>

            <a class="form-p" href="registration.php">Ještě nemáte účet?</a>

            <script>
              var menu = document.getElementById('trigger');

              window.onclick = function(event) {
                if (event.target == menu) {
                  menu.style.display = "none";
                }
              }
            </script>

              </div>
          </div>
      </div>
      </form>
    </div>
    <div>
      <ul class="navbar-nav ml-auto">
        <?php
        if (!empty($_SESSION)) :
          if ($opravneni == 1) :
        ?>
            <li class="nav-item">
              <a class="navbar-brand" href="administration.php" class="logout">Administrace</a>
            </li>
          <?php
          endif;
          ?>
          <li class="nav-item">
            <a class="navbar-brand" href="newPost.php">Nový příspěvek</a>
          </li>
          <li class="nav-item">
            <a class="navbar-brand" href="index.php">Hlavní stránka</a>
          </li>
          <li class="nav-item">
            <a class="navbar-brand" href="logout.php">Odhlásit se</a>
          </li>
        <?php
        endif;
        ?>
        <li class="nav-item">
          <?php
          if (empty($_SESSION)) :
          ?>
            <a class="navbar-brand" href="index.php">Zpět</a>
          <?php
          endif;
          ?>
        </li>
      </ul>
    </div>
    </div>
  </nav>

  <header class="masthead" style="background-image: url('Other/knihovna.jpg')">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="site-heading">
            <h1>Blog</h1>
            <h3 class="navbar-brand"><?php if ($msg != "") {
                                        echo $msg;
                                      } ?></h3>
          </div>
        </div>
      </div>
    </div>
  </header>


  <div class="container" style="text-align: center;">
        <?php if(!empty($connection->showSingleKategory($_GET["id"]))): ?>
          <h2 style="padding-bottom: 25px"><?php echo $connection->showSingleKategory($_GET["id"]);?></h2>
        <?php else: ?>
          <h2 style="padding-bottom: 25px">Kategorie neexistuje</h2>
        <?php endif; ?>  
    <?php
    foreach ($connection->showKategory() as $kategory) :
    ?>
      <a href="kategory.php?id=<?php echo $kategory['id_kategory'] ?>"><button type="button" style="margin-bottom: 25px" class="btn btn-secondary"><?php echo $kategory["kategory_name"]; ?></button></a>
    <?php
    endforeach;
    ?>
  </div>

  <div class="container" style="padding-top: 50px">
    <div class="row">
      <div class="col-lg-8 col-md-10 mx-auto">

      


        <?php foreach ($connection->showPost($_GET["id"]) as $post) : ?>

          <div class="post-preview">
              <h2 class="post-title">
                <a class="post-title" href="post.php?kategId=<?php echo $_GET["id"]?>&postId=<?php echo $post['id_post'] ?>">
                  <h1><?php echo $post["title"] ?></h1>
                </a>
              </h2>
              <div class="postContent">
                <p class="post-title">
                    <?php echo $post["content"] ?>               
                </p>
              </div>

            <div>
              <div class="row text-center text-lg-left">
                <?php foreach ($connection->showImages($post["id_post"]) as $image) : ?>
                  <div class="col-lg-5">
                    <a href="Images/<?php echo $image["image_name"] ?>" class="d-block mb-4 h-100">
                      <img class="img-fluid img-thumbnail" src="Images/<?php echo $image["image_name"] ?>">
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>

            </div>

            <p class="post-meta">
              <?php if ($connection->convertId($post["id_creator"]) != null) : ?>
                Vytvořeno uživatelem <b><?php echo $connection->convertId($post["id_creator"]); ?></b>
              <?php else : ?>
                Uživatel, který tento příspěvek napsal byl smazán
              <?php endif; ?>
            </p>
            <?php if (!empty($_SESSION)) : ?>
              <?php if ($opravneni == 1 || $_SESSION["username"] == $connection->convertId($post["id_creator"])) : ?>
                <a class="btn btn-danger" href="deletePost.php?id=<?php echo $post['id_post'] ?>&id2=<?php echo $post['id_kategory'] ?>&id3=<?php echo $post['id_creator'] ?>">Smazat</a>
                <a class="btn btn-warning" href="editPost.php?id=<?php echo $post['id_post'] ?>">Edit</a>
              <?php endif; ?>
            <?php endif; ?>

          </div>
          <hr>
        <?php endforeach; ?>

      </div>
    </div>
  </div>
  <hr>

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
        <p class="copyright text-muted">Vytvořeno v roce 2020 <br>Vedoucí maturitní práce: RNDr. Jana Reslová</p>
        </div>
      </div>
    </div>
  </footer>

</body>

</html>