<?php
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<div class="search-bar">
    <form method="POST" action="home.php">
        <label for="title-input">Titre :</label>
        <input type="text" name="txttitre" value="<?php echo isset($_POST["txttitre"]) ? $_POST['txttitre'] : "" ?>"/>
        
        <label for="actor-input">Acteur :</label>
        <input type="text" name="txtact" value="<?php echo isset($_POST["txtact"]) ? $_POST['txtact'] : "" ?>"/>

        <label for="director-input">Réalisateur :</label>
        <input type="text" name="txtreal" value="<?php echo isset($_POST["txtreal"]) ? $_POST['txtreal'] : "" ?>"/>

        <label for="public-select">Public :</label>
        <select name="cbopublic">
            <option value="" selected>Sélectionner un public</option>
            <?php

            $req = $bdd->prepare("select * from public");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                if (isset($_POST["cbopublic"])==true && $_POST["cbopublic"]==$uneligne["nopublic"]){
                    echo ("<option value=$uneligne[nopublic] selected>$uneligne[libpublic]</option>");
                }
                else 
                {
                    echo ("<option value=$uneligne[nopublic]>$uneligne[libpublic]</option>");
                }
                $uneligne = $req->fetch();

            }
            
            $req->closeCursor();
            ?>
        </select>
        <label for="genre-select">Genre :</label>
        <select name="cbogenres[]" multiple>
            
            <?php
            $req = $bdd->prepare("select * from genre");
            $req->execute();
            $uneligne = $req->fetch();
            while ($uneligne!=null)
            {
                echo ("<option value='$uneligne[nogenre]'>$uneligne[libgenre]</option>");
                $uneligne = $req->fetch();

            }
            
            $req->closeCursor();
            ?>
        </select>
        <input type="submit" name="btnvalider" value="Rechercher">
    </form>
</div>
<?php        
//Génération de la première requête dans une variable en string avec uniquement le titre, les réalisateurs et acteurs
$titre = isset($_POST["txttitre"]) ? addslashes($_POST['txttitre']) : "";
$acteurs = isset($_POST["txtact"]) ? addslashes($_POST['txtact']) : "";
$realisateurs = isset($_POST["txtreal"]) ? addslashes($_POST['txtreal']) : "";

$requete = ("select distinct film.* from film natural join concerner where titre like\"%$titre%\" and acteurs like\"%$acteurs%\"
                            and realisateurs like\"%$realisateurs%\" ");
//Si un public est renseigné, ajoute la recherche du public à la requête
if(isset($_POST["cbopublic"]) == true && $_POST["cbopublic"] != "") {
    $requete.= (" and nopublic='$_POST[cbopublic]' ");
}
// Pour chaques genres sélectionnés, ceux-ci sont rajoutés à la requête
if(isset($_POST["cbogenres"]) == true && $_POST["cbogenres"] != "") {
    $requete.= (" and nogenre IN (");
    for ($i=0;$i<count($_POST["cbogenres"]);$i++)  
    {
        $requete.= ($_POST["cbogenres"][$i].", ");
    }
    $requete = substr($requete, 0, -2).")";
}
// Préparation de la requête en utilisant la variable préparée auparavant
$req = $bdd->prepare($requete);
$req->execute();
// Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
$uneligne = $req->fetch();

if (!$uneligne){
    echo "Aucun film ne correspond à votre recherche";
}
while ($uneligne)
{
    echo "<label>";
    echo "<table cellpadding='5' onclick=\"document.getElementById('form_$uneligne[nofilm]').submit()\">";
    echo "<tr>";
    echo "<td rowspan='7'><img src='assets/media/affiches/$uneligne[imgaffiche]' width=100px></td>";
    echo "<td>$uneligne[titre]</td>";
    echo "</tr>";
    echo "<tr><td>Durée : ".str_replace(":","h",date('G:i', strtotime($uneligne["duree"])))."</td></tr>";
    echo "<tr><td>$uneligne[realisateurs]</td></tr>";
    echo "<tr><td>$uneligne[acteurs]</td></tr>";
    echo "</table>";
    echo "<form id='form_$uneligne[nofilm]' method='post' action='film.php'>";
    echo "<input type='hidden' name='nofilm' value='$uneligne[nofilm]'>";
    echo "<input type='hidden' name='titre' value='".urlencode($uneligne["titre"])."'>";
    echo "</form>";
    echo "</label>";
    $uneligne = $req->fetch();
}
$req->closeCursor();

include("includes/deconnexion.php");
include("includes/pagepied.php");
?>