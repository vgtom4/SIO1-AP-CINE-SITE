<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.ico">
    <title>Accueil</title>
</head>

<body>
    <form method="get" action="home.php">
        Titre : <input type="text" name="txttitre" /><br />
        Acteurs.trices : <input type="text" name="txtact" /><br />
        Réalisateurs.trices : <input type="text" name="txtreal" /><br />
        <select name="cbopublic">
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
        if(isset($_GET["txttitre"])==true) {
            $req = $bdd->prepare("select * from film where titre like='%".$_GET['txttitre']."%'");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                echo ($uneligne["titre"] . " " . $uneligne["realisateurs"] . "<br/>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
        }
        if(isset($_GET["txtact"])==true) {
            $req = $bdd->prepare("select * from film where acteurs like='%".$_GET['txtact']."%'");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                echo ($uneligne["titre"] . " " . $uneligne["realisateurs"] . "<br/>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
        }
        if(isset($_GET["txtreal"])==true) {
            $req = $bdd->prepare("select * from film where realisateurs like='%".$_GET['txtreal']."%'");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                echo ($uneligne["titre"] . " " . $uneligne["realisateurs"] . "<br/>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
        }
        if(isset($_GET["cbopublic"])==true) {
            $req = $bdd->prepare("select * from film where nopublic='".$_GET['cbopublic']."'");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                echo ($uneligne["titre"] . " " . $uneligne["realisateurs"] . "<br/>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
        }
        if(isset($_GET["txtgenr"])==true) {
            $req = $bdd->prepare("select * from genre where nogenre='".$_GET['cbogenre']."'");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                echo ($uneligne["titre"] . " " . $uneligne["realisateurs"] . "<br/>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
        }
    }
    $bdd=null;

?>
</body>

</html>