<?php

require_once('../connexion.php');

$link = "../../";
$script = "";
$pages = "../";
$home = "../../../";

if (isset($_GET['commandeNumber'])) {

    $username = $_COOKIE['user'];
    $commande = $_GET['commandeNumber'];

    $sqlClients =   "SELECT client.id_client, admin, username, valider FROM commande
                    INNER JOIN client ON client.id_client = commande.id_client
                    WHERE id_commande = :id_commande";
    $queryClients = $db->prepare($sqlClients);
    $queryClients->execute([
        "id_commande" => $commande
    ]);

    $allClients = $queryClients->fetch();

    $sqlAdmin =   "SELECT * FROM client
                WHERE username = :username";
    $queryAdmin = $db->prepare($sqlAdmin);
    $queryAdmin->execute([
        "username" => $username
    ]);

    $admin = $queryAdmin->fetch();

    /* requete pour récupérer les informations d'un client */

    $sql = "SELECT * FROM commande WHERE id_commande = :commande;";
    $query = $db->prepare($sql);
    $query->execute([
        'commande' => $commande
    ]);

    $client = $query->fetch();

    /* je renvois l'utilisateur à la page index si le client n'existe pas en base */

    if ($client === false){
        header('Location:clients.php');
    }

    /* je renvois l'utilisateur à la page index si la requete IRL de numero de commande ne correspond a aucune des siennes */
    if ($allClients['id_client'] !== $admin['id_client'] && $admin['admin'] !== "1") {
        header('Location:clients.php');
    }

    if ($allClients['username'] === $username || $admin['admin'] === '1') {

        if (isset($_POST['submit']) && isset($_POST['commandeNumber'])) {

            $password = htmlspecialchars($_POST['password']);

            if ($admin['username'] && password_verify($password, $admin['password']) || $admin['email'] && password_verify($password, $admin['password'])) {

                if ($allClients['valider'] === '0') {

                    $sqlUpdate =    "UPDATE fournisseur_fleur
                                    SET stock = stock + (SELECT quantity FROM fleur_commande WHERE num_commande = :id AND fleur_commande.id_fleur = fournisseur_fleur.id_fleur)
                                    WHERE EXISTS (SELECT * FROM fleur_commande WHERE num_commande = :id AND fleur_commande.id_fleur = fournisseur_fleur.id_fleur)";
                    $queryUpdate = $db->prepare($sqlUpdate);
                    $queryUpdate->execute([
                        "id" => $_POST['commandeNumber']
                    ]);

                    $sqlDeleteLignes = "DELETE FROM demo_fleuriste.fleur_commande WHERE num_commande = :num";
                    $queryDeleteLignes = $db->prepare($sqlDeleteLignes);
                    $queryDeleteLignes->execute([
                        "num" => $_POST['commandeNumber']
                    ]);

                    $sqlDeleteCommande = "DELETE FROM demo_fleuriste.commande WHERE id_commande = :num";
                    $queryDeleteCommande = $db->prepare($sqlDeleteCommande);
                    $queryDeleteCommande->execute([
                        "num" => $_POST['commandeNumber']
                    ]);

                $id = $admin['id_client'];

                header("Location:seeClient.php?id=$id");
                }

            } else {
                echo "<script>alert('Mot de passe incorrect');</script>";
            }
        }

            $title = "Ma commande ". $commande. " - Wild";
            include_once('../header.php');

            $sqlAdmin = "SELECT client.id_client, nom, prenom, telephone, adresse, username, quantity, valider, email, prix, code_postal, `admin`, ville,
            commande.id_commande AS commande, variete.libelle AS variete, couleur.libelle AS couleur, img FROM `client`
            INNER JOIN commande ON client.id_client = commande.id_client
            INNER JOIN fleur_commande ON commande.id_commande = fleur_commande.num_commande
            INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
            INNER JOIN variete ON fleur.id_variete = variete.id_variete
            INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
            WHERE fleur_commande.num_commande = :num_commande";
            $queryAdmin = $db->prepare($sqlAdmin);
            $queryAdmin->execute([
                "num_commande" => $commande
            ]);

            $clientsAdmin = $queryAdmin->fetchAll();
            $clientAdmin = $clientsAdmin[0]; ?>

            <h1 class="mainClientsTitle">Commande numéro <?= $commande ?></h1>
            <main class="mainClients">
                <div class="seeClientsCommandeContainer">
                    <?php $a = 0;
                        foreach($clientsAdmin as $client) {
                            $prix = $client['prix'] * $client['quantity'];
                            $a = $a + $prix; ?>
                            <div class="deleteCommandeCardContainer">
                                <img src="<?= $client['img'] ?>" class="editFleurImg">
                                <div class="deleteCommandeCardContent">
                                    <p class="deleteCommandeCardContentTitle"><?= $client['variete']. ' '. $client['couleur']?></p>
                                    <p class="mainClientsVariete">Quantité : <?=  $client['quantity']?></p>
                                    <p class="mainClientsVariete">Prix : <?= $prix. "€"?></p>
                                </div>
                            </div>
                    <?php } ?>
                    <p class="deleteCommandeCardContentTotal">Total : <?= $a ?>€</p>

                    <div class="deleteCommandeFirstForm" id="deleteClientFirstForm">
                        <?php if ($clientAdmin['valider'] === '0') { ?>

                            <div class="deleteCommandeContainer" id="deleteCommandeBtn">
                                <?php if ($clientAdmin['admin'] === '1') { ?>
                                    <button type="button" class="commandeCardSubmit">Annuler la commande</button></a>
                                <?php } else { ?>
                                    <button type="button" class="commandeCardSubmit">Annuler ma commande</button></a>
                                <?php } ?>
                            </div>
                            <form method="POST" id="deleteCommandeForm" class="deleteCommandeForm">
                                <input type="hidden" name="commandeNumber" value="<?= $commande; ?>" >
                                <div class="commandeCardInput">
                                    <label for="password" class="cardFournisseurLabel">Mot de passe *</label>
                                    <input type="password" name="password" placeholder="Mot de passe" class="cardFournisseurInput">
                                </div>
                                <input type="submit" name="submit" class='cardClientContainerRightSaveEdit' value="Annuler">
                            </form>

                        <?php } ?>
                    </div>
                </div>
            </main>

            <script src="../../javascript/deleteCommande.js"></script>
            <?php include_once('../footer.php');

    } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: ../../../index.php');
}
?>
