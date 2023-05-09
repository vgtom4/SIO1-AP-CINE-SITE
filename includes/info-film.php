<div class='info-film'>
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
            <div class='info'>
                <img src='<?php echo $pageAdmin ? "../../assets/media/affiches/".$uneligne["imgaffiche"] : "assets/media/affiches/".$uneligne["imgaffiche"] ?>' width=200px>
                <h1><?php echo $uneligne["titre"]?></h1>
                
                <label><i>Durée : </i><?php echo date('G\hi', strtotime($uneligne["duree"]))?></label></br>
                </br><label><i>Réalisateur(s) : </i><?php echo $uneligne["realisateurs"]?></label></br>
                </br><label><i>Acteur(s) : </i><?php echo $uneligne["acteurs"]?></label></br>
                <?php if ($uneligne["infofilm"]) {?></br><label><i>Informations : </i><?php echo $uneligne["infofilm"]?><?php }?></label></br>
                </br><label><i>Type de public : </i><?php echo $uneligne["libpublic"]?></label></br>
                </br><label><i>Genre(s) : </i><?php echo $genres?></label>
            </div>
            </br><label><p><i>Synopsis :</i></p><?php echo $uneligne["synopsis"]?></label>
            
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