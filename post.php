<?php
require_once("database.php");
require_once("comment.class.php");
require_once("login.class.php");
$connection = new DBconnection();
$msg="";

if (!empty($_SESSION)) {
  if ($_SESSION["ban"] == 1) {
      header("Location: ban.php");
  }
}

if (empty($_GET["postId"])){
  header("Location: index.php");
}

try{
    if(isset($_GET["id"])){
        $msg = $_GET["id"];
    }

    $msg = "";  
    if(isset($_POST['send'])){
        $log = $_POST['username'];
        $pass = $_POST['password'];
        $login = new Login($log, $pass);
			if($login->login()){
                $_SESSION["username"] = $log;
                $_SESSION["ban"] = $connection->banCheck($_SESSION["username"]);
            }
            else{
                $msg = "Heslo nebo jméno jsou nesprávné";
            }
    }

    if(isset($_POST["send2"])){
        $com = strip_tags($_POST['comment']);
        $user_id = $connection->showUserID($_SESSION["username"]);
        $id_post = $_GET["postId"];
        $comment = new comment($user_id, $id_post, $com);
        if($connection->addComment($comment)) {
            header("Location: post.php?postId=$id_post");
        }
    }

    if(isset($_SESSION["username"])){
        $opravneni = $connection->adminCheck($_SESSION["username"]);
        }
    }catch(PDOException $exception){
		echo "Chyba s databází zkuste to později";
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
  <script src="https://kit.fontawesome.com/4524a4a284.js" crossorigin="anonymous"></script>

</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
                    <?php 
                        if(!empty($_SESSION)):
                    ?>
        <a class="navbar-brand" href="user.php"><?php echo strip_tags($_SESSION["username"]);?></a>
                    <?php
                        endif;
                    ?>

                    <?php 
                        if(empty($_SESSION)):
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
                        if(!empty($_SESSION)):
                        if($opravneni == 1):
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
                        if(empty($_SESSION)):
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
            <h3 class="navbar-brand"><?php if($msg != ""){echo $msg;} ?></h3>
          </div>
        </div>
      </div>
    </div>
  </header>


  <div class="container" style="text-align: center;">
          <?php if(isset($_GET["kategId"])): ?>
      <a href="kategory.php?id=<?php echo $_GET["kategId"]?>"><button type="button" style="margin-bottom: 25px" class="btn btn-secondary">Zpět</button></a> 
          <?php else: ?>
      <a href="userPosts.php"><button type="button" style="margin-bottom: 25px" class="btn btn-secondary">Zpět</button></a> 
          <?php endif; ?>
  </div>

  <div class="container" style="padding-top: 50px">
    <div class="row">
      <div class="col-lg-8 col-md-10 mx-auto">


      <?php foreach($connection->showOnePost($_GET["postId"]) as $post): ?>
      <div class="post-preview">

            <h2>
                <h1><?php echo $post["title"] ?></h1>
            </h2>
            <h4>
                <?php $rate = $connection->showLikes($post["id_post"]) ?>
                <b><?php echo $rate[0]?></b>  <i class="far fa-thumbs-up"></i>
            </h4>
            <p>
                <div class="postContent">
                <?php echo $post["content"] ?>
                </div>
            </p>


                <div>      
                <div class="row text-center text-lg-left">
                        <?php foreach($connection->showImages($post["id_post"]) as $image): ?>
                        <div class="col-lg-6 col-md-7 col-9">    
                            <a href="Images/<?php echo $image["image_name"]?>" class="d-block mb-4 h-100">                           
                                    <img class="img-fluid img-thumbnail" src="Images/<?php echo $image["image_name"]?>">                             
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

                        <div class="buttons-post"> 
          <?php if(!empty($_SESSION)): ?>    
            <?php if(empty($connection->showRate($post["id_post"], $connection->showUserID($_SESSION["username"])))): ?>     
                <a class="btn btn-primary" href="ratePost.php?id=<?php echo $post["id_post"] ?>&id2=<?php echo $connection->showUserID($_SESSION["username"])?>">To se mi líbí</a> 
            <?php else: ?>
                <a class="btn btn-success" href="ratePost.php?id=<?php echo $post["id_post"] ?>&id2=<?php echo $connection->showUserID($_SESSION["username"])?>">Už se mi to nelíbí</a> 
            <?php endif; ?>              
                            <?php if($opravneni == 1 || $_SESSION["username"] == $connection->convertId($post["id_creator"])): ?>                      
                <a class="btn btn-danger" href="deletePost.php?id=<?php echo $post['id_post']?>&id2=<?php echo $post['id_kategory']?>&id3=<?php echo $post['id_creator']?>">Smazat</a>
                <a class="btn btn-warning" href="editPost.php?id=<?php echo $post['id_post']?>">Edit</a>                           
                            <?php endif; ?>                            
                        <?php endif; ?>
                        </div>  
        </div>


        <?php if(!empty($_SESSION)): ?>

                            <form method="post">
                                <div class="container">
                                    <div class="form-group">
                                        <textarea class="form-control" name="comment" placeholder="Napiště komentář k tomuto příspěvku" style="height: 85px;" required></textarea>
                                    </div>
                                    <div class="submitB">
                                        <input type="submit" class="btn btn-secondary" name="send2" value="Okomentovat" style="margin-top: 10px;">
                                    </div>
                                </div>
                            </form>
                                <?php endif; ?>
        <hr>
        <?php endforeach; ?>
            <div class="site-heading">
                <h2 class="text-center">Komentáře</h2>
            </div>

            <hr>

    <?php foreach($connection->showComment($_GET["postId"]) as $comment): ?>
        <div class="row text-center text-lg-left">
        <div class="container">
            <div class="form-group">
                    <h2><?php echo $connection->convertId($comment["id_user"]); ?></h2>
                    <div class="postContent">
                <article><?php echo $comment["comment"] ?></article>
                </div>
            </div>
        </div> 
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
          <p class="copyright text-muted">Vytvořeno v roce 2020</p>
        </div>
      </div>
    </div>
  </footer>

</body>

</html>

