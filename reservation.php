<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/media/logo.png">
    <!-- <link rel="stylesheet" href="assets/style/style.css"> -->
    <link href="https://fonts.googleapis.com/css?family=Proxima+Nova:400,700&display=swap" rel="stylesheet">

    <title>Reservation</title>
</head>

<body>
<a href="home.php">Accueil</a>
    <a href="projection.php">Projections</a>
    <a href="film.php">Debug Film</a>
    <a href="reservation.php">Debug Reservation</a>

    <div class="reservation-info">
        <?php
        if (isset($_GET["noproj"])){
            $erreur = false;
            $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

            //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
            $requete = ("select * from projection natural join film natural join salle where noproj=$_GET[noproj]");
            // Préparation de la requête en utilisant la variable préparée auparavant
            $req = $bdd->prepare($requete);
            $req->execute();
            // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
            $uneligne = $req->fetch();

            if ($uneligne){
                echo "<h1>Réservation pour le film : $uneligne[titre]</h1>";
                echo "<table cellpadding='5'>";
                    echo "<tr>";
                    echo "<td rowspan='7'><img src='assets/media/affiches/$uneligne[imgaffiche]' width=100px></td>";
                    $date = date('l j F Y', strtotime($uneligne["dateproj"]));
                    echo "<td>Date : $date</td>";
                    echo "</tr>";
                    echo "<tr><td>Horaire : ".str_replace(":","h",date('G:i', strtotime($uneligne["heureproj"])))."</td></tr>";
                    echo "<tr><td>Salle $uneligne[nosalle]</td></tr>";
                    echo "<tr><td>$uneligne[infoproj]</td></tr>";
                echo "</table>";
            }else{
                echo "Erreur : projection inconnue";
                $erreur = true;
            }

            $req->closeCursor();
        }else{
            echo "Erreur : problème de projection sélectionnée";
            $erreur = true;
        }
        ?>
    </div>

    <div class="reservation-form">
        <?php 
        
        $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$_GET[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$_GET[noproj]),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=$_GET[noproj]");
        $req2 = $bdd->prepare($requete2);
        $req2->execute();
        $uneligne2 = $req2->fetch();
        
        echo "<td>";
        if ($uneligne2["nbplacerestante"]>0){
            echo "<form method='get' action='reservation.php'>";
            echo "<input type='hidden' name='noproj' value='$_GET[noproj]'>";
            echo "Indiquez le nombre de place à réserver : <input type='number' name='nbplaceresa' min='1' max='$uneligne2[nbplacerestante]' value='1' required>";
            echo "(place(s) disponible(s) : $uneligne2[nbplacerestante] / $uneligne2[nbplaces])</br>";
            echo "Pseudo :<input type='text' name='txtpseudo' placeholder='Saisir pseudo' required></br>";
            echo "Mot de passe : <input type='password' name='txtpwd' placeholder='Saisir mot de passe' required></br>";
            echo "<input type='submit' name='btnvalider' value='Reserver'>";
            echo "</form>";
        }else{
            echo ("<h1>Séance complète.</h1>");
            echo ("<h1>Aucune place disponible.</h1>");
            echo ("<img src='https://media.giphy.com/media/xX0rXi3iWNd0qpWsXq/giphy.gif'>");
        }
        ?>
        
    </div>
    <?php 
    $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");
    if(isset($_GET["btnvalider"])==true) {
        echo "séance réservé</br>";
        echo "mettre récapitulatif";
    }
    $bdd=null;

?>
</body>

</html>