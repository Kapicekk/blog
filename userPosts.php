<?php
require_once("login.class.php");
$connection = new DBconnection();
$user_id = $connection->showUserID($_SESSION["username"]);
$opravneni = $connection->adminCheck($_SESSION["username"]);

if (!empty($_SESSION)) {
  if ($_SESSION["ban"] == 1) {
      header("Location: ban.php");
  }
}

try{  
    if(empty($_SESSION["username"])){
        header("Location: index.php"); 
    }
    $err_msg = "";  
    }catch(PDOException $exception){
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
                        if(!empty($_SESSION)):
                    ?>
        <a class="navbar-brand" href="user.php"><?php echo strip_tags($_SESSION["username"]);?></a>
                    <?php
                        endif;
                    ?>

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
          </div>
        </div>
      </div>
    </div>
  </header>


  <div class="container" style="text-align: center;">
  <a href="user.php" class="btn btn-secondary">Zpět</a>
</div>

  <div class="container" style="padding-top: 50px">
    <div class="row">
      <div class="col-lg-8 col-md-10 mx-auto">


      <?php foreach($connection->showUserPost($user_id) as $post): ?>
      <div class="post-preview">
          <a href="post.html">
            <h2 class="post-title">
                <a class="post-title" href="post.php?postId=<?php echo $post['id_post']?>"><h1><?php echo $post["title"] ?></h1></a>
            </h2>
            <p class="post-title">
                <?php echo $post["content"] ?>
            </p>
          </a>

                <div>      
                <div class="row text-center text-lg-left">
                        <?php foreach($connection->showImages($post["id_post"]) as $image): ?>
                        <div class="col-lg-5">    
                            <a href="Images/<?php echo $image["image_name"]?>" class="d-block mb-4 h-100">                           
                                    <img class="img-fluid img-thumbnail" src="Images/<?php echo $image["image_name"]?>">                             
                            </a>
                        </div>
                        <?php endforeach; ?>    
                </div>     

                </div>

          <p class="post-meta">
          <?php echo $connection->convertId($post["id_creator"]); ?> / <?php echo $connection->showUserKategory($post["id_kategory"])[0]; ?></b>
          </p>

          <?php if(!empty($_SESSION)): ?>                       
                            <?php if($opravneni == 1 || $_SESSION["username"] == $connection->convertId($post["id_creator"])): ?>                      
                <a class="btn btn-danger" href="deletePost.php?id=<?php echo $post['id_post']?>&id2=<?php echo $post['id_kategory']?>&id3=<?php echo $post['id_creator']?>">Smazat</a>
                <a class="btn btn-warning" href="editPost.php?id=<?php echo $post['id_post']?>">Edit</a>                           
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

