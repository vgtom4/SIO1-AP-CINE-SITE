<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/media/logo.png">
    <!-- <link rel="stylesheet" href="assets/style/style.css"> -->
    <link href="https://fonts.googleapis.com/css?family=Proxima+Nova:400,700&display=swap" rel="stylesheet">

    <title>Accueil</title>
</head>

<body>
    <a href="projection.php">Projections</a>
    <div class="search-bar">
        <form method="get" action="reservation.php">
            <h1>Choisissez un film</h1>
            <select name="cbofilm">
                <option value="" disabled selected hidden>Sélectionnez un film</option>
                <?php
                $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

                $req = $bdd->prepare("select * from film");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne!=null)
                {
                    if (isset($_GET["cbofilm"])==true && $_GET["cbofilm"]==$uneligne["nofilm"]){
                        echo ("<option value=$uneligne[nofilm] selected>$uneligne[titre]</option>");
                    }
                    else 
                    {
                        echo ("<option value=$uneligne[nofilm]>$uneligne[titre]</option>");
                    }
                    $uneligne = $req->fetch();

                }

                $req->closeCursor();
                $bdd=null;
             ?>
            </select>



            <input type="submit" name="btnvalider" value="Rechercher">
        </form>
    </div>
    <?php 
    $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");
    if(isset($_GET["btnvalider"])==true) {

        if(isset($_GET["cbofilm"]) == true) {
        //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
        $requete = ("select dateproj, noproj, heureproj from projection where nofilm=".$_GET['cbofilm']."");
        }
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
        $uneligne = $req->fetch();

        while ($uneligne!=null)
        {
            $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$uneligne[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$uneligne[noproj]),0)");
            $req2 = $bdd->prepare($requete2);

            $req2->execute();

            $uneligne2 = $req2->fetchColumn();

            echo ("<br/>Une projection : $uneligne[dateproj] $uneligne[heureproj] ");
            if ($uneligne2){
                echo ("<a href='reservation2.php'>Réserver pour cette séance</a><br/>");
                echo ("Il reste $uneligne2 places pour cette séance <br/>");
            }else{
                echo ("<br/>Aucune place disponible <br/>");
            }
            
            $uneligne = $req->fetch();
        }
        $req->closeCursor();
        // test

    }
    $bdd=null;

?>
</body>

</html>