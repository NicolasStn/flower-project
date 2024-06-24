<?php

require_once('../connexion.php');

$link = "../../";
$script = "";
$pages = "../";
$home = "../../../";

if ($_COOKIE != null) {

    $username = $_COOKIE['user'];

    $sqlClients = "SELECT * FROM client
    WHERE username = :username OR email = :username";
    $queryClients = $db->prepare($sqlClients);
    $queryClients->execute([
        "username" => $username
    ]);

    $clients = $queryClients->fetch();

    if ($clients['admin'] === '1') {

        if (isset($_POST['submit']) && isset($_POST['id']) && intval($_POST['id']) != 0 ) {

            $password = htmlspecialchars($_POST['password']);

            if ($clients['username'] && password_verify($password, $clients['password']) || $clients['email'] && password_verify($password, $clients['password'])) {

                $id = intval(trim($_POST['id']));

                $sqlCheckFlower =  "SELECT * FROM fleur_commande
                                    WHERE id_fleur = :id_fleur";

                $queryCheckFlower = $db->prepare($sqlCheckFlower);
                $queryCheckFlower->execute([
                    "id_fleur" => $id,
                ]);

                $checkFlower = $queryCheckFlower->fetch();

                if ($checkFlower === false) {

                    $sqlDelete = "DELETE FROM fournisseur_fleur
                                WHERE id_fleur = :id";
                    $queryDelete = $db->prepare($sqlDelete);
                    $queryDelete->execute([
                        "id" => $id
                    ]);

                    $sqlDeleteLignes = "DELETE FROM fleur WHERE id_fleur = :id";

                    $queryDeleteLignes = $db->prepare($sqlDeleteLignes);
                    $queryDeleteLignes->execute([
                        "id" => $id
                    ]);

                    header('Location:fleurs.php');
                } else {
                    echo "<script>alert('Une commande a déjà été passé avec cette fleur. La suppression est donc impossible');</script>";
                }

            } else {
                echo "<script>alert('Mot de passe incorrect');</script>";
            }
        }

        $id_fleur = intval(trim($_GET['id']));

        $sql = "SELECT fournisseur.raison_social AS raison_social, couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                INNER JOIN fournisseur ON fournisseur_fleur.id_fournisseur = fournisseur.id_fournisseur
                WHERE fleur.id_fleur = :id_fleur";

        $query = $db->prepare($sql);
        $query->execute([
            "id_fleur" => $id_fleur
        ]);

        $fleurs = $query->fetch();

        $title = "Supprimer la fleur ". $fleurs['variete']. " ". $fleurs['couleur']. " - Wild";
        include_once('../header.php'); ?>

            <main class="deleteFleur">
                <h1 class="deleteFleurTitle" id="cardClientContainerRightTitle">Suppression de <?=$fleurs['variete']. " ".$fleurs['couleur']?></h1>

                <div class="deleteFleurContainer">
                    <div class="deleteFleurContainerContent">
                        <p class="deleteFleurContent">Variété : <?= $fleurs['variete'] ?></p>
                        <p class="deleteFleurContent">Couleur : <?= $fleurs['couleur'] ?></p>
                        <p class="deleteFleurContent">Prix : <?= $fleurs['prix'] ?></p>
                        <p class="deleteFleurContent">Stock : <?= $fleurs['stock'] ?></p>
                        <p class="deleteFleurContent">Fournisseur : <?= $fleurs['raison_social'] ?></p>
                    </div>
                    <img src="<?= $fleurs['img'] ?>" class="editFleurImg">
                </div>

                <div class="deleteCommandeContainer" id="deleteCommandeBtn">
                    <button type="button" class="deleteFleurSubmit">Supprimer</button></a>
                </div>
                <form method="POST" id="deleteCommandeForm" class="deleteFleurForm">
                    <input type="hidden" name="id" value="<?= $fleurs['id_fleur']; ?>" >
                    <div class="commandeCardInput">
                        <label for="password" class="cardFournisseurLabel">Mot de passe *</label>
                        <input type="password" name="password" class="cardFournisseurInput" placeholder="Mot de passe">
                    </div>
                    <input type="submit" name="submit" value="Supprimer" class='cardClientContainerRightSaveEdit'>
                </form>
            </main>
            <script src="../../javascript/deleteCommande.js"></script>

            <?php include_once('../footer.php'); ?>

    <?php

    } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: ../../../index.php');
}

?>
