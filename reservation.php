<?php
// Connexion à la base de données et inclusion de l'entête
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<div class="reservation-info">
    <?php
    // Vérification que la projection a bien été sélectionnée
    if (isset($_POST["noproj"])){
        $erreur = false;

        //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
        $requete = ("select * from projection natural join film natural join salle where noproj=:noproj");
        $req = $bdd->prepare($requete);
        $req->bindParam(':noproj', $_POST["noproj"], PDO::PARAM_INT);
        $req->execute();
        $uneligne = $req->fetch();

        // Affichage des informations de la projection
        if ($uneligne){
            $date = date('l j F Y', strtotime($uneligne["dateproj"])) ?>
            <h1>Réservation pour le film : <?php echo $uneligne["titre"]?></h1>
            <table cellpadding='5'>
                <tr>
                    <td rowspan='7'><img src='assets/media/affiches/<?php echo $uneligne["imgaffiche"]?>' width=100px></td>
                    <td>Date : <?php echo $date?></td>
                </tr>
                <tr><td>Horaire : <?php echo date('G\hi', strtotime($uneligne["heureproj"]))?></td></tr>
                <tr><td>Salle <?php echo $uneligne["nosalle"]?></td></tr>
                <tr><td>Informations : <?php echo $uneligne["infoproj"]?></td></tr>
            </table>
        <?php }else{ ?>
            <h1>Erreur : projection inconnue</h1>
            <?php $erreur = true;
        }
        $req->closeCursor();
    }else{?>
        <h1>Erreur : problème de projection sélectionnée</h1>
        <?php $erreur = true;
    }?>
</div>

<!-- Affichage du formulaire de réservation -->
<div class="reservation-form">
    <?php if (!$erreur){
        if(isset($_POST["btnvalider"])) {
            if(!isset($_POST["confirmResa"])){
                // Définir les informations de réservation
                $datetime_reservation = date("Y-m-d H:i:s");
                $num_projection = $_POST["noproj"];
                $date_projection = $uneligne["dateproj"];
                $horaire_projection = $uneligne["heureproj"];
                $salle_projection = $uneligne["nosalle"];
                $titre_film = $uneligne["titre"];
                $pseudo_client = $_POST["txtpseudo"];
                $motdepasse = bin2hex(random_bytes(3));
                $nbplaceresa = $_POST["nbplaceresa"];
                
                // Insertion de la réservation dans la base de données
                $requete = "INSERT INTO reservation (mdpresa, dateresa, nomclient, nbplacesresa, noproj) VALUES (:motdepasse, NOW(), :nomclient, :nbplacesresa, :noproj)";
                $req = $bdd->prepare($requete);
                $req->bindParam(':motdepasse', $motdepasse, PDO::PARAM_STR);
                $req->bindParam(':nomclient', $_POST['txtpseudo'], PDO::PARAM_STR);
                $req->bindParam(':nbplacesresa', $_POST['nbplaceresa'], PDO::PARAM_INT);
                $req->bindParam(':noproj', $_POST['noproj'], PDO::PARAM_INT);
                $req->execute();
                $req->closeCursor();

                // Récupération du numéro de réservation
                $lastNoResa = $bdd->lastInsertId();
                $num_reservation = $lastNoResa;

                // Inclure la bibliothèque PHP QR Code
                include("assets/utils/phpqrcode/qrlib.php");

                // Concaténation des informations de réservations
                $code_texte = "$num_reservation;$datetime_reservation;$num_projection;$date_projection;$horaire_projection;$titre_film;$salle_projection;$pseudo_client;$nbplaceresa";
                
                // Génération du qrcode
                QRcode::png($code_texte, "assets/media/qrcode/qrcode.png");?>

                <!-- Formulaire cacher de confirmation de réservation pour contrer l'actualisation (réexecution de l'insertion en cas d'actualisation) -->
                <form method="post" id="reservation_form_confirm_<?php echo $num_reservation ?>">
                    <input type="hidden" name="noproj" value="<?php echo $num_projection ?>">
                    <input type="hidden" name="noresa" value="<?php echo $num_reservation ?>">
                    <input type="hidden" name="txtpseudo" value="<?php echo urlencode($pseudo_client) ?>">
                    <input type="hidden" name="mdp" value="<?php echo $motdepasse ?>">
                    <input type="hidden" name="nbplaceresa" value="<?php echo $nbplaceresa ?>">
                    <input type="hidden" name="confirmResa" value="true">
                    <input type="hidden" name="btnvalider" value="true">
                </form>
                <script>document.getElementById("reservation_form_confirm_<?php echo $num_reservation ?>").submit();</script>

            <?php }else{

            // Récupération des informations de réservation
            $num_reservation = $_POST["noresa"];
            $pseudo_client = $_POST["txtpseudo"];
            $motdepasse = $_POST["mdp"];
            $nbplaceresa = $_POST["nbplaceresa"];?>

            <!-- Affichage des informations de réservation -->
            <h2>Réservation effectuée</h2>
            Client : <?php echo $pseudo_client?>
            </br>Mot de passe : <?php echo $motdepasse?> <i>(Attention, vous en aurez besoin pour valider votre passage en caisse avec le QRCode!)</i>
            </br>Nombre de place réservée : <?php echo $nbplaceresa?></br>

            </br><h3>Voici votre QR code de réservation</h3>

            <!-- Afficher le QR code sur votre page PHP -->
            <img src='assets/media/qrcode/qrcode.png' alt='QR code'>

            <!-- Ajouter un bouton de téléchargement pour le QR code -->
            </br><a href='assets/media/qrcode/qrcode.png' download='Reservation<?php echo $num_reservation?>.png'>Télécharger votre QR code</a>
            
            </br></br><a href='home.php'>Retour à l'accueil</a>
            <?php }
        }else{
            // Recherche du nombre de place restante pour la projection sélectionnée
            $requete = ("select (select nbplaces from salle natural join projection where noproj=:noproj) - COALESCE((select sum(nbplacesresa) from reservation where noproj=:noproj),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=:noproj");
            $req = $bdd->prepare($requete);
            $req->bindParam(':noproj', $_POST["noproj"], PDO::PARAM_INT);
            $req->execute();
            $uneligne = $req->fetch();
            
            // Si le nombre de place restante est supérieur à 0, afficher le formulaire de réservation
            if ($uneligne["nbplacerestante"]>0){?>
                </br><h2>Formulaire de réservation</h2>
                <!-- Formulaire de réservation -->
                <form method='POST' action='reservation.php'>
                    <input type='hidden' name='noproj' value='<?php echo $_POST["noproj"]?>'>
                    Indiquez le nombre de place à réserver : <input type='number' name='nbplaceresa' min='1' max='<?php echo $uneligne["nbplacerestante"]?>' value='1' required>
                    (place(s) disponible(s) : <?php echo $uneligne["nbplacerestante"]?> / <?php echo $uneligne["nbplaces"]?>)</br>
                    Pseudo :<input type='text' name='txtpseudo' placeholder='Saisir pseudo' required></br>
                    <input type='submit' name='btnvalider' value='Réserver'>
                </form>
            <?php }else{ ?>
                <h1>Séance complète.</h1>
                <h1>Aucune place disponible.</h1>
                <img src='https://media.giphy.com/media/xX0rXi3iWNd0qpWsXq/giphy.gif'>
            <?php }
            $req->closeCursor();
        }
    }
    ?>
</div>

<?php if ($erreur) echo ("</br></br><a href='home.php'>Retour à l'accueil</a>") ?>

<?php
include("includes/deconnexion.php");
include("includes/pagepied.php");
?>