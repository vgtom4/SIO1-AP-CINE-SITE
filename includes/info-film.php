<div name='info-film'>
    </br>
    <?php 
    // Vérification que le film a bien été sélectionné
    if (isset($_POST["nofilm"])){
        $erreur = false;

        //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
        $requete = ("select * from film natural join public where nofilm=:nofilm");
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->bindParam(':nofilm', $_POST["nofilm"], PDO::PARAM_INT);
        $req->execute();
        $uneligne = $req->fetch();

        if ($uneligne){
            // Recherche des genres du film
            $requete2 = ("select libgenre from genre natural join concerner where nofilm=:nofilm");
            // Préparation de la requête en utilisant la variable préparée auparavant
            $req2 = $bdd->prepare($requete2);
            $req2->bindParam(':nofilm', $_POST["nofilm"], PDO::PARAM_INT);
            $req2->execute();
            $genres = "";
            while ($uneligne2 = $req2->fetch()){
                $genres.=$uneligne2["libgenre"].", ";
            }
            if($genres) $genres = substr($genres, 0, -2);

            $titre = $uneligne["titre"];
            ?>

            <!-- Affichage des informations du film -->
            <table cellpadding='5'>
                <tr>
                <td rowspan='7'><img src='<?php echo $pageAdmin ? "../../assets/media/affiches/".$uneligne["imgaffiche"] : "assets/media/affiches/".$uneligne["imgaffiche"] ?>' width=200px></td>
                <td><h1><?php echo $uneligne["titre"]?></h1></td>
                </tr>
                <tr><td>Durée : <?php echo date('G\hi', strtotime($uneligne["duree"]))?></td></tr>
                <tr><td>Réalisateur(s) : <?php echo $uneligne["realisateurs"]?></td></tr>
                <tr><td>Acteur(s) : <?php echo $uneligne["acteurs"]?></td></tr>
                <?php if ($uneligne["infofilm"]) {?><tr><td>Informations : <?php echo $uneligne["infofilm"]?></td></tr><?php }?>
                <tr><td>Type de public : <?php echo $uneligne["libpublic"]?></td></tr>
                <tr><td>Genre(s) : <?php echo $genres?></td></tr>
                <tr><td colspan='2'></br><p class='font-weight-bold'>Synopsis :</p><?php echo $uneligne["synopsis"]?></td></tr>
            </table>
            <?php
        }else{?>
            Erreur : film inconnu
            <?php $erreur = true;
        }
        $req->closeCursor();
        
    }else{?>
        Erreur : problème de film sélectionné
        <?php $erreur = true;
    }
    ?>
</div>