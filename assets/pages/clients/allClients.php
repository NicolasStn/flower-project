<?php

require_once('../connexion.php');

$link = "../../";
$title = "Liste des clients - Wild";
$script = "";
$pages = "../";
$home = "../../../";

if (isset($_COOKIE['user'])) {

$username = $_COOKIE['user'];

$sqlClients = "SELECT username, admin, password, email FROM client
WHERE username = :username";
$queryClients = $db->prepare($sqlClients);
$queryClients->execute([
    "username" => $username
]);

$checkClients = $queryClients->fetch();

if ($checkClients['admin'] === '1') {

    $sql = "SELECT username, id_client, gender, nom, prenom, email, telephone, adresse, code_postal, ville FROM client
            ORDER BY id_client DESC";
    $query = $db->prepare($sql);
    $query->execute();

    $allClients = $query->fetchAll();

    // Add

    if (isset($_POST['submitAdd'])) {

        if (isset($_POST['nom']) && $_POST['nom'] != '' && isset($_POST['prenom']) && $_POST['prenom'] != '' && isset($_POST['confirm_password']) && $_POST['confirm_password'] != ''
        && isset($_POST['password']) && $_POST['password'] != '' && isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['email']) && $_POST['email'] != ''
        && isset($_POST['tel']) && $_POST['tel'] != ''){

            $nom = htmlspecialchars(trim($_POST['nom']));
            $nom = stripslashes($nom);
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $prenom = stripslashes($prenom);
            $tel = htmlspecialchars(trim($_POST['tel']));
            $tel = stripslashes($tel);
            $adresse = htmlspecialchars(trim($_POST['adresse']));
            $adresse = stripslashes($adresse);
            $code_postal = htmlspecialchars(trim($_POST['code_postal']));
            $code_postal = stripslashes($code_postal);
            $ville = htmlspecialchars(trim($_POST['ville']));
            $ville = stripslashes($ville);
            $email = htmlspecialchars(trim($_POST['email']));
            $email = stripslashes($email);
            $emailLower = strtolower($email);
            $username = htmlspecialchars(trim($_POST['username']));
            $username = stripslashes($username);
            $usernameLower = strtolower($username);
            $password = htmlspecialchars($_POST['password']);
            $hashedpwd = password_hash($password, PASSWORD_DEFAULT);
            $gender = $_POST['gender'];

            $a = 0;
            $b = 0;
            $c = 0;

            $sqlCheckClientes = "SELECT * FROM client";
            $queryCheckClientes = $db->prepare($sqlCheckClientes);
            $queryCheckClientes->execute();

            $checkClients = $queryCheckClientes->fetchAll();

            foreach ($checkClients as $checkClient) {
                if (strtolower($checkClient['username']) === $usernameLower) {
                    $a++;
                }
                if ($checkClient['telephone'] === $tel) {
                    $b++;
                }
                if (strtolower($checkClient['email']) === $emailLower) {
                    $c++;
                }
            }

            if ($a === 1 && $b === 0 && $c === 0) {
                echo "<script>alert('Username déjà enregistré')</script>";
            } else if ($a === 0 && $b === 1 && $c === 0) {
                echo "<script>alert('Téléphone ". $tel. " déjà enregistré')</script>";
            } else if ($a === 0 && $b === 0 && $c === 1) {
                echo "<script>alert('Email ". $email. " déjà enregistré')</script>";
            } else if ($a === 1 && $b === 1 && $c === 0) {
                echo "<script>alert('Username et téléphone déjà enregistrés')</script>";
            } else if ($a === 1 && $b === 0 && $c === 1) {
                echo "<script>alert('Username et email déjà enregistrés')</script>";
            } else if ($a === 0 && $b === 1 && $c === 1) {
                echo "<script>alert('Téléphone et email déjà enregistrés')</script>";
            } else if ($a === 1 && $b === 1 && $c === 1) {
                echo "<script>alert('Username, téléphone et email déjà existants')</script>";
            } else if ($a === 0 && $b === 0 && $c === 0) {

            $sql = "INSERT INTO client(nom, prenom, email, telephone, adresse, code_postal, ville, username, `password`, gender)
            VALUES (:nom, :prenom, :email, :telephone, :adresse, :code_postal, :ville, :username, :pwd, :gender);";

            $query = $db->prepare($sql);
            $query->execute([
                "nom" => $nom,
                "prenom" => $prenom,
                "email" => $email,
                "telephone" => $tel,
                "adresse" => $adresse,
                "code_postal" => $code_postal,
                "ville" => $ville,
                "username" => $username,
                "pwd" => $hashedpwd,
                "gender" => $gender
            ]);

            header("Location:allClients.php");

            }
        } else {
            header("Location:allClients.php");
            echo "<script>alert('Merci de remplir correctement le formulaire.');</script>";
        }
    }

    // Edit

    if (isset($_POST['submitEdit']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['telephone']) && isset($_POST['gender'])) {

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

        $sqlOldUsername = "SELECT username, email, telephone FROM client
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
            header('Location:allClients.php');
        }
    }

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
            header('Location:allClients.php');
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

        if ($checkClients['username'] && password_verify($password, $checkClients['password']) || $checkClients['email'] && password_verify($password, $checkClients['password'])) {

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

            header('Location:allClients.php');
        } else {
            echo "<script>alert('Mot de passe incorrect');</script>";
        }
    }

    include_once('../header.php'); ?>

        <h1 class="mainClientsTitle">Nos clients</h1>

    <main class="mainFournisseurs">

    <!-- Add -->

        <section class="cardClientsAdd" id="cardClientsAdd"><button id="cardClientsPlus" type="button">+</button>
            <h1 class="mainAddTitle">Ajout client</h1>
            <form method="POST">

                <div class="mainClientCardInputGender">
                    <input type="radio" name="gender" value="M" required>M
                    <input type="radio" name="gender" value="Mme" required>Mme
                    <input type="radio" name="gender" value="Autre" required>Autre
                </div>
                <div class="cardClientFormContainerContent">
                    <div class="cardClientFormContainer">
                        <label for="nom" class="cardFournisseurLabel">Nom *</label>
                        <input type="text" name="nom" id="nom" placeholder="Nom *" class="cardFournisseurInput" required>
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="prenom" class="cardFournisseurLabel">Prénom *</label>
                        <input type="text" name="prenom" id="prenom" placeholder="Prénom *" class="cardFournisseurInput" required>
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="username" class="cardFournisseurLabel">Username *</label>
                        <input type="text" name="username" id="username" placeholder="Nom d'utilisateur *" class="cardFournisseurInput" required>
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="username" class="cardFournisseurLabel">Email *</label>
                        <input type="email" name="email" id="email" placeholder="exemple@contact.fr *" class="cardFournisseurInput" required>
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="tel" class="cardFournisseurLabel">Téléphone *</label>
                        <input type="tel" name="tel" id="telephone" placeholder="Téléphone *" class="cardFournisseurInput" required>
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="adresse" class="cardFournisseurLabel">Adresse</label>
                        <input type="text" name="adresse" id="adresse" placeholder="Adresse" class="cardFournisseurInput">
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="code_postal" class="cardFournisseurLabel">Code Postal</label>
                        <input type="number" name="code_postal" id="code_postal" placeholder="Code Postal" class="cardFournisseurInput">
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="ville" class="cardFournisseurLabel">Ville</label>
                        <input type="text" name="ville" id="ville" placeholder="Ville" class="cardFournisseurInput">
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="password" class="cardFournisseurLabel">Mot de passe *</label>
                        <input type="password" name="password" id="password" placeholder="Mot de passe *" class="cardFournisseurInput" required>
                    </div>
                    <div class="cardClientFormContainer">
                        <label for="confirm_password" class="cardFournisseurLabel">Confirmation mot de passe *</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmation mot de passe *" class="cardFournisseurInput" required>
                    </div>
                </div>
                <input type="submit" name="submitAdd" value="Ajouter"  class='cardClientsAddSubmit'>
            </form>

        </section>

        <!-- Edit -->

            <?php foreach($allClients as $client){ ?>

                <div class="cardFournisseurs">
                    <?php $id_client = $client['id_client']; ?>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?=$id_client?>">

                        <div class="mainClientCardInputGender">
                            <input type="radio" name="gender" value="M" <?php if ($client['gender'] === 'M') { echo "checked"; } ?> required>M
                            <input type="radio" name="gender" value="Mme" <?php if ($client['gender'] === 'Mme') { echo "checked"; } ?> required>Mme
                            <input type="radio" name="gender" value="Autre" <?php if ($client['gender'] === 'Autre') { echo "checked"; } ?> required>Autre
                        </div>
                        <div class="cardClientFormContainerContent">
                            <div class="cardClientFormContainer">
                                <label for="nom" class="cardFournisseurLabel">Nom *</label>
                                <input type="text" name="nom" id="nom" placeholder="Nom *" class="cardFournisseurInput" value="<?= $client['nom']; ?>" required>
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="prenom" class="cardFournisseurLabel">Prénom *</label>
                                <input type="text" name="prenom" id="prenom" placeholder="Prénom *" class="cardFournisseurInput" value="<?= $client['prenom']; ?>" required>
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="username" class="cardFournisseurLabel">Username *</label>
                                <input type="text" name="username" id="username" placeholder="Nom d'utilisateur *" class="cardFournisseurInput" value="<?= $client['username']; ?>" required>
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="username" class="cardFournisseurLabel">Email *</label>
                                <input type="email" name="email" id="email" placeholder="exemple@contact.fr *" class="cardFournisseurInput" value="<?= $client['email']; ?>" required>
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="telephone" class="cardFournisseurLabel">Téléphone *</label>
                                <input type="tel" name="telephone" id="telephone" placeholder="Téléphone *" class="cardFournisseurInput" value="<?= $client['telephone']; ?>" required>
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="adresse" class="cardFournisseurLabel">Adresse</label>
                                <input type="text" name="adresse" id="adresse" placeholder="Adresse" class="cardFournisseurInput" value="<?= $client['adresse']; ?>">
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="code_postal" class="cardFournisseurLabel">Code Postal</label>
                                <input type="number" name="code_postal" id="code_postal" placeholder="Code Postal" class="cardFournisseurInput" value="<?= $client['code_postal']; ?>">
                            </div>
                            <div class="cardClientFormContainer">
                                <label for="ville" class="cardFournisseurLabel">Ville</label>
                                <input type="text" name="ville" id="ville" placeholder="Ville" class="cardFournisseurInput" value="<?= $client['ville']; ?>">
                            </div>
                        </div>

                        <div class="allClientsEdit">
                            <input type="submit" name="submitEdit" value="Modifier"  class='mainFournisseursEdit'>
                            <button type="button" id="deleteFournisseurFirstForm" class="mainFournisseursDelete" onclick="showDeleteForm(this)">Supprimer</button>
                        </div>
                    </form>

                    <form action="seeClient.php" method="post">
                        <input type="hidden" name="id" value="<?= $id_client ?>">
                        <input type="submit" name="submitCommandes" value="Voir les commandes" class="mainClientsCommande">
                    </form>

                    <!-- Delete -->

                    <form method="POST" class="deleteClientFinalForm" id="deleteFournisseurFinalForm">
                        <input type="hidden" name="id" value="<?= $id_client; ?>" >
                        <label for="password">Afin de supprimer définitivement votre compte, merci de saisir votre mot de passe </label>
                        <input type="password" name="password" placeholder="Mot de passe" class="cardFournisseurInputPasswordDelete">
                        <input type="submit" name="submitDelete" value="Supprimer" class="mainFournisseursDeleteConfirmation">
                    </form>
                </div>

            <?php } ?>
    </main>
    <script src="../../javascript/addClient.js"></script>

<?php include_once('../footer.php'); ?>

<?php

    } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: ../../../index.php');
}

?>
