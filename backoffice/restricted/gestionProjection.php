<?php 
include("../../includes/connexion.php");
include("../../includes/pageentete.php");

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

        echo $requete;
        $req = $bdd->prepare($requete);
        $req->execute();
        $salle_occupee = (bool) $req->fetchColumn();

        if ($salle_occupee) {
            echo '<script type="text/javascript">window.alert("Impossible d\'ajouter la projection car la salle est déjà occupée à cette date et à cette heure.");</script>';
        } else {
            // Ajouter la projection
            $requete3="insert into projection values (null,'$_POST[date]','$_POST[time]','$_POST[txtInfo]',$_POST[nofilm],'$_POST[cbosalle]')";
            $req3=$bdd->prepare($requete3);
            $req3->execute();
        }
    }
}

if(isset($_POST["btnsupprimer"])) {
    // Vérifier si des réservations existent pour la projection à supprimer
    $requete = "SELECT EXISTS(select noresa from projection natural join reservation where noproj=$_POST[noproj])";
    $req = $bdd->prepare($requete);
    $req->execute();
    $reservations_exist = (bool) $req->fetchColumn();

    if ($reservations_exist) {
        echo '<script type="text/javascript">window.alert("Impossible de supprimer la projection car des réservations y sont affiliés.");</script>';
    } else {
        // Supprimer la projection et ses réservations associées
        $requete = "DELETE FROM reservation WHERE noproj = $_POST[noproj]";
        $req = $bdd->prepare($requete);
        $req->execute();

        $requete = "DELETE FROM projection WHERE noproj = $_POST[noproj]";
        $req = $bdd->prepare($requete);
        $req->execute();
    }
}
?>

<div name='info-film'>
    </br>
    <?php 
    if (isset($_POST["nofilm"])){
        $erreur = false;

        //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
        $requete = ("select * from film natural join public where nofilm=$_POST[nofilm]");
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
        $uneligne = $req->fetch();

        if ($uneligne){
            echo "<table cellpadding='5'>";
                echo "<tr>";
                echo "<td rowspan='7'><img src='../../assets/media/affiches/$uneligne[imgaffiche]' width=200px></td>";
                echo "<td>$uneligne[titre]</td>";
                echo "</tr>";
                echo "<tr><td>Durée : ".str_replace(":","h",date('G:i', strtotime($uneligne["duree"])))."</td></tr>";
                echo "<tr><td>$uneligne[realisateurs]</td></tr>";
                echo "<tr><td>$uneligne[acteurs]</td></tr>";
                echo "<tr><td>$uneligne[infofilm]</td></tr>";
                echo "<tr><td>$uneligne[libpublic]</td></tr>";
                echo "<tr><td colspan='2'>$uneligne[synopsis]</td></tr>";
            echo "</table>";
        }else{
            echo "Erreur : film inconnu";
            $erreur = true;
        }

        $req->closeCursor();
    }else{
        echo "Erreur : problème de film sélectionné";
        $erreur = true;
    }?>
</div>

<div name='seance-film'>
    </br>

    <?php 
    if (!$erreur){?>
        <h1>Séances</h1>
        
        <?php
        //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
        if(isset($_POST["nofilm"])) $requete = ("select * from projection where nofilm=$_POST[nofilm] ORDER BY dateproj, heureproj");
        
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
        $uneligne = $req->fetch();

        $dateDeProj = null;
        
        while ($uneligne)
        {
            $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$uneligne[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$uneligne[noproj]),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=$uneligne[noproj]");
            $req2 = $bdd->prepare($requete2);
            $req2->execute();
            $uneligne2 = $req2->fetch();
            
            if ($dateDeProj!=$uneligne["dateproj"]){
                echo "</table>";
                $dateDeProj = $uneligne["dateproj"];
                $date = date('l j F Y', strtotime($uneligne["dateproj"]));
                echo "<br/>Projections du $date :";
                echo "<table cellpadding='5'>";
                echo "<tr>";
                    echo "<th>Horaire</th>";
                    echo "<th>Informations séance</th>";
                echo "</tr>";
            }?>
            
            <tr>
                <td>
                    <?php echo str_replace(":","h",date('G:i', strtotime($uneligne["heureproj"]))) ?>
                </td>
            <td>
            <?php echo $uneligne["infoproj"]?>
            </td>
            <td>
                <td>
                    <form method='post' action='gestionProjection.php'>
                        <input type='hidden' name='noproj' value='<?php echo $uneligne["noproj"] ?>'>
                        <input type='hidden' name='nofilm' value='<?php echo $_POST["nofilm"] ?>'>
                        <button type='submit' name='btnsupprimer'>Supprimer cette séance</button>
                    </form></td>
                </td>
            </tr>

            <?php $uneligne = $req->fetch(); }?>
        </table>
        <?php $req->closeCursor(); ?>

        <form method='post'>
            <h1>Ajouter des séances</h1>

            Informations sur la projection : <input type='text' name='txtInfo' value='<?php isset($_POST["txtInfo"]) ? $_POST["txtInfo"] : '' ?>' />
            </br>
            </br>
            <label for="salle-select">Salle :</label>
            <select name="cbosalle" required>
                <option value="" selected>Sélectionner une salle</option>

                <?php
                $req = $bdd->prepare("select * from salle");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne)
                {
                    // Affichage des numéros de salle dans la liste déroulante en sélectionnant la valeur précédemment sélectionnée
                    echo ("<option value=$uneligne[nosalle]>$uneligne[nosalle]</option>");
                    $uneligne = $req->fetch();
                }
                $req->closeCursor();
                ?>

            </select>
            </br>

            Date : <input type='date' name='date' value='<?php echo isset($_POST["date"]) ? $_POST["date"] : date("Y-m-d") ?>' required/><br />
            Heure : <input type='time' name='time' value='<?php echo isset($_POST["time"]) ? $_POST["time"] : date("H:i") ?>' required/><br />
            <input type='hidden' name='nofilm' value='<?php echo $_POST["nofilm"] ?>'>
            <input type='submit' name='btnajouter' value='Ajouter'>
        </form>
    <?php } ?>
</div>

<?php 
include("../../includes/deconnexion.php"); 
include("../../includes/pagepied.php");
?>

