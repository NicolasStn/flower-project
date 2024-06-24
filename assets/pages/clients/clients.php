<?php

require_once('../connexion.php');

$link = "../../";
$script = "";
$pages = "../";
$home = "../../../";

if (isset($_COOKIE['user'])) {

    $username = $_COOKIE['user'];

    $sqlClients = "SELECT * FROM client
    WHERE username = :username";
    $queryClients = $db->prepare($sqlClients);
    $queryClients->execute([
        "username" => $username
    ]);

    $clients = $queryClients->fetch();

    if ($clients['admin'] === '1') {

        // Delete

        if (isset($_POST['submitDelete']) && isset($_POST['id']) && intval($_POST['id']) != 0 ) {

            $id = $_POST['id'];
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

            /* requête pour récupérer les commandes du client */
            $sqlCommandes = "SELECT commande.id_commande, commande.date_commande, total FROM demo_fleuriste.commande
                INNER JOIN demo_fleuriste.client ON commande.id_client = client.id_client
                INNER JOIN demo_fleuriste.fleur_commande ON commande.id_commande = fleur_commande.num_commande
                INNER JOIN demo_fleuriste.fleur ON fleur_commande.id_fleur = fleur.id_fleur
                WHERE client.id_client = :id
                GROUP BY commande.id_commande;";
            $queryCommandes = $db->prepare($sqlCommandes);
            $queryCommandes->execute([
                "id" => $id
            ]);

            $commandes = $queryCommandes->fetchAll();

            $password = htmlspecialchars($_POST['password']);

            if ($clients['username'] && password_verify($password, $clients['password']) || $clients['email'] && password_verify($password, $clients['password'])) {

                $id = intval(trim($_POST['id']));

                foreach($commandes as $commande) {
                    $sqlDeleteLignes = "DELETE FROM demo_fleuriste.fleur_commande WHERE num_commande = :num";
                    $sqlDeleteCommande = "DELETE FROM demo_fleuriste.commande WHERE num_commande = :num";

                    $queryDeleteLignes = $db->prepare($sqlDeleteLignes);
                    $queryDeleteLignes->execute([
                        "num" => $commande['num_commande']
                    ]);

                    $queryDeleteCommande = $db->prepare($sqlDeleteCommande);
                    $queryDeleteCommande->execute([
                        "num" => $commande['num_commande']
                    ]);
                }

                $sqlDelete = "DELETE FROM demo_fleuriste.client WHERE id_client = :id";
                $queryDelete = $db->prepare($sqlDelete);
                $queryDelete->execute([
                    'id' => $id
                ]);

                header("Location: ".$_SERVER['HTTP_REFERER']);
            } else {
                header("Location: ".$_SERVER['HTTP_REFERER']);
            }
        }

        // Edit

        if (isset($_POST['submitEdit']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['telephone']) && isset($_POST['gender'])) {

            /* je renvois l'utilisateur à la page index s'il n'y a pas de parametre id dans l'url de la page */
            if (!isset($_POST['id']) || intval($_POST['id']) == 0) {
                header('Location:allClients.php');
            }

            $id = $_POST['id'];

            /* requete pour récupérer les informations d'un client */

            $sql = "SELECT username FROM demo_fleuriste.`client` WHERE id_client = :id;";
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id
            ]);

            $client = $query->fetch();

            /* je renvois l'utilisateur à la page index si le client n'existe pas en base */

            if ($client === false){
                header('Location:allClients.php');
            }

            $id = intval(trim($_POST['id']));
            $nom = htmlspecialchars(trim($_POST['nom']));
            $nom = stripslashes($nom);
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $prenom = stripslashes($prenom);
            $username = htmlspecialchars(trim($_POST['username']));
            $username = stripslashes($username);
            $usernameLower = strtolower($username);
            $email = htmlspecialchars(trim($_POST['email']));
            $email = stripslashes($email);
            $emailLower = strtolower($email);
            $telephone = htmlspecialchars(trim($_POST['telephone']));
            $telephone = stripslashes($telephone);
            $adresse = htmlspecialchars(trim($_POST['adresse']));
            $adresse = stripslashes($adresse);
            $code_postal = htmlspecialchars(trim($_POST['code_postal']));
            $code_postal = stripslashes($code_postal);
            $ville = htmlspecialchars(trim($_POST['ville']));
            $ville = stripslashes($ville);
            $gender = htmlspecialchars(trim($_POST['gender']));

            $sqlVerifUsername = "SELECT username FROM client
                                WHERE username = :username";
            $queryVerifUsername = $db->prepare($sqlVerifUsername);
            $queryVerifUsername->execute([
                "username" => $username
            ]);

            $verifUsername = $queryVerifUsername->fetch();

            $sqlVerifEmail = "SELECT email FROM client
                              WHERE email = :email";
            $queryVerifEmail = $db->prepare($sqlVerifEmail);
            $queryVerifEmail->execute([
                "email" => $email
            ]);

            $verifEmail = $queryVerifEmail->fetch();

            $sqlVerifTelephone = "SELECT telephone FROM client
                                WHERE telephone = :telephone";
            $queryVerifTelephone = $db->prepare($sqlVerifTelephone);
            $queryVerifTelephone->execute([
                "telephone" => $telephone
            ]);

            $verifTelephone = $queryVerifTelephone->fetch();

            $sqlOldUsername = "SELECT username FROM client
                                WHERE id_client = :id";
            $queryOldUsername = $db->prepare($sqlOldUsername);
            $queryOldUsername->execute([
                    "id" => $id
                ]);

            $verifOldUsername = $queryOldUsername->fetch();

            if (!empty($verifUsername) && $usernameLower != strtolower($verifOldUsername['username'])) {
                echo "<script>alert('Username déjà existant');</script>";
            } else if (!empty($verifEmail) && $emailLower != strtolower($verifOldUsername['email'])) {
                echo "<script>alert('Email déjà existant');</script>";
            } else if (!empty($verifTelephone) && $telephone != $verifOldUsername['telephone']) {
                echo "<script>alert('Téléphone déjà existant');</script>";
            } else {
                $sqlUpdate = "UPDATE demo_fleuriste.client
                SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse, code_postal = :code_postal, ville = :ville, email = :email, gender = :gender, username = :username
                WHERE id_client = :id";
                $queryUpdate = $db->prepare($sqlUpdate);
                $queryUpdate->execute([
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "telephone" => $telephone,
                    "adresse" => $adresse,
                    "code_postal" => $code_postal,
                    "ville" => $ville,
                    "id" => $id,
                    "username" => $username,
                    "email" => $email,
                    "gender" => $gender
                ]);
                header("Location: ".$_SERVER['HTTP_REFERER']);
            }
        }

    } else if ($clients['admin'] === '0') {

        // Delete

        if (isset($_POST['submitDelete']) && isset($_POST['id']) && intval($_POST['id']) != 0 ) {

            $id = $_POST['id'];

            $sqlVerif = "SELECT id_client FROM demo_fleuriste.`client` WHERE username = :username;";
            $queryVerif = $db->prepare($sqlVerif);
            $queryVerif->execute([
                'username' => $_COOKIE['user']
            ]);

            $clientVerif = $queryVerif->fetch();

            if ($clientVerif['id_client'] === $id) {

                /* requete pour récupérer les informations d'un client */
                $sql = "SELECT username FROM demo_fleuriste.`client` WHERE id_client = :id;";
                $query = $db->prepare($sql);
                $query->execute([
                    'id' => $id
                ]);

                $client = $query->fetch();

                /* requête pour récupérer les commandes du client */
                $sqlCommandes = "SELECT num_commande FROM demo_fleuriste.commande
                                INNER JOIN demo_fleuriste.client ON commande.id_client = client.id_client
                                INNER JOIN demo_fleuriste.fleur_commande ON commande.id_commande = fleur_commande.num_commande
                                INNER JOIN demo_fleuriste.fleur ON fleur_commande.id_fleur = fleur.id_fleur
                                WHERE client.id_client = :id
                                GROUP BY commande.id_commande;";
                $queryCommandes = $db->prepare($sqlCommandes);
                $queryCommandes->execute([
                    "id" => $id
                ]);

                $commandes = $queryCommandes->fetchAll();

                $password = htmlspecialchars($_POST['password']);

                if ($clients['username'] && password_verify($password, $clients['password']) || $clients['email'] && password_verify($password, $clients['password'])) {

                    $id = intval(trim($_POST['id']));

                    foreach($commandes as $commande) {

                            $sqlDeleteLignes = "DELETE FROM demo_fleuriste.fleur_commande WHERE num_commande = :num";

                            $queryDeleteLignes = $db->prepare($sqlDeleteLignes);
                            $queryDeleteLignes->execute([
                                "num" => $commande['num_commande']
                            ]);

                        }

                        /* requête pour récupérer les commandes du client */
                        $sqlCommandes = "SELECT id_commande FROM demo_fleuriste.commande
                                        INNER JOIN demo_fleuriste.client ON commande.id_client = client.id_client
                                        WHERE client.id_client = :id";
                        $queryCommandes = $db->prepare($sqlCommandes);
                        $queryCommandes->execute([
                            "id" => $id
                        ]);

                        $commandes = $queryCommandes->fetchAll();

                        foreach($commandes as $commande) {

                                $sqlDeleteCommande = "DELETE FROM demo_fleuriste.commande WHERE id_commande = :num";

                                $queryDeleteCommande = $db->prepare($sqlDeleteCommande);
                                $queryDeleteCommande->execute([
                                    "num" => $commande['id_commande']
                                ]);

                            }

                    $sqlDelete = "DELETE FROM demo_fleuriste.client WHERE id_client = :id";
                    $queryDelete = $db->prepare($sqlDelete);
                    $queryDelete->execute([
                        'id' => $id
                    ]);

                    setcookie("user", '', time() - 864000 * 30, "/");

                    header('Location:../../../index.php');
                } else {
                    echo "<script>alert('Mot de passe incorrect');</script>";
                }
            } else {
                header('Location:clients.php');
            }
        }

        // Edit

        if (isset($_POST['submitEdit'])) {

            if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['telephone']) && isset($_POST['gender'])) {

                /* je renvois l'utilisateur à la page index s'il n'y a pas de parametre id dans l'url de la page */

                if (!isset($_POST['id']) || intval($_POST['id']) == 0) {
                    header('Location:clients.php');
                } else {

                    $id = $_POST['id'];

                    $sqlVerif = "SELECT id_client FROM demo_fleuriste.`client` WHERE id_client = :id;";
                    $queryVerif = $db->prepare($sqlVerif);
                    $queryVerif->execute([
                        'id' => $id
                    ]);

                    $clientVerif = $queryVerif->fetch();
                }


                if (!isset($_POST['id']) || intval($_POST['id']) == 0 || $clientVerif['username'] !== $_COOKIE['user']) {
                    header('Location:clients.php');
                }

                $id = $_POST['id'];

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
                $id = intval(trim($_POST['id']));
                $nom = htmlspecialchars(trim($_POST['nom']));
                $nom = stripslashes($nom);
                $prenom = htmlspecialchars(trim($_POST['prenom']));
                $prenom = stripslashes($prenom);
                $username = htmlspecialchars(trim($_POST['username']));
                $username = stripslashes($username);
                $email = htmlspecialchars(trim($_POST['email']));
                $email = stripslashes($email);
                $telephone = htmlspecialchars(trim($_POST['telephone']));
                $telephone = stripslashes($telephone);
                $adresse = htmlspecialchars(trim($_POST['adresse']));
                $adresse = stripslashes($adresse);
                $code_postal = htmlspecialchars(trim($_POST['code_postal']));
                $code_postal = stripslashes($code_postal);
                $ville = htmlspecialchars(trim($_POST['ville']));
                $ville = stripslashes($ville);
                $gender = htmlspecialchars(trim($_POST['gender']));

                $sqlVerifUsername = "SELECT username FROM client
                                    WHERE username = :username";
                $queryVerifUsername = $db->prepare($sqlVerifUsername);
                $queryVerifUsername->execute([
                    "username" => $username
                ]);

                $verifUsername = $queryVerifUsername->fetch();

                $sqlVerifEmail = "SELECT email FROM client
                                WHERE email = :email";
                $queryVerifEmail = $db->prepare($sqlVerifEmail);
                $queryVerifEmail->execute([
                    "email" => $email
                ]);

                $verifEmail = $queryVerifEmail->fetch();

                $sqlVerifTelephone = "SELECT telephone FROM client
                                    WHERE telephone = :telephone";
                $queryVerifTelephone = $db->prepare($sqlVerifTelephone);
                $queryVerifTelephone->execute([
                    "telephone" => $telephone
                ]);

                $verifTelephone = $queryVerifTelephone->fetch();

                if (!empty($verifUsername) && $username != $_COOKIE['user']) {
                    echo "<p>Username déjà existant</p>";
                } else if (!empty($verifEmail) && $email != $clients['email']) {
                    echo "<p>Email déjà existant</p>";
                } else if (!empty($verifTelephone) && $telephone != $clients['telephone']) {
                    echo "<p>Téléphone déjà existant</p>";
                } else {
                    $sqlUpdate = "UPDATE demo_fleuriste.client
                                SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse, code_postal = :code_postal, ville = :ville, username = :username, email = :email, gender = :gender, telephone = :telephone
                                WHERE id_client = :id";
                    $queryUpdate = $db->prepare($sqlUpdate);
                    $queryUpdate->execute([
                        "nom" => $nom,
                        "prenom" => $prenom,
                        "telephone" => $telephone,
                        "adresse" => $adresse,
                        "code_postal" => $code_postal,
                        "ville" => $ville,
                        "id" => $id,
                        "username" => $username,
                        "email" => $email,
                        "gender" => $gender
                        ]);
                    $cookie_name = "user";
                    $cookie_value = $username;
                    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
                    header('Location:clients.php');
                }
            }
        }
    } else {
        header('Location:clients.php');
    }

    // Edit Password

    if (!empty($clients)) {


        if (isset($_POST['submit']) && isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {

            if (password_verify($_POST['old_password'], $clients['password']) && $_POST['password'] === $_POST['confirm_password']) {

                $hashedpwd = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sqlUpdate = "UPDATE demo_fleuriste.client
                                SET `password` = :pwd
                                WHERE username = :username";
                        $queryUpdate = $db->prepare($sqlUpdate);
                        $queryUpdate->execute([
                            "pwd" => $hashedpwd,
                            "username" => $clients['username']
                        ]);
                        header('Location:clients.php');
            } else {
                echo "<script>alert('Les mots de passe doivent correspondre');</script>";
            }
        }

        $title = "Mon profil - Wild";
        include_once('../header.php'); ?>

        <main class="mainClients">

            <div class="cardClients" id="cardClients">
                <?php $id_client = $clients['id_client']; ?>
                <div class="cardClientsContent">

                    <section class="cardClientsContainerLeft">
                        <h1 class="cardClientsTitle">Bienvenue <?=$clients['prenom']?></h1>
                        <button type="button" id="cardClientsContainerLeftBtnInfos" class="cardClientsContainerLeftBtn" onclick="window.location.href='#cardClientContainerRightTitle'">Mes informations ></button>
                        <form method="POST" action="seeClient.php">
                            <input type="hidden" name="id" value="<?=$id_client?>">
                            <input type="submit" name="submitCommandes" value="Mes commandes >" class="cardClientsContainerLeftBtn">
                        </form>
                        <a href="../disconnect.php"><button type="button" id="cardClientsContainerLeftBtnDeconnexion" class="cardClientsContainerLeftBtn">Se déconnecter ></button></a>
                        <button type="button" id="cardClientsContainerLeftBtnDelete" class="cardClientsContainerLeftBtn" onclick="window.location.href='#cardClientsContainerRightDelete'">Supprimer mon compte ></button>
                    </section>

                    <!-- Read -->

                    <section class="cardClientsContainerRight" id="cardClientsContainerRight">
                        <h1 class="cardClientContainerRightTitle" id="cardClientContainerRightTitle">Mes informations</h1>
                        <p>Genre : <?= $clients['gender']; ?></p>
                        <p class="mainClientsNom">Nom : <?= $clients['nom']; ?></p>
                        <p class="mainClientsPrenom">Prénom : <?= $clients['prenom']; ?></p>
                        <p class="mainClientsPrenom">Username : <?= $clients['username']; ?></p>
                        <a href="mailto:<?= $clients['email']; ?>"><?= $clients['email']; ?></a>
                        <a href="tel:<?= $clients['telephone']; ?>"><?= $clients['telephone']; ?></a>
                        <p class="mainClientsAdresse">Adresse : <?= $clients['adresse']; ?></p>
                        <p class="mainClientsCodePostal">Code Postal : <?= $clients['code_postal']; ?></p>
                        <p class="mainClientsVille">Ville : <?= $clients['ville']; ?></p>
                        <button type="button" id="cardClientsContainerLeftBtnEdit" class="loginCardSubmit" onclick="window.location.href='#cardClientContainerRightEdit'">Modifier mes informations</button>
                        <button type="button" id="cardClientsContainerLeftBtnEditPassword" class="loginCardClientBtn" onclick="window.location.href='#editPasswordContainer'">Modifier mon mot de passe</button>

                        <div class="editPasswordContainer" id="editPasswordContainer">
                            <form method="post">
                                <input type="password" name="old_password" class="createAccountCardInput" placeholder="Mot de passe actuel *" required>
                                <input type="password" name="password" id="password" placeholder="Nouveau mot de passe *" class="createAccountCardInput" required>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmation nouveau mot de passe *" class="createAccountCardInput" required>
                                <input type="submit" name="submit" class="createAccountCardSubmit" value="Confirmer">
                            </form>
                        </div>

                    </section>

                    <!-- Edit -->

                    <section class="cardClientContainerRightEdit" id="cardClientContainerRightEdit">
                        <h1 class="cardClientContainerRightTitle">Mes informations</h1>
                        <?php $id_client = $clients['id_client']; ?>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?=$id_client?>">

                            <div class="mainClientCardInputGender">
                                <input type="radio" name="gender" value="M" <?php if ($clients['gender'] === 'M') { echo "checked"; } ?> required>M
                                <input type="radio" name="gender" value="Mme" <?php if ($clients['gender'] === 'Mme') { echo "checked"; } ?> required>Mme
                                <input type="radio" name="gender" value="Autre" <?php if ($clients['gender'] === 'Autre') { echo "checked"; } ?> required>Autre
                            </div>
                            <div class="cardClientFormContainerContent">
                                <div class="cardClientFormContainer">
                                    <label for="nom" class="cardFournisseurLabel">Nom *</label>
                                    <input type="text" name="nom" id="nom" placeholder="Nom *" class="cardFournisseurInput" value="<?= $clients['nom']; ?>" required>
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="prenom" class="cardFournisseurLabel">Prénom *</label>
                                    <input type="text" name="prenom" id="prenom" placeholder="Prénom *" class="cardFournisseurInput" value="<?= $clients['prenom']; ?>" required>
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="username" class="cardFournisseurLabel">Username *</label>
                                    <input type="text" name="username" id="username" placeholder="Nom d'utilisateur *" class="cardFournisseurInput" value="<?= $clients['username']; ?>" required>
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="username" class="cardFournisseurLabel">Email *</label>
                                    <input type="email" name="email" id="email" placeholder="exemple@contact.fr *" class="cardFournisseurInput" value="<?= $clients['email']; ?>" required>
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="telephone" class="cardFournisseurLabel">Téléphone *</label>
                                    <input type="tel" name="telephone" id="telephone" placeholder="Téléphone *" class="cardFournisseurInput" value="<?= $clients['telephone']; ?>" required>
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="adresse" class="cardFournisseurLabel">Adresse</label>
                                    <input type="text" name="adresse" id="adresse" placeholder="Adresse" class="cardFournisseurInput" value="<?= $clients['adresse']; ?>">
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="code_postal" class="cardFournisseurLabel">Code Postal</label>
                                    <input type="number" name="code_postal" id="code_postal" placeholder="Code Postal" class="cardFournisseurInput" value="<?= $clients['code_postal']; ?>">
                                </div>
                                <div class="cardClientFormContainer">
                                    <label for="ville" class="cardFournisseurLabel">Ville</label>
                                    <input type="text" name="ville" id="ville" placeholder="Ville" class="cardFournisseurInput" value="<?= $clients['ville']; ?>">
                                </div>
                            </div>
                            <input type="submit" name="submitEdit" value="Enregistrer" class='cardClientContainerRightSaveEdit'>
                        </form>
                    </section>

                    <!-- Delete -->

                    <form method="POST" class="cardClientsContainerRightDelete" id="cardClientsContainerRightDelete">
                        <input type="hidden" name="id" value="<?= $id_client; ?>" >
                        <label for="password">Afin de supprimer définitivement votre compte, merci de saisir votre mot de passe </label>
                        <input type="password" name="password" placeholder="Mot de passe" class="cardFournisseurInputPasswordDelete">
                        <input type="submit" name="submitDelete" value="Supprimer" class="mainFournisseursDeleteConfirmation">
                    </form>

                </div>
            </div>

        </main>
        <script src="../../javascript/switchTabClient.js"></script>

        <?php include_once('../footer.php');

    } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: ../../../index.php');
}
?>
