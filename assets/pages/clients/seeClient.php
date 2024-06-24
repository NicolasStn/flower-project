<?php

require_once('../connexion.php');

$link = "../../";
$script = "";
$pages = "../";
$home = "../../../";

if (isset($_POST['id']) && isset($_POST['submitCommandes'])) {

$username = $_COOKIE['user'];
$id = $_POST['id'];

$sqlClients = "SELECT username, admin FROM client
WHERE username = :username";
$queryClients = $db->prepare($sqlClients);
$queryClients->execute([
    "username" => $username
]);

$allClients = $queryClients->fetch();

/* requete pour récupérer les informations d'un client */

$sql = "SELECT username FROM demo_fleuriste.`client` WHERE id_client = :id;";
$query = $db->prepare($sql);
$query->execute([
    'id' => $id
]);

$client = $query->fetch();

/* je renvois l'utilisateur à la page index si le client n'existe pas en base */

if ($client === false){
    header('Location:clients.php');
}

if ($allClients['admin'] === '1') {

    $sqlAdmin = "SELECT client.id_client, nom, prenom, telephone, adresse, code_postal, ville, email, username, fleur_commande.num_commande AS commande, variete.libelle AS variete, couleur.libelle AS couleur FROM `client`
    INNER JOIN commande ON client.id_client = commande.id_client
    INNER JOIN fleur_commande ON commande.id_commande = fleur_commande.num_commande
    INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
    INNER JOIN variete ON fleur.id_variete = variete.id_variete
    INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
    WHERE client.id_client = :id";
    $queryAdmin = $db->prepare($sqlAdmin);
    $queryAdmin->execute([
        "id" => $id
    ]);

    $clientsAdmin = $queryAdmin->fetchAll();

    if ($clientsAdmin == NULL) {
        $sqlAdmin = "SELECT prenom, nom FROM `client`
        WHERE client.id_client = :id";
        $queryAdmin = $db->prepare($sqlAdmin);
        $queryAdmin->execute([
            "id" => $id
        ]);

        $clientsAdmin = $queryAdmin->fetch();

        $title = "Commandes de ". $clientsAdmin['prenom']. " ". $clientsAdmin['nom']. " - Wild";
        include_once('../header.php'); ?>

            <h1 class="mainClientsTitle">Commande de <?= $clientsAdmin['prenom']. " ". $clientsAdmin['nom'] ?></h1>
            <main class="mainClients">
                    <div class="seeClientsCommandeContainer">
                        <p class="seeClientsCommande">Aucune commande passée ou en cours</p>
                        <?php
                    } else {
                        $sqlAdmin = "SELECT client.id_client, nom, prenom, telephone, adresse, username, email, code_postal, ville, commande.id_commande AS commande
                                    FROM `client`
                                    INNER JOIN commande ON client.id_client = commande.id_client
                                    WHERE client.id_client = :id
                                    ORDER BY `commande`.`date_commande` DESC";
                        $queryAdmin = $db->prepare($sqlAdmin);
                        $queryAdmin->execute([
                            "id" => $id
                        ]);

                        $clientsAdmin = $queryAdmin->fetchAll();

                        $clientAdmin = $clientsAdmin[0];

                            $title = "Commandes de ". $clientAdmin['prenom']. " ". $clientAdmin['nom']. " - Wild";
                            include_once('../header.php'); ?>

                        <h1 class="mainClientsTitle">Commande de <?= $clientAdmin['prenom']. " ". $clientAdmin['nom'] ?></h1>
                        <main class="mainClients">
                            <div class="seeClientsCommandeContainer">
                                <div class="seeClientsCommandeContainerCenter">
                            <?php foreach($clientsAdmin as $client){ ?>
                                <p>Commande numéro : <a href="commandeClient.php?commandeNumber=<?= $client['commande'] ?>"><?= $client['commande'] ?></a></p>
                            <?php } ?>
                                </div>
                            <?php } ?>
                    </div>
        </main>
    <?php include_once('../footer.php');

    } else if ($allClients['admin'] === '0') {

        $sql = "SELECT commande.id_commande AS commande, client.id_client AS id_client FROM `client`
        INNER JOIN commande ON client.id_client = commande.id_client
        WHERE client.username = :username
        ORDER BY `commande`.`date_commande` DESC";
        $query = $db->prepare($sql);
        $query->execute([
            "username" => $username
        ]);

        $clientsUser = $query->fetchAll();

        $title = "Mes commandes - Wild";
        include_once('../header.php'); ?>

            <h1 class="mainClientsTitle">Mes commandes</h1>

            <main class="mainClients">
                        <div class="seeClientsCommandeContainer">
                            <?php if (!empty($clientsUser)) {
                                $id_client['id_client'] = $clientsUser[0]; ?>
                                    <div class="seeClientsCommandeContainerCenter">

                                        <?php foreach($clientsUser as $client) {

                                        ?>

                                            <p class="seeClientsCommande">Commande numéro : <a href="commandeClient.php?commandeNumber=<?= $client['commande'] ?>"><?= $client['commande'] ?></a></p>

                                        <?php } ?>

                                        </div>

                                <?php } else { ?>
                                <p class="seeClientsCommande">Vous n'avez encore effectué aucune commande</p>
                            <?php } ?>
                        </div>
            </main>

<?php include_once('../footer.php');

        } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: clients.php');
}
?>
