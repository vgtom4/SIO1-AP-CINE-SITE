<?php
// Connexion à la base de données et inclusion de l'entête
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<!-- Affichage de l'input date pour sélectionner une date de projection -->
<form method='POST' action='projection.php'>
    Sélectionnez une date de projection : <input type='date' name='date' value='<?php echo isset($_POST["date"]) ? $_POST["date"] : date("Y-m-d") ?>' onchange='form.submit()'/><br />
</form>

<?php
// Si l'utilisateur a saisi une date, on l'enregistre dans la variable $date, sinon on met la date du jour
if (isset($_POST["date"])) { $date = $_POST["date"]; } else { $date = date("Y-m-d" ); }

// Recherche des projections dans la base de données en fonction de la date sélectionnée
$requete = ("select distinct * from projection natural join film where dateproj ='$date' order by heureproj, nosalle");
$req = $bdd->prepare($requete);
$req->execute();
$uneligne = $req->fetch();

// Vérifie si des projections ont été trouvées pour la date sélectionnée
if ($uneligne) {
    $horaire = "";
    // Affichage des projections trouvées
    while ($uneligne) {
        if ($horaire != date('G\hi', strtotime($uneligne["heureproj"]))){
            $horaire = date('G\hi', strtotime($uneligne["heureproj"]));?>
            </br></br><h3>Séance(s) de <?php echo $horaire?></h3></br>
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
                $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$uneligne[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$uneligne[noproj]),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=$uneligne[noproj]");
                $req2 = $bdd->prepare($requete2);
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