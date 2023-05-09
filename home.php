<?php
// Connexion à la base de données et inclusion de l'entête
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<center><h1>Bienvenue chez Pathé Mâcon</h1></center>

<?php
// Inclusion de la barre de recherche et de la requête de recherche
include("includes/search-page/search-bar.php");
include("includes/search-page/search-request.php");
?>

<!-- Affichage des films correspondants à la recherche -->
<div class='liste-film'>
    <?php if (!$uneligne) { ?>
        <center><h2>Aucun film ne correspond à votre recherche</h2></center>
    <?php } ?>
    <div class='films-grid'>
        <?php 
        while ($uneligne)
        { ?>
            <!-- Affichage des films correspondants à la recherche
            // Lors du clic sur un film, le formulaire est envoyé vers film.php -->
            <div class='film' onclick='document.getElementById("form_<?php echo $uneligne["nofilm"] ?>").submit()'>
                <img src='assets/media/affiches/<?php echo $uneligne["imgaffiche"] ?>'>
                <p><?php echo $uneligne["titre"] ?></p>
                <form id='form_<?php echo $uneligne["nofilm"]?>' method='post' action='film.php'>
                    <input type='hidden' name='nofilm' value='<?php echo $uneligne["nofilm"] ?>'>
                    <input type='hidden' name='titre' value='<?php echo urlencode($uneligne["titre"]) ?>'>
                </form>
            </div>
            <?php $uneligne = $req->fetch();
        } ?>
    </div>
    <?php $req->closeCursor();?>
</div>

<?php
include("includes/deconnexion.php"); 
include("includes/pagepied.php");
?>