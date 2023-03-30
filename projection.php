<?php
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<?php
echo "<form method='POST' action='projection.php'>";
    echo "Date : <input type='date' name='date' value='".(isset($_POST["date"]) ? $_POST["date"] : date("Y-m-d"))."' onchange='form.submit()'/><br />";
echo "</form>";

if (isset($_POST["date"]) == true) {
    // Création de la rêquete qui permet d'afficher les projections de la date saisie par l'utilisateur
    $requete = ("select distinct * from projection natural join film where dateproj ='$_POST[date]' order by heureproj, nosalle");
    // Préparation de la requête en utilisant la variable préparée auparavant
    $req = $bdd->prepare($requete);
    $req->execute();
    // Recherche des projections dans la base de données
    $uneligne = $req->fetch();
    if ($uneligne) {
        echo "<table><tr> <th>Horaire</th> <th>Film</th> <th>Salle</th> </tr>";
        while ($uneligne != null) {
            echo ("<tr> <td>".date("H\hi", strtotime($uneligne["heureproj"])). "</td> <td>$uneligne[titre]</td> <td>$uneligne[nosalle]</td></tr>");
            $uneligne = $req->fetch();
        }
        echo "</table>";
    }else{
        echo "Il n'y a pas de projection pour cette date";
    }
    $req->closeCursor();
}

include("includes/deconnexion.php");
include("includes/pagepied.php");
?>