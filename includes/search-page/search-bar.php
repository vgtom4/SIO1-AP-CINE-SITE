<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag/dist/css/multi-select-tag.css">
<div class="search-bar">
    <!-- Affichage du formulaire de recherche -->
    <form method="POST">
        <!-- Input pour le titre -->
        <label for="title-input">Titre :</label>
        <input type="text" name="txttitre" value="<?php echo isset($_POST["txttitre"]) ? $_POST['txttitre'] : "" ?>"/>
        
        <!-- Input pour l'acteur -->
        <label for="actor-input">Acteur :</label>
        <input type="text" name="txtact" value="<?php echo isset($_POST["txtact"]) ? $_POST['txtact'] : "" ?>"/>

        <!-- Input pour le réalisateur -->
        <label for="director-input">Réalisateur :</label>
        <input type="text" name="txtreal" value="<?php echo isset($_POST["txtreal"]) ? $_POST['txtreal'] : "" ?>"/>

        <!-- Input pour le type de public -->
        <label for="public-select">Public :</label>
        <select name="cbopublic">
            <option value="" selected>Sélectionner un public</option>
            <?php
            // Requête pour récupérer les types de public
            $req = $bdd->prepare("select * from public");
            $req->execute();
            $uneligne = $req->fetch();

            // Boucle pour afficher les types de public dans le select "cbopublic"
            while ($uneligne)
            {
                echo ("<option value=$uneligne[nopublic] ".(isset($_POST["cbopublic"]) && $_POST["cbopublic"]==$uneligne["nopublic"] ? 'selected' : null).">$uneligne[libpublic]</option>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
            ?>
        </select>

        <!-- Input pour le genre -->
        <label for="genre-select">Genre :</label>
        <select id="cbogenres" name="cbogenres[]" multiple>
            <?php
            // Requête pour récupérer les genres
            $req = $bdd->prepare("select * from genre");
            $req->execute();
            $uneligne = $req->fetch();

            // Boucle pour afficher les genres dans le select "cbogenres"
            while ($uneligne)
            {
                if(isset($_POST["cbogenres"])){
                    $selected = false;
                    // Boucle pour vérifier si le genre a été sélectionné par l'utilisateur
                    // Si oui, on le sélectionne
                    for ($i=0;$i<count($_POST["cbogenres"]);$i++)  
                    {
                        if($_POST["cbogenres"][$i] == $uneligne["nogenre"]) $selected = true;
                    }
                }
                echo ("<option value='$uneligne[nogenre]' ".($selected ? 'selected' : null).">$uneligne[libgenre]</option>");
                $uneligne = $req->fetch();
            }
            $req->closeCursor();
            ?>
        </select>
        <input type="submit" name="btnvalider" value="Rechercher">
    </form>
    <!-- Bouton pour réinitialiser les champs du formulaire -->
    <form method="POST">
        <input type="submit" name="btnreset" title="Réinitialiser la recherche" value="X">
    </form>
</div>
<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag/dist/js/multi-select-tag.js"></script>
<script>
    new MultiSelectTag('cbogenres')  // id
</script>