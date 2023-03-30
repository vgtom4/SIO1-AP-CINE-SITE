<?php session_start(); ?> 
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/media/logo.png">
    <title><?php echo isset($_POST["titre"]) ? urldecode($_POST["titre"]) : pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) ?></title>
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap theme -->
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
    
    <link rel="stylesheet" href="assets/style/style.css">
    <link href="https://fonts.googleapis.com/css?family=Proxima+Nova:400,700&display=swap" rel="stylesheet">
	<!-- Bootstrap core JavaScript -->
    <!-- If placed at the end of the document, the pages load faster -->
    <script src="bootstrap/jquery/jquery-3.3.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </head>
  <body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
  <a class="navbar-brand" href="home.php"><img src="assets/media/logo.png" width="100"/></a>


  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">

      <li class="nav-item active">
        <a class="nav-link" href="home.php">Accueil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="projection.php">Projections</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="film.php">Debug Film</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="reservation.php">Debug Reservation</a>
      </li>
    </ul>
  </div>
</nav>
    <div class="container theme-showcase" role="main">