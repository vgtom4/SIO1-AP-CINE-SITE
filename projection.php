<?php
include("includes/connexion.php");
include("includes/pageentete.php");
?>


<form method='POST' action='projection.php'>
    Date : <input type='date' name='date' value='<?php echo isset($_POST["date"]) ? $_POST["date"] : date("Y-m-d") ?>' onchange='form.submit()'/><br />
</form>

<?php
if (isset($_POST["date"])) { $date = $_POST["date"]; } else { $date = date("Y-m-d" ); }

// Création de la rêquete qui permet d'afficher les projections de la date saisie par l'utilisateur
$requete = ("select distinct * from projection natural join film where dateproj ='$date' order by heureproj, nosalle");
// Préparation de la requête en utilisant la variable préparée auparavant
$req = $bdd->prepare($requete);
$req->execute();
// Recherche des projections dans la base de données
$uneligne = $req->fetch();
if ($uneligne) {
    echo "<table><tr> <th>Horaire</th> <th>Film</th> <th>Salle</th> </tr>";
    while ($uneligne != null) {
        echo ("<tr> <td>".date("H\hi", strtotime($uneligne["heureproj"])). "</td> <td>$uneligne[titre]</td> <td>$uneligne[nosalle]</td>");
        $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$uneligne[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$uneligne[noproj]),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=$uneligne[noproj]");
        $req2 = $bdd->prepare($requete2);
        $req2->execute();
        $uneligne2 = $req2->fetch();
        echo "<td>";
        if ($uneligne2["nbplacerestante"]>0){
            echo "$uneligne2[nbplacerestante] sur $uneligne2[nbplaces]";
            echo "<td><form method='post' action='reservation.php'>";
            echo "<input type='hidden' name='noproj' value='$uneligne[noproj]'>";
            echo "<button type='submit'>Réserver pour cette séance</button>";
            echo "</form></td>";                
        }else{
            echo ("Aucune place disponible");
        }
        $uneligne = $req->fetch();
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}else{
    echo "Il n'y a pas de projection pour cette date";
}
$req->closeCursor();

include("includes/deconnexion.php");
include("includes/pagepied.php");
?>