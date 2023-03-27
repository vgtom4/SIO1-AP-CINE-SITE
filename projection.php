<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/media/logo.png">
    <title>Projections</title>
</head>

<body>
    <a href="home.php">Accueil</a>
    <a href="projection.php">Projections</a>
    <a href="film.php">Debug Film</a>
    <a href="reservation.php">Debug Reservation</a>
    <?php
    echo "<form method='POST' action='projection.php'>";
    if (isset($_POST["btnvalider"]) == true) {
        echo "Date : <input type='date' name='date' value='$_POST[date]'/><br />";
    } else {
        echo "Date : <input type='date' name='date' value='" . date("Y-m-d") . "' /><br />";
    }
    echo "<input type='submit' name='btnvalider' value='Rechercher'>";
    echo "</form>";

    if (isset($_POST["btnvalider"]) == true) {
        $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

        // Création de la rêquete qui permet d'afficher les projections de la date saisie par l'utilisateur
        $requete = ("select distinct * from projection natural join film where dateproj ='$_POST[date]' order by heureproj, nosalle");
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche des projections dans la base de données
        $uneligne = $req->fetch();
        if ($uneligne == null) {
            echo "Il n'y a pas de projection pour cette date";
        }else{
            echo "<table><tr> <th>Horaire</th> <th>Film</th> <th>Salle</th> </tr>";
            while ($uneligne != null) {
                echo ("<tr> <td>".date("H\hi", strtotime($uneligne["heureproj"])) . "</td> <td>$uneligne[titre]</td> <td>$uneligne[nosalle]</td></tr>");
                $uneligne = $req->fetch();
            }
            echo "</table>";
        }
        $req->closeCursor();
    }
    $bdd = null;

    ?>
</body>

</html>