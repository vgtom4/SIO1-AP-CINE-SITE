<?php 
include("../../includes/connexion.php");
include("../../includes/pageentete.php");

// Permet d'ajouter une projection à la base de données après vérification de la disponibilité de la salle
if (isset($_POST["btnajouter"])) {
    if ($_POST["cbosalle"] != ""){
        // Vérifier si la salle est déjà occupée à la date et l'heure de la projection à ajouter
        $requete =  "SELECT EXISTS(".
                    "SELECT noproj FROM projection ".
                    "WHERE dateproj = '$_POST[date]' ".
                    "AND nosalle = '$_POST[cbosalle]' ".
                    "AND CAST(heureproj as time) ".
                    "BETWEEN TIMEDIFF(".
                        "'$_POST[time]', ".
                        "ADDTIME(".
                            "'00:05:00',".
                            "(SELECT duree FROM film natural join projection ".
                            "WHERE dateproj = '$_POST[date]' ".
                            "AND nosalle = '$_POST[cbosalle]' ".
                            "AND heureproj = (".
                                "SELECT MAX(heureproj) FROM projection ".
                                "WHERE dateproj = '$_POST[date]' ".
                                "AND nosalle = '$_POST[cbosalle]' ".
                                "AND heureproj <= '$_POST[time]')))) ".
                    "AND ADDTIME('$_POST[time]', ".
                        "ADDTIME('00:05:00',".
                            "(SELECT duree FROM film WHERE nofilm = $_POST[nofilm])))) AS filmBlocked";

        $req = $bdd->prepare($requete);
        $req->execute();
        $salle_occupee = (bool) $req->fetchColumn();

        // Si la salle est déjà occupée à la date et l'heure de la projection à ajouter, afficher un message d'erreur
        if ($salle_occupee) {?>
            <script type="text/javascript">window.alert("Impossible d\'ajouter la projection car la salle est déjà occupée à cette date et à cette heure.");</script>';
        <?php
        } else {
            // Ajout de la projection à la base de données
            $requete="insert into projection values (null,'$_POST[date]','$_POST[time]','$_POST[txtInfo]',$_POST[nofilm],'$_POST[cbosalle]')";
            $req=$bdd->prepare($requete);
            $req->execute();
        }
        $req->closeCursor();
    }
}

// Permet de supprimer une projection de la base de données après vérification de la présence de réservations
if(isset($_POST["delete_projection"])) {
    // Vérifier si des réservations existent pour la projection à supprimer
    $requete = "select sum(nbplacesresa) from reservation where noproj=$_POST[noproj]";
    $req = $bdd->prepare($requete);
    $req->execute();
    $nbPlacesResa = $req->fetchColumn();
    ?>

    <!-- Formulaire cacher permettant de confirmation de suppression d'une projection -->
    <form id="delete_form_confirm_<?php echo $_POST["noproj"] ?>" method='post'>
        <input type='hidden' name='noproj' value='<?php echo $_POST["noproj"] ?>'>
        <input type='hidden' name='nofilm' value='<?php echo $_POST["nofilm"] ?>'>
        <input type='hidden' name='delete_confirm' value='true'>
    </form>

    <?php
    // Si des réservations existent, affichage d'un message (pop-up) de confirmation
    if ($nbPlacesResa > 0) {
        ?>
        <script>
            if(confirm("Des réservations ont été faites pour cette séance.\nÊtes-vous sûr de vouloir la supprimer ?")) {
                document.getElementById("delete_form_confirm_<?php echo $_POST["noproj"] ?>").submit();
            }
        </script>
        <?php
    } else {
        ?>
        <script>document.getElementById("delete_form_confirm_<?php echo $_POST["noproj"] ?>").submit();</script>
        <?php
    }
}

// Suppression de la projection et de ses réservations associées
if(isset($_POST["delete_confirm"])) {
    // Supprimer la projection et ses réservations associées
    $requete = "DELETE FROM reservation WHERE noproj = $_POST[noproj]";
    $req = $bdd->prepare($requete);
    $req->execute();

    $requete = "DELETE FROM projection WHERE noproj = $_POST[noproj]";
    $req = $bdd->prepare($requete);
    $req->execute();
}
?>

<?php
// Affichage des informations du film
include("../../includes/info-film.php");
?>

<?php if ($erreur) echo ("</br></br><a href='homeAdmin.php'>Retour à l'accueil</a>") ?>

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
            while ($uneligne)
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
                            <th>Salle</th>
                        </tr>
                <?php } ?>
                
                <tr>
                    <!-- Affichage des informations de la séance -->
                    <td><?php echo date('G\hi', strtotime($uneligne["heureproj"]))?></td>
                    <td><?php echo $uneligne["infoproj"]?></td>
                    <td><?php echo $uneligne["nosalle"]?></td>
                    <td>
                        <!-- Formulaire permettant de supprimer une projection -->
                        <button onclick='document.getElementById("delete_form_<?php echo $uneligne["noproj"] ?>").submit();'>Supprimer cette séance</button>
                        <form id="delete_form_<?php echo $uneligne["noproj"] ?>" method='post'>
                            <input type='hidden' name='noproj' value='<?php echo $uneligne["noproj"] ?>'>
                            <input type='hidden' name='nofilm' value='<?php echo $_POST["nofilm"] ?>'>
                            <input type='hidden' name='delete_projection' value='true'>
                        </form>
                    </td>
                </tr>
                <?php $uneligne = $req->fetch(); 
            }?>
            </table>
            <?php
        }else{ ?>
            <h4>Aucune séance n'est prévue pour ce film</h4>            
        <?php }
        $req->closeCursor();
    } ?>
</div>

<!-- Affichage du formulaire d'ajout de séances -->
<div name='ajout-seance'>
    <?php if (!$erreur){?>
        </br></br>
        <h1>Ajouter des séances</h1>
        </br>
        <!-- Formulaire permettant d'ajouter des séances -->
        <form method='post'>
            Informations sur la projection : <input type="text" name='txtInfo' value='<?php isset($_POST["txtInfo"]) ? $_POST["txtInfo"] : ''?>'></textarea>
            </br>
            <label for="salle-select">Salle :</label>
            <select name="cbosalle" required>
                <option value="" selected>Sélectionner une salle</option>
                <?php
                // Recherche des salles dans la base de données
                $req = $bdd->prepare("select * from salle");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne)
                {
                    // Affichage des salles dans une liste déroulante
                    echo ("<option value=$uneligne[nosalle]>$uneligne[nosalle]</option>");
                    $uneligne = $req->fetch();
                }
                $req->closeCursor();
                ?>
            </select>
            </br>Date : <input type='date' name='date' value='<?php echo isset($_POST["date"]) ? $_POST["date"] : date("Y-m-d") ?>' required/>
            </br>Heure : <input type='time' name='time' value='<?php echo isset($_POST["time"]) ? $_POST["time"] : date("H:i") ?>' required/><br />
            <input type='hidden' name='nofilm' value='<?php echo $_POST["nofilm"] ?>'>
            <input type='submit' name='btnajouter' value='Ajouter'>
        </form>
    <?php } ?>
</div>

<?php 
include("../../includes/deconnexion.php"); 
include("../../includes/pagepied.php");
?>