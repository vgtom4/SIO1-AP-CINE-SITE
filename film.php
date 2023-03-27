<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/media/logo.png">
    <!-- <link rel="stylesheet" href="assets/style/style.css"> -->
    <link href="https://fonts.googleapis.com/css?family=Proxima+Nova:400,700&display=swap" rel="stylesheet">

    <title><?php echo isset($_POST["titre"]) ? urldecode($_POST["titre"]) : "film" ?></title>
</head>

<body>
    <a href="home.php">Accueil</a>
    <a href="projection.php">Projections</a>
    <a href="film.php">Debug Film</a>
    <a href="reservation.php">Debug Reservation</a>

    <div name='info-film'>
        </br>
        <?php 
        if (isset($_POST["nofilm"])){
            $erreur = false;
            $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

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
                    echo "<td rowspan='7'><img src='assets/media/affiches/$uneligne[imgaffiche]' width=200px></td>";
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
        }
        ?>
    </div>
    <div name='seance-film'>
        </br>
        <?php 
        if (!$erreur){
            echo "<h1>Séances</h1>";
            

            if(isset($_POST["nofilm"])) {
                //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
                $requete = ("select * from projection where nofilm=$_POST[nofilm] ORDER BY dateproj, heureproj");
            }
            // Préparation de la requête en utilisant la variable préparée auparavant
            $req = $bdd->prepare($requete);
            $req->execute();
            // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
            $uneligne = $req->fetch();

            if ($uneligne!=null){
                $dateDeProj = $uneligne["dateproj"];
                $date = date('l j F Y', strtotime($uneligne["dateproj"]));
                echo "Projections du $date :";
                echo "<table cellpadding='5'>";
                echo "<tr>";
                    echo "<th>Horaire</th>";
                    echo "<th>Informations séance</th>";
                    echo "<th>Places disponibles</th>";
                echo "</tr>";
            }
        
            while ($uneligne!=null)
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
                        echo "<th>Places disponibles</th>";
                    echo "</tr>";
                }
                
                echo "<tr>";
                echo "<td>";
                echo str_replace(":","h",date('G:i', strtotime($uneligne["heureproj"])));
                echo "</td>";
                echo "<td>";
                echo $uneligne["infoproj"];
                echo "</td>";
                echo "<td>";
                if ($uneligne2["nbplacerestante"]>0){
                    echo ("$uneligne2[nbplacerestante] sur $uneligne2[nbplaces] <td><a href='reservation.php?noproj=$uneligne[noproj]'>Réserver pour cette séance</a></td>");
                }else{
                    echo ("Aucune place disponible");
                }
                echo "</td>";
                echo "</tr>";
                
                $uneligne = $req->fetch();
            }
            echo "</table>";
            $req->closeCursor();
            // test
                
            $bdd=null;
        }
        ?>
    </div>

    
</body>

</html>