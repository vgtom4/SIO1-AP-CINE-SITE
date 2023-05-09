<?php session_start();
// Vérifie si la page est appelée depuis le backoffice ou le frontoffice
if (strpos(dirname($_SERVER['PHP_SELF']), "/backoffice/restricted")) { $pageAdmin=true; }else{ $pageAdmin=false; }
?>

<!-- Début du code HTML -->
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Affichage de l'icone d'onglet -->
  <link rel="icon" href="<?php echo $pageAdmin ? "../../" : "" ?>assets/media/logo.png">

  <!-- Affichage du nom de l'onglet -->
  <title>Pathé Ciné - <?php echo isset($_POST["titre"]) ? urldecode($_POST["titre"]) : pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) ?></title>

  <!-- Importation des feuilles de style -->
  
  <link rel="stylesheet" href="<?php echo $pageAdmin ? "../../" : "" ?>assets/style/multi-select-tag.css">
  <link rel="stylesheet" href="<?php echo $pageAdmin ? "../../" : "" ?>assets/style/style.css">

  <!-- Importation de bootstrap -->
  <!-- <link href="<?php echo $pageAdmin ? "../../" : "" ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $pageAdmin ? "../../" : "" ?>bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
  <script src="<?php echo $pageAdmin ? "../../" : "" ?>bootstrap/jquery/jquery-3.3.1.min.js"></script>
  <script src="<?php echo $pageAdmin ? "../../" : "" ?>bootstrap/js/bootstrap.min.js"></script> -->
</head>
<body style="background-color: #1d1d1d;" class="text-light">
  <nav class="navbar">
    <!-- Affichage du logo dans la barre de navigation -->
    <a class="navbar-logo" href="<?php echo $pageAdmin ? "homeAdmin.php" : "home.php" ?>"><img src="<?php echo $pageAdmin ? "../../" : "" ?>assets/media/logo.png"/></a>
    <div>
      <ul>
      <?php
      // Récupération du nom de la page courante
      $currentPage = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
      // Affichage des liens de navigation en fonction de la page courante
      if ($pageAdmin) { ?>
        <!-- Affichage des liens de navigation pour le backoffice -->
        <li class='nav-item <?php echo $currentPage == 'homeAdmin' ? "active" : null ?>'>
          <a class='nav-link' href='homeAdmin.php'>Accueil ADMIN</a></li>
        <li class='nav-item <?php echo $currentPage == 'home' ? "active" : null ?>'>
          <a class='nav-link' href='../../home.php'>Accueil Client</a></li>
      <?php }else{ ?>
        <!-- Affichage des liens de navigation pour le frontoffice -->
        <li class='nav-item <?php echo $currentPage == 'home' ? "active" : null ?>'>
          <a class='nav-link' href='home.php'>Accueil</a></li>
        <li class='nav-item <?php echo $currentPage == 'projection' ? "active" : null ?>'>
          <a class='nav-link' href='projection.php'>Projections</a></li>
        <li class='nav-item'>
          <a class='nav-link' href='backoffice/restricted/homeAdmin.php'>Gestion ADMIN</a></li>
      <?php } ?>
      </ul>
    </div>
  </nav>

  <div class="container theme-showcase" role="main">