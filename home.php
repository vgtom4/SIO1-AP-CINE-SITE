<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/media/logo.png">
    <title>Accueil</title>
</head>

<body>
    <form method="get" action="home.php">
        Titre : <input type="text" name="txttitre" /><br />
        Acteurs.trices : <input type="text" name="txtact" /><br />
        Réalisateurs.trices : <input type="text" name="txtreal" /><br />
        <select name="cbopublic">
            <option></option>
            <?php
                $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

                $req = $bdd->prepare("select * from public");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne!=null)
                {
                    echo ("<option value=".$uneligne['nopublic'].">".$uneligne['libpublic']."</option>");
                    $uneligne = $req->fetch();

                }
                
                $req->closeCursor();
                $bdd=null;
             ?>
        </select> <br />
        <select name="cbogenres">
            <option></option>

            <?php
                $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");

                $req = $bdd->prepare("select * from genre");
                $req->execute();
                $uneligne = $req->fetch();
                while ($uneligne!=null)
                {
                    echo ("<option value=".$uneligne['nogenre'].">".$uneligne['libgenre']."</option>");
                    $uneligne = $req->fetch();

                }
                
                $req->closeCursor();
                $bdd=null;
             ?>
        </select> <br />


        <input type="submit" name="btnvalider" value="Rechercher">
    </form>
    <?php 
    $bdd = new PDO("mysql:host=localhost;dbname=bdcinevieillard-lepers;charset=utf8", "root", "");           
    if(isset($_GET["btnvalider"])==true) {
        //Génération de la première requête dans une variable en string avec uniquement le titre, les réalisateurs et acteurs
        $requete = ("select distinct nofilm, film.* from film natural join concerner where titre like'%".$_GET['txttitre']."%' and acteurs like'%".$_GET['txtact']."%'
                                 and realisateurs like'%".$_GET['txtreal']."%' ");
        //Si un public est renseigné, ajoute la recherche du public à la requête
        if($_GET["cbopublic"]>=1) {
            $requete.= (" and nopublic='".$_GET['cbopublic']."' ");
        }
        //Si un genre est renseigné, ajoute la recherche du genre à la requête
        if($_GET["cbogenres"]>=1) {
            $requete.= (" and nogenre='".$_GET['cbogenres']."' ");
        }
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
        $uneligne = $req->fetch();
        while ($uneligne!=null)
        {
            echo ($uneligne["titre"] . " " . $uneligne["realisateurs"] . "<br/>");
            $uneligne = $req->fetch();
        }
        $req->closeCursor();
    }
    $bdd=null;

?>
</body>

</html>