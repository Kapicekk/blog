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
  if (isset($_GET["id"])) {
    $msg = $_GET["id"];
  }

  if (isset($_POST['send'])) {
    $log = strip_tags($_POST['username']);
    $pass = strip_tags($_POST['password']);
    $login = new Login($log, $pass);
    if ($login->login()) {
      $_SESSION["username"] = $log;
      $_SESSION["ban"] = $connection->banCheck($_SESSION["username"]);
      $msg = "přihlášení proběhlo úspěšně";
    } else {
      $msg = "Heslo nebo jméno jsou nesprávné";
    }
  }
  if (isset($_SESSION["username"])) {
    $opravneni = $connection->adminCheck($_SESSION["username"]);
  }
} catch (PDOException $exception) {
  $msg = "Chyba s databází zkuste to později" . $exception->getMessage();
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


            <?php
            if (!empty($_SESSION)) :
            ?>
              <div class="men-users">
                <a href="user.php" class="userN"><?php echo strip_tags($_SESSION["username"]); ?></a>
                <?php
                if ($opravneni == 1) :
                ?>
                  <a href="administration.php" class="navbar-brand">Administrace</a>
                <?php
                endif;
                ?>
                <a href="newPost.php" class="navbar-brand">Nový příspěvek</a>
                <?php

                ?>
                <a href="logout.php" class="navbar-brand">Odhlásit se</a>
              </div>
          </div>
        <?php
            else :
        ?>
        <?php
            endif;
        ?>
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
            <a class="navbar-brand" href="logout.php">Odhlásit se</a>
          </li>
        <?php
        endif;
        ?>
        <li class="nav-item">
          <?php
          if (empty($_SESSION)) :
          ?>
          <?php
          endif;
          ?>
        </li>
      </ul>
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
    <h2 style="padding-bottom: 25px">Kategorie</h2>
    <?php
    foreach ($connection->showKategory() as $kategory) :
    ?>
      <a href="kategory.php?id=<?php echo $kategory['id_kategory'] ?>"><button type="button" style="margin-bottom: 25px" class="btn btn-secondary"><?php echo $kategory["kategory_name"]; ?></button></a>
    <?php
    endforeach;
    ?>
  </div>

  <div class="container" style="padding-top: 50px">


        <div class="container-mid">
        <div class="post-preview">
          <h1 class="post-title">
            Co naleznete na naší stránce?
          </h1>
          <p class="info-text"><b>Vítejte na naši stránce!</b><br>Na této stránce dostanete možnost si pročítat mnoho skvělých článků, příspěvku a prohlížet mnoho fotografií, které přidávají ostatní uživatelé. Hodnoťte také ostatní příspěvky komentáři a zdvyženými palci. Navazujte nové vztahy a sdílejte své myšlenky. Vytvořte si i VY svůj článek <a href="newPost.php">zde</a>!</p>
        </div>

        <hr>

        <div class="post-preview">
          <h1 class="post-title">
            Proč se zaregistrovat?
          </h1>
          <p class="info-text">Registrací získáte spousty skvělých výhod, kterými jsou hodnocení ostatních uživatelů a komentování jejich příspěvků. Zároveň bez registrace nemůžete zakládat své příspěvky. Registrujte se <a href="registration.php">zde</a>!</p>
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