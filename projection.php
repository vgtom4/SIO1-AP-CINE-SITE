<?php
// Connexion à la base de données et inclusion de l'entête
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<!-- Gestion de la date -->
<?php
// Vérifie si la date a été sélectionnée par l'utilisateur
// Si oui, on récupère la date sélectionnée
// Si non, on récupère la date du jour
if (isset($_POST["date"]) && strtotime($_POST["date"])) {
    $date = $_POST["date"];
    // Vérifie si l'utilisateur a cliqué sur le bouton "previous"
    // Si oui, on récupère la date précédent la date sélectionnée dans la base de données
    if (isset($_POST["btnprevious"])) {
        $requete = ("select distinct MAX(dateproj) from projection where dateproj < '$date'");
        $req = $bdd->prepare($requete);
        $req->execute();
        $date = $req->fetchColumn();
    } else 
    // Vérifie si l'utilisateur a cliqué sur le bouton "next"
    // Si oui, on récupère la date suivant la date sélectionnée dans la base de données
    if (isset($_POST["btnnext"])) {
        $requete = ("select distinct MIN(dateproj) from projection where dateproj > '$date'");
        $req = $bdd->prepare($requete);
        $req->execute();
        $date = $req->fetchColumn();
    }
    // On vérifie si la date est nulle
    // Si oui, cela signifie que l'utilisateur a cliqué sur le bouton "previous" ou "next" et qu'il n'y a pas de date précédente ou suivante
    if ($date == null) $date = $_POST["date"];
} else {
    // Si l'utilisateur a saisi une date, on l'enregistre dans la variable $date, sinon on met la date du jour
    $date = date("Y-m-d"); 
}
?>

Sélectionnez une date de projection : 
<!-- Bouton "previous" et "next" -->
<form method="post">
    <input type="hidden" name="date" value="<?php echo $date; ?>">
    <input type="submit" name="btnprevious" value="Projections précédentes">
    <input type='date' name='date' value='<?php echo $date ?>' onchange='form.submit()'/>
    <input type="submit" name="btnnext" value="Projections suivantes">
</form>
<?php

// Affichage de la date sélectionnée
$dateproj = date('l j F Y', strtotime($date));?>
<br><h2>Projections du <?php echo $dateproj ?>:</h2>

<?php

// Recherche des projections dans la base de données en fonction de la date sélectionnée
$requete = ("select distinct * from projection natural join film where dateproj =:date order by heureproj, nosalle");
$req = $bdd->prepare($requete);
$req->bindParam(':date', $date, PDO::PARAM_STR);
$req->execute();
$uneligne = $req->fetch();

// Vérifie si des projections ont été trouvées pour la date sélectionnée
if ($uneligne) {
    $horaire = "";
    // Affichage des projections trouvées
    while ($uneligne) {
        if ($horaire != date('G\hi', strtotime($uneligne["heureproj"]))){
            $horaire = date('G\hi', strtotime($uneligne["heureproj"]));?>
            </br><h3>Séance(s) de <?php echo $horaire?></h3></br>
        <?php } ?>

        <!-- Affichage des informations de la projection -->
        <table cellpadding='5'>
            <tr>
                <!-- Affichage de l'affiche et du titre du film -->
                <td rowspan='7'><img src='assets/media/affiches/<?php echo $uneligne["imgaffiche"]?>' width=150px></td>
                <td><?php echo $uneligne["titre"]?></td>
            </tr>
            <!-- Affichage de la durée du film -->
            <tr><td>Durée : <?php echo date('G\hi', strtotime($uneligne["duree"]))?></td></tr>
            <!-- Affichage des informations de la projection -->
            <tr><td><?php echo $uneligne["infoproj"]?></td></tr>
            <tr><td>
                <!-- Affichage du bouton pour voir plus d'informations sur le film -->
                <form id='form_<?php echo $uneligne["nofilm"]?>' method='post' action='film.php'>
                    <input type='hidden' name='nofilm' value='<?php echo $uneligne["nofilm"]?>'>
                    <input type='hidden' name='titre' value='<?php echo urlencode($uneligne["titre"])?>'>
                    <button type='submit'>Voir plus</button>
                </form>
            </td></tr>
            
            <tr><td>
                <?php
                // Recherche du nombre de places restantes pour la projection
                $requete2 = ("select (select nbplaces from salle natural join projection where noproj=:noproj) - COALESCE((select sum(nbplacesresa) from reservation where noproj=:noproj),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=:noproj");
                $req2 = $bdd->prepare($requete2);
                $req2->bindParam(':noproj', $uneligne["noproj"], PDO::PARAM_INT);
                $req2->execute();
                $uneligne2 = $req2->fetch();

                // Vérifie si des places sont disponibles pour la projection
                // Si oui, affiche le nombre de places restantes et le bouton pour réserver
                // Si non, affiche qu'il n'y a plus de places disponibles
                if ($uneligne2["nbplacerestante"]>0){ ?>
                    Place(s) disponible(s) : <?php echo $uneligne2["nbplacerestante"]."/".$uneligne2["nbplaces"]?>
                    <form method='post' action='reservation.php'>
                        <input type='hidden' name='noproj' value='<?php echo $uneligne["noproj"]?>'>
                        <button type='submit'>Réserver pour cette séance</button>
                    </form>           
                <?php }else{ ?>
                    Aucune place disponible
                <?php } ?>
            </td></tr>
        </table>

        <!-- Affichage du bouton pour réserver la projection -->
        <form id='form_<?php echo $uneligne["nofilm"]?>' method='post' action='reservation.php'>
            <input type='hidden' name='nofilm' value='<?php echo $uneligne["nofilm"]?>'>
            <input type='hidden' name='titre' value='<?php echo urlencode($uneligne["titre"])?>'>
        </form>
        </br>

        <?php $uneligne = $req->fetch();
    } ?>
    </table>
<?php }else{ ?>
    Il n'y a pas de projection pour cette date
<?php }
$req->closeCursor();

include("includes/deconnexion.php");
include("includes/pagepied.php");
?>