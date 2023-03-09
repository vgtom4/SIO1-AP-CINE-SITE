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
    <?php
    echo "<form method='get' action='projection.php'>";
    if (isset($_GET["btnvalider"]) == true) {
        echo "Date : <input type='date' name='date' value='" . $_GET["date"] . "'/><br />";
    } else {
        echo "Date : <input type='date' name='date' value='" . date("Y-m-d") . "'/><br />";
    }
    echo "<input type='submit' name='btnvalider' value='Rechercher'>";
    echo "</form>";

    if (isset($_GET["btnvalider"]) == true) {
        $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

        // Création de la rêquete qui permet d'afficher les projections de la date saisie par l'utilisateur
        $requete = ("select distinct * from projection natural join film where dateproj ='$_GET[date]' order by heureproj, nosalle");
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche des projections dans la base de données
        $uneligne = $req->fetch();
        if ($uneligne == null) {
            echo "Il n'y a pas de projection pour cette date";
        }
        while ($uneligne != null) {
            echo (date("H\hi", strtotime($uneligne["heureproj"])) . "  $uneligne[titre] $uneligne[nosalle]<br/>");
            $uneligne = $req->fetch();
        }
        $req->closeCursor();
    }
    $bdd = null;

    ?>
</body>

</html>