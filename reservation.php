<?php
include("includes/connexion.php");
include("includes/pageentete.php");
?>

<div class="reservation-info">
    <?php
    if (isset($_POST["noproj"])){
        $erreur = false;

        //Génération de la requête qui va chercher dans la DB les projections correspondant au film renseigné
        $requete = ("select * from projection natural join film natural join salle where noproj=$_POST[noproj]");
        // Préparation de la requête en utilisant la variable préparée auparavant
        $req = $bdd->prepare($requete);
        $req->execute();
        // Recherche pour chaque film de la base de données si ses caractéristiques correspondent à celles recherchées
        $uneligne = $req->fetch();

        if ($uneligne){
            echo "<h1>Réservation pour le film : $uneligne[titre]</h1>";
            echo "<table cellpadding='5'>";
                echo "<tr>";
                echo "<td rowspan='7'><img src='assets/media/affiches/$uneligne[imgaffiche]' width=100px></td>";
                $date = date('l j F Y', strtotime($uneligne["dateproj"]));
                echo "<td>Date : $date</td>";
                echo "</tr>";
                echo "<tr><td>Horaire : ".str_replace(":","h",date('G:i', strtotime($uneligne["heureproj"])))."</td></tr>";
                echo "<tr><td>Salle $uneligne[nosalle]</td></tr>";
                echo "<tr><td>$uneligne[infoproj]</td></tr>";
            echo "</table>";
        }else{
            echo "Erreur : projection inconnue";
            $erreur = true;
        }

        $req->closeCursor();
    }else{
        echo "</br>Erreur : problème de projection sélectionnée";
        $erreur = true;
    }
    ?>
</div>

<div class="reservation-form">
    <?php
    if(isset($_POST["btnvalider"])) {
        // Requête sql pour insérer la réservation
        $requete3 = ("insert into reservation (mdpresa, dateresa, nomclient, nbplacesresa, noproj) values ('$_POST[txtpwd]', now(), '$_POST[txtpseudo]', '$_POST[nbplaceresa]', '$_POST[noproj]')");
        $req3 = $bdd->prepare($requete3);
        $req3->execute();
        $reservation=true;
    }else{
        $reservation=false;
    }
    
    if ($reservation==false){
        if (isset($_POST["noproj"]) && $erreur == false){
            $requete2 = ("select (select nbplaces from salle natural join projection where noproj=$_POST[noproj]) - COALESCE((select sum(nbplacesresa) from reservation where noproj=$_POST[noproj]),0) as nbplacerestante, nbplaces from salle natural join projection where noproj=$_POST[noproj]");
            $req2 = $bdd->prepare($requete2);
            $req2->execute();
            $uneligne2 = $req2->fetch();
            
            if ($uneligne2["nbplacerestante"]>0){
                echo "<form method='POST' action='reservation.php'>";
                    echo "<input type='hidden' name='noproj' value='$_POST[noproj]'>";
                    echo "Indiquez le nombre de place à réserver : <input type='number' name='nbplaceresa' min='1' max='$uneligne2[nbplacerestante]' value='1' required>";
                    echo "(place(s) disponible(s) : $uneligne2[nbplacerestante] / $uneligne2[nbplaces])</br>";
                    echo "Pseudo :<input type='text' name='txtpseudo' placeholder='Saisir pseudo' required></br>";
                    echo "Mot de passe : <input type='password' name='txtpwd' placeholder='Saisir mot de passe' value='".bin2hex(openssl_random_pseudo_bytes(3))."' required></br>";
                    echo "<input type='checkbox' name='check' required>J'accepte de me faire voler mes données.</br>";
                    echo "<input type='checkbox' name='check' required>J'accepte de me faire frapper par l'État.</br>";
                    echo "<a href='https://www.youtube.com/watch?v=dQw4w9WgXcQ'>Conditions d'utilisation</a>";
                    echo "<a href='https://www.youtube.com/watch?v=dQw4w9WgXcQ'>Politique de confidentialité</a>";
                    echo "<input type='submit' name='btnvalider' value='Reserver'>";
                echo "</form>";
            }else{
                echo ("<h1>Séance complète.</h1>");
                echo ("<h1>Aucune place disponible.</h1>");
                echo ("<img src='https://media.giphy.com/media/xX0rXi3iWNd0qpWsXq/giphy.gif'>");
            }
        }
    }else{
        echo "<h2>Réservation effectuée</h2>";
        echo "Client : $_POST[txtpseudo]";
        echo "</br>Nombre de place réservée : $_POST[nbplaceresa]</br>";

        $requete4 = ("select max(noresa) as noresa from reservation");
        $req4 = $bdd->prepare($requete4);
        $req4->execute();
        $noResa = $req4->fetchColumn();

        // Inclure la bibliothèque PHP QR Code
        require_once "assets/utils/phpqrcode/qrlib.php";

        // Définir les informations de réservation
        $num_reservation = $noResa;
        $datetime_reservation = date("Y-m-d H:i:s");
        $num_projection = $_POST["noproj"];
        $date_projection = $uneligne["dateproj"];
        $horaire_projection = $uneligne["heureproj"];
        $salle_projection = $uneligne["nosalle"];
        $titre_film = $uneligne["titre"];
        $pseudo_client = $_POST["txtpseudo"];
        $nbplaceresa = $_POST["nbplaceresa"];

        // Concaténer les informations en une seule chaîne de caractères
        $code_texte = "$num_reservation;$datetime_reservation;$num_projection;$date_projection;$horaire_projection;$titre_film;$salle_projection;$pseudo_client;$nbplaceresa";
        
        // Générer le QR code en tant que fichier temporaire
        $temp_file = tempnam(sys_get_temp_dir(), 'qr_');
        
        QRcode::png($code_texte, "assets/media/qrcode/Reservation".$num_reservation.".png", QR_ECLEVEL_L);

        echo "<h3>Voici votre QR code de réservation</h3>";
        // Afficher le QR code sur votre page PHP
        echo "<img src='assets/media/qrcode/Reservation$num_reservation.png' alt='QR code'>";

        // Ajouter un bouton de téléchargement pour le QR code
        echo "</br><a href='assets/media/qrcode/Reservation$num_reservation.png' download='Reservation$num_reservation.png'>Télécharger le QR code</a>";
        
        echo "</br></br><a href='home.php'>Retour à l'accueil</a>";
        $reservation=false;
    }
    ?>
    
</div>

<?php
include("includes/deconnexion.php");
include("includes/pagepied.php");
?>