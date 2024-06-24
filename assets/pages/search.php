<?php

require_once("connexion.php");
$link = "../";
$script = "";
$pages = "";
$home = "../../";

if ($_GET['search'] == "") {
    header("Location: ".$_SERVER['HTTP_REFERER']);
}

if ($_COOKIE == null) {
    include_once('cookiesBanner.php');
}

$title = "Résultats pour ". $_GET['search']. " - Wild";
include_once('header.php');

    if (!empty($_GET['search']) || !empty($_GET['submit']) ) { // si les champs sont remplis, alors on execute le sql

        $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY fleur.id_fleur DESC";
        $query = $db->prepare($sql);
        $query->execute();
        $contents = $query->fetchAll();

        $sqlClients =  "SELECT id_client, nom, prenom, telephone, email, username, gender, adresse, code_postal, ville FROM client";
        $queryClients = $db->prepare($sqlClients);
        $queryClients->execute();
        $contentsClients = $queryClients->fetchAll();

        $sqlFournisseurs =  "SELECT * FROM fournisseur";
        $queryFournisseurs = $db->prepare($sqlFournisseurs);
        $queryFournisseurs->execute();
        $contentsFournisseurs = $queryFournisseurs->fetchAll();

        $sqlCommande =  "SELECT id_commande, nom, prenom FROM commande
                        INNER JOIN client ON client.id_client = commande.id_client";
        $queryCommande = $db->prepare($sqlCommande);
        $queryCommande->execute();
        $contentsCommande = $queryCommande->fetchAll();

?>

    <section class='searchResult'>
    <?php

        // en fonction de l'element recherché on affiche l'information qui correcpond à la recherche.

        if (isset($_COOKIE['user'])) {

            $username = $_COOKIE['user'];

            $sqlClients = "SELECT username, admin FROM client
            WHERE username = :username";
            $queryClients = $db->prepare($sqlClients);
            $queryClients->execute([
            "username" => $username
            ]);

            $clients = $queryClients->fetch();

            if ($clients['admin'] === '1') { ?>

                <h1 class="searchResultsTitle">Résultat de la commande numéro : <?= $_GET['search'] ?></h1>
                <main class="mainSearch">
                    <div class="searchCommand">

                    <?php $resultsCommande = 0;

                    foreach ($contentsCommande as $commande) { // pour chaque info renseigné dans la barre de recherche, on sanitise puis ça affiche
                        $searchTrim = trim($_GET['search']);
                        $search = strtolower($searchTrim);
                        $strcommande = strtolower($commande['id_commande']);

                        if (strpos($strcommande, $search) !== false) {

                            $resultsCommande++;
                            $num_commande = $commande['id_commande']; ?>
                            <p class="mainFournisseursCouleur">Commande <a href="clients/commandeClient.php?commandeNumber=<?= $num_commande ?>"><?= $num_commande ?></a> du client <?= $commande['nom']. " ". $commande['prenom']; ?></p>
                        <?php }
                    } ?>

                <?php if ($resultsCommande <= 0) {
                    echo "<p>Il n'existe aucune commande contenant ". $_GET['search']. "</p>";
                } ?>

                    </div>
                </main>

            <h1 class="searchResultsTitle">Résultat des Fournisseurs pour : <?= $_GET['search'] ?></h1>
            <main class="mainSearch">

                <?php $resultsFournisseur = 0;

                foreach ($contentsFournisseurs as $contentsFournisseur) { // pour chaque info renseigné dans la barre de recherche, on sanitise puis ça affiche
                    $searchTrim = trim($_GET['search']);
                    $search = strtolower($searchTrim);
                    $strraison_social = strtolower($contentsFournisseur['raison_social']);
                    $strprenom = strtolower($contentsFournisseur['prenom']);
                    $strtelephone = strtolower($contentsFournisseur['telephone']);
                    $strnom = strtolower($contentsFournisseur['nom']);

                    if (strpos($strraison_social, $search) !== false || strpos($strprenom, $search) !== false || strpos($strtelephone, $search) !== false || strpos($strnom, $search) !== false) { ?>

                        <!-- Edit -->

                        <section class="searchCommand">
                            <?php $id_fournisseur = $contentsFournisseur['id_fournisseur']; ?>

                            <form method="POST" action="fournisseurs/fournisseurs.php">
                                <input type="hidden" name="id" value="<?=$id_fournisseur?>">
                                <div class="cardFournisseurContainer">
                                    <div class="cardFournisseurFormContainer">
                                        <label for="raison_social" class="cardFournisseurLabel">Raison Social *</label>
                                        <input type="text" name="raison_social" value="<?= $contentsFournisseur['raison_social']; ?>" placeholder="Fournisseur" class="cardFournisseurInput" required>
                                    </div>
                                    <div class="cardFournisseurFormContainer">
                                        <label for="telephone" class="cardFournisseurLabel">Téléphone *</label>
                                        <input type="tel" name="telephone" value="<?= $contentsFournisseur['telephone']; ?>" placeholder="Téléphone" class="cardFournisseurInput" required>
                                    </div>
                                    <div class="cardFournisseurFormContainer">
                                        <label for="nom" class="cardFournisseurLabel">Nom</label>
                                        <input type="text" name="nom" value="<?= $contentsFournisseur['nom']; ?>" placeholder="Nom" class="cardFournisseurInput">
                                    </div>
                                    <div class="cardFournisseurFormContainer">
                                        <label for="prenom" class="cardFournisseurLabel">Prénom</label>
                                        <input type="text" name="prenom" value="<?= $contentsFournisseur['prenom']; ?>" placeholder="Prénom" class="cardFournisseurInput">
                                    </div>
                                </div>
                                <div class="cardFournisseurAdmin">
                                    <input type="submit" name="submitEdit" value="Modifier"  class='mainFournisseursEdit'>
                                    <button type="button" id="deleteFournisseurFirstForm" class="mainFournisseursDelete" onclick="showDeleteForm(this)">Supprimer</button>
                                </div>
                            </form>

                            <!-- Delete -->

                            <form method="POST" class="deleteClientFinalForm" id="deleteFournisseurFinalForm" action="fournisseurs/fournisseurs.php">
                                <input type="hidden" name="id" value="<?= $id_fournisseur; ?>" >
                                <label for="password">Afin de supprimer définitivement votre compte, merci de saisir votre mot de passe </label>
                                <input type="password" name="password" placeholder="Mot de passe" class="cardFournisseurInputPasswordDelete">
                                <input type="submit" name="submitDelete" value="Supprimer" class="mainFournisseursDeleteConfirmation">
                            </form>

                        </section>
                        <?php $resultsCommande++;
                    }
                }

                if ($resultsCommande <= 0) {
                    echo "<div class='searchCommand'><p>Il n'existe aucun fournisseur contenant ". $_GET['search']. "</p></div>";
                } ?>

                </main>

                <h1 class="searchResultsTitle">Résultat des Clients pour : <?= $_GET['search'] ?></h1>
                <main class="mainSearch">

                    <?php $resultsClient = 0;
                    foreach ($contentsClients as $contentsclient) { // pour chaque info renseigné dans la barre de recherche, on sanitise puis ça affiche
                        $searchTrim = trim($_GET['search']);
                        $search = strtolower($searchTrim);
                        $strnom = strtolower($contentsclient['nom']);
                        $strprenom = strtolower($contentsclient['prenom']);
                        $strtelephone = strtolower($contentsclient['telephone']);
                        $strmail = strtolower($contentsclient['email']);
                        $strusername = strtolower($contentsclient['username']);

                        if (strpos($strnom, $search) !== false || strpos($strprenom, $search) !== false || strpos($strtelephone, $search) !== false || strpos($strmail, $search) !== false
                        || strpos($strusername, $search) !== false) { ?>

                            <div class="searchCommand" id="cardClientContainerRightEdit">
                                <?php $id_client = $contentsclient['id_client']; ?>
                                <form method="POST" action="clients/clients.php">
                                    <input type="hidden" name="id" value="<?=$id_client?>">

                                    <div class="mainClientCardInputGender">
                                        <input type="radio" name="gender" value="M" <?php if ($contentsclient['gender'] === 'M') { echo "checked"; } ?> required>M
                                        <input type="radio" name="gender" value="Mme" <?php if ($contentsclient['gender'] === 'Mme') { echo "checked"; } ?> required>Mme
                                        <input type="radio" name="gender" value="Autre" <?php if ($contentsclient['gender'] === 'Autre') { echo "checked"; } ?> required>Autre
                                    </div>
                                    <div class="cardClientFormContainerContent">
                                        <div class="cardClientFormContainer">
                                            <label for="nom" class="cardFournisseurLabel">Nom *</label>
                                            <input type="text" name="nom" id="nom" placeholder="Nom *" class="cardFournisseurInput" value="<?= $contentsclient['nom']; ?>" required>
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="prenom" class="cardFournisseurLabel">Prénom *</label>
                                            <input type="text" name="prenom" id="prenom" placeholder="Prénom *" class="cardFournisseurInput" value="<?= $contentsclient['prenom']; ?>" required>
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="username" class="cardFournisseurLabel">Username *</label>
                                            <input type="text" name="username" id="username" placeholder="Nom d'utilisateur *" class="cardFournisseurInput" value="<?= $contentsclient['username']; ?>" required>
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="username" class="cardFournisseurLabel">Email *</label>
                                            <input type="email" name="email" id="email" placeholder="exemple@contact.fr *" class="cardFournisseurInput" value="<?= $contentsclient['email']; ?>" required>
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="telephone" class="cardFournisseurLabel">Téléphone *</label>
                                            <input type="tel" name="telephone" id="telephone" placeholder="Téléphone *" class="cardFournisseurInput" value="<?= $contentsclient['telephone']; ?>" required>
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="adresse" class="cardFournisseurLabel">Adresse</label>
                                            <input type="text" name="adresse" id="adresse" placeholder="Adresse" class="cardFournisseurInput" value="<?= $contentsclient['adresse']; ?>">
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="code_postal" class="cardFournisseurLabel">Code Postal</label>
                                            <input type="number" name="code_postal" id="code_postal" placeholder="Code Postal" class="cardFournisseurInput" value="<?= $contentsclient['code_postal']; ?>">
                                        </div>
                                        <div class="cardClientFormContainer">
                                            <label for="ville" class="cardFournisseurLabel">Ville</label>
                                            <input type="text" name="ville" id="ville" placeholder="Ville" class="cardFournisseurInput" value="<?= $contentsclient['ville']; ?>">
                                        </div>
                                    </div>
                                    <div class="allClientsEdit">
                                        <input type="submit" name="submitEdit" value="Modifier"  class='mainFournisseursEdit'>
                                        <button type="button" id="deleteFournisseurFirstForm" class="mainFournisseursDelete" onclick="showDeleteForm(this)">Supprimer</button>
                                    </div>
                                </form>

                                <form method="POST" class="deleteClientFinalForm" id="cardClientsContainerRightDelete" action="clients/clients.php">
                                    <input type="hidden" name="id" value="<?= $id_client; ?>" >
                                    <label for="password">Afin de supprimer définitivement votre compte, merci de saisir votre mot de passe </label>
                                    <input type="password" name="password" placeholder="Mot de passe" class="cardFournisseurInputPasswordDelete">
                                    <input type="submit" name="submitDelete" value="Supprimer" class="mainFournisseursDeleteConfirmation">
                                </form>
                            </div>

                            <?php $resultsClient++;
                        }
                    }
                    if ($resultsClient <= 0) {
                        echo "<div class='searchCommand'><p>Il n'existe aucun client contenant ". $_GET['search']. "</p></div>";
                    }
            } ?>
        </main> <?php
    }
}

// en fonction de l'element recherché on affiche l'information qui correcpond à la recherche.

if (isset($_COOKIE['user'])) {

    $username = $_COOKIE['user'];

    $sqlClients = "SELECT username, admin FROM client
    WHERE username = :username";
    $queryClients = $db->prepare($sqlClients);
    $queryClients->execute([
        "username" => $username
    ]);

    $clients = $queryClients->fetch();

    if ($clients['admin'] === '1') { ?>

    <h1 class="searchResultsTitle">Résultat des Fleurs pour : <?= $_GET['search'] ?></h1>
    <main class="mainFleurs">

        <?php $resultsFleurs = 0;
        foreach ($contents as $content) { // pour chaque info renseigné dans la barre de recherche, on sanitise puis ça affiche
                $searchTrim = trim($_GET['search']);
                $search = strtolower($searchTrim);
                $strcouleur = strtolower($content['couleur']);
                $strvariete = strtolower($content['variete']);
                $strprix = strtolower($content['prix']);
                $strstock = strtolower($content['stock']);
            if (strpos($strcouleur, $search) !== false || strpos($strvariete, $search) !== false || strpos($strprix, $search) !== false || strpos($strstock, $search) !== false) { ?>

                <?php

                    $strimg = substr($content['img'], 3);
                    $id_fleur = $content['id_fleur']; ?>
                    <div class="cardFleurs" id="cardFleurs">
                        <a href="fleurs/seeFleur.php?variete=<?=$content['variete']."&couleur=".$content['couleur']?>" class="cardFleursLink">
                            <div class="cardFleursImgShape">
                                <img alt="Fleur <?= $content['variete'].' '. $content['couleur']; ?>" src="<?= $strimg;?>" class="cardFleursImg" id="cardFleursImg">
                            </div>
                            <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $content['variete']. " - ". $content['couleur']; ?></p>
                            <p class="mainFleursPrix" id="mainFleursPrix"><?= $content['prix']."€"; ?></p>
                            <p class="mainFleursStock" id="mainFleursStock">Stock : <?= $content['stock']; ?></p>
                            <a href='fleurs/editFleur.php?id=<?=$id_fleur?>'><img alt='Trash' src='../img/edit.png' class='cardFleursEdit'></a>
                            <a href='fleurs/deleteFleur.php?id=<?=$id_fleur?>'><img alt='Trash' src='../img/trash.png' class='cardFleursTrash'></a>
                        </a>
                    </div>

            <?php $resultsFleurs++;
            }

        }
        if ($resultsFleurs <= 0) {
            echo "<div class='searchCommand'><p>Il n'existe aucune fleur contenant ". $_GET['search']. "</p></div>";
        } ?>
    </main> <?php
    } else { ?>

        <h1 class="searchResultsTitle">Résultats de recherche pour : <?=$_GET['search']?></h1>
        <main class="mainFleurs">

    <?php $resultsFleurs = 0;

        foreach ($contents as $content) { // pour chaque info renseigné dans la barre de recherche, on sanitise puis ça affiche
            $searchTrim = trim($_GET['search']);
            $search = strtolower($searchTrim);
            $strcouleur = strtolower($content['couleur']);
            $strvariete = strtolower($content['variete']);
            $strprix = strtolower($content['prix']);
            $strstock = strtolower($content['stock']);

            if (strpos($strcouleur, $search) !== false || strpos($strvariete, $search) !== false || strpos($strprix, $search) !== false) {
                $strimg = substr($content['img'], 3);
                $id_fleur = $content['id_fleur']; ?>
                <div class="cardFleurs" id="cardFleurs">
                    <a href="fleurs/seeFleur.php?variete=<?=$content['variete']."&couleur=".$content['couleur']?>" class="cardFleursLink">
                        <div class="cardFleursImgShape">
                            <img alt="Fleur <?= $content['variete'].' '. $content['couleur']; ?>" src="<?= $strimg;?>" class="cardFleursImg" id="cardFleursImg">
                        </div>
                        <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $content['variete']. " - ". $content['couleur']; ?></p>
                        <p class="mainFleursPrix" id="mainFleursPrix"><?= $content['prix']."€"; ?></p>
                    </a>
                </div>

                <?php $resultsFleurs++;
            }
        }

        if ($resultsFleurs <= 0) {
            echo "<div class='searchCommand'><p>Il n'existe aucune fleur contenant ". $_GET['search']. "</p></div>";
        } ?>
    </main> <?php }
} else { ?>

    <h1 class="searchResultsTitle">Résultats de recherche pour : <?=$_GET['search']?></h1>
    <main class="mainFleurs">

        <?php $resultsFleurs = 0;

            foreach ($contents as $content) { // pour chaque info renseigné dans la barre de recherche, on sanitise puis ça affiche
                $searchTrim = trim($_GET['search']);
                $search = strtolower($searchTrim);
                $strcouleur = strtolower($content['couleur']);
                $strvariete = strtolower($content['variete']);
                $strprix = strtolower($content['prix']);
                $strstock = strtolower($content['stock']);

                if (strpos($strcouleur, $search) !== false || strpos($strvariete, $search) !== false || strpos($strprix, $search) !== false) {

                $strimg = substr($content['img'], 3);
                $id_fleur = $content['id_fleur']; ?>
                    <div class="cardFleurs" id="cardFleurs">
                        <a href="fleurs/seeFleur.php?variete=<?=$content['variete']."&couleur=".$content['couleur']?>" class="cardFleursLink">
                            <div class="cardFleursImgShape">
                                <img alt="Fleur <?= $content['variete'].' '. $content['couleur']; ?>" src="<?= $strimg;?>" class="cardFleursImg" id="cardFleursImg">
                            </div>
                            <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $content['variete']. " - ". $content['couleur']; ?></p>
                            <p class="mainFleursPrix" id="mainFleursPrix"><?= $content['prix']."€"; ?></p>
                        </a>
                    </div>

                    <?php $resultsFleurs++;
                }
            }

            if ($resultsFleurs <= 0) {
                echo "<div class='searchCommand'><p>Il n'existe aucune fleur contenant ". $_GET['search']. "</p></div>";
            }
        } ?>
    </main>

  </section>

    <?php include_once('footer.php'); ?>

    <script src="../javascript/scroll.js"></script>
    <script src="../javascript/deleteDisplaySearch.js"></script>
