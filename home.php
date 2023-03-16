<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/media/logo.png">
    <link rel="stylesheet" href="assets/style/style.css">
    <link href="https://fonts.googleapis.com/css?family=Proxima+Nova:400,700&display=swap" rel="stylesheet">

    <title>Accueil</title>
</head>

<body>
    <a href="projection.php">Projections</a>
    <div class="search-bar">
        <form method="get" action="home.php">
            <label for="title-input">Titre :</label>
            <input type="text" name="txttitre" />
            
            <label for="actor-input">Acteur :</label>
            <input type="text" name="txtact" />
            <label for="director-input">Réalisateur :</label>
            <input type="text" name="txtreal" />
            <label for="public-select">Public :</label>
            <select name="cbopublic">
                <option value="" disabled selected hidden>Sélectionner un public</option>
                <?php
                $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

                $req = $bdd->prepare("select * from public");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne!=null)
                {
                    if (isset($_GET["cbopublic"])==true && $_GET["cbopublic"]==$uneligne["nopublic"]){
                        echo ("<option value=$uneligne[nopublic] selected>$uneligne[libpublic]</option>");
                    }
                    else 
                    {
                        echo ("<option value=$uneligne[nopublic]>$uneligne[libpublic]</option>");
                    }
                    $uneligne = $req->fetch();

                }
                
                $req->closeCursor();
                $bdd=null;
             ?>
            </select>
            <label for="genre-select">Genre :</label>
            <select name="cbogenres[]" multiple>
                <?php
                $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

                $req = $bdd->prepare("select * from genre");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne!=null)
                {
                    echo ("<option value='$uneligne[nogenre]'>$uneligne[libgenre]</option>");
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
        //Génération de la première requête dans une variable en string avec uniquement le titre, les réalisateurs et acteurs
        $requete = ("select distinct nofilm, film.* from film natural join concerner where titre like'%".$_GET['txttitre']."%' and acteurs like'%".$_GET['txtact']."%'
                                 and realisateurs like'%".$_GET['txtreal']."%' ");
        //Si un public est renseigné, ajoute la recherche du public à la requête
        if(isset($_GET["cbopublic"]) == true) {
            $requete.= (" and nopublic='$_GET[cbopublic]' ");
        }
        // Pour chaques genres sélectionnés, ceux-ci sont rajoutés à la requête
        if(isset($_GET["cbogenres"]) == true) {
            $requete.= (" and nogenre IN (");
            for ($i=0;$i<count($_GET["cbogenres"]);$i++)  
            {
                $requete.= ($_GET["cbogenres"][$i].", ");
            }
            $requete = substr($requete, 0, -2).")";
        }
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
        $uneligne = $req->fetch();
        while ($uneligne!=null)
        {
            echo ("$uneligne[titre] $uneligne[realisateurs]<br/>");
            $uneligne = $req->fetch();
        }
        $req->closeCursor();
    }
    $bdd=null;

?>
</body>

</html>