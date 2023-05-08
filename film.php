<?php
// Connexion à la base de données et inclusion de l'entête
include("includes/connexion.php");
include("includes/pageentete.php");

// Affichage des informations du film
include("includes/info-film.php");
?>

<!-- Affichage des séances du film -->
<div name='seance-film'>
    </br>
    <?php 
    // Si aucune erreur n'est survenue, on affiche les séances du film
    if (!$erreur){?>
        <h1>Séances</h1>
        
        <?php
        // Recherche des séances du film dans la base de données
        $requete = ("select * from projection where nofilm=$_POST[nofilm] ORDER BY dateproj, heureproj, nosalle");
        $req = $bdd->prepare($requete);
        $req->execute();
        $uneligne = $req->fetch();

        // Si des séances sont prévues pour le film, on les affiche
        // Sinon, on affiche un message indiquant qu'aucune séance n'est prévue
        if ($uneligne) {
            $dateDeProj = null;
            // Affichage des séances
            while($uneligne)
            {
                // Si la date de la projection est différente de la date de la projection précédente, 
                // on ferme la table et on en ouvre une nouvelle.
                if ($dateDeProj!=$uneligne["dateproj"]){
                    $dateDeProj = $uneligne["dateproj"];
                    $date = date('l j F Y', strtotime($uneligne["dateproj"]));?>
                    </table>

                    <br><h4>Projections du <?php echo $date ?>:</h4>
                    <table cellpadding=10>
                        <tr>
                            <th>Horaire</th>
                            <th>Informations séance</th>
                            <th>Places disponibles</th>
                        </tr>
                <?php } ?>
                
                <tr>
                    <!-- Affichage des informations de la séance -->
                    <td><?php echo date('G\hi', strtotime($uneligne["heureproj"]))?></td>
                    <td><?php echo $uneligne["infoproj"]?></td>
                    <td>
                        <?php
                        // Recherche du nombre de places restantes pour la projection
                        $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$uneligne[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$uneligne[noproj]),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=$uneligne[noproj]");
                        $req2 = $bdd->prepare($requete2);
                        $req2->execute();
                        $uneligne2 = $req2->fetch();
                        // Si des places sont disponibles, on affiche un bouton pour réserver
                        // Sinon, on affiche un message indiquant qu'il n'y a plus de places disponibles
                        if ($uneligne2["nbplacerestante"]>0){ 
                            echo "$uneligne2[nbplacerestante] sur $uneligne2[nbplaces]" ?>
                            <td>
                                <!-- Formulaire pour réserver une place -->
                                <form method='post' action='reservation.php'>
                                    <input type='hidden' name='noproj' value='<?php echo $uneligne["noproj"] ?>'>
                                    <button type='submit'>Réserver pour cette séance</button>
                                </form>
                            </td>       
                        <?php }else{ ?>
                            Aucune place disponible
                        <?php } ?>
                    </td>
                </tr>
                <?php $req2->closeCursor(); 
                $uneligne = $req->fetch();
            }?>
            </table>
            <?php $req->closeCursor();
        }else{ ?>
            <h4>Aucune séance n'est prévue pour ce film</h4>
        <?php }
    }
    ?>
</div>

<?php if ($erreur) echo ("</br></br><a href='home.php'>Retour à l'accueil</a>") ?>

<?php
include("includes/deconnexion.php");
include("includes/pagepied.php");
?>