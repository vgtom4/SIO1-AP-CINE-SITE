<?php
// Initialisation des variables de recherche
$titre = isset($_POST["txttitre"]) ? addslashes($_POST['txttitre']) : "";
$acteurs = isset($_POST["txtact"]) ? addslashes($_POST['txtact']) : "";
$realisateurs = isset($_POST["txtreal"]) ? addslashes($_POST['txtreal']) : "";

// Requête pour récupérer les films correspondant aux critères de recherche
$requete = ("select distinct film.* from film natural join concerner where titre like '%$titre%' 
                                                                        and acteurs like '%$acteurs%'
                                                                        and realisateurs like '%$realisateurs%'");

// Si un public est renseigné, ajout du paramètre "public" à la requête
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

// Execution de la requête
$req = $bdd->prepare($requete);
$req->execute();
$uneligne = $req->fetch();
?>