<?php

require_once('../connexion.php');

$link = "../../";
$title = "Nos fournisseurs - Wild";
$script = "";
$pages = "../";
$home = "../../../";

if ($_COOKIE == null) { ?>
    <section class="cookieContainer" id="cookieContainer">
        <h1 class="cookieTitle">Des cookies pour une vie fleurie</h1>
        <p class="cookieContent">Bonjour ! Wild utilise des cookies techniques et des cookies fonctionnels. Vous pouvez accepter ou refuser l'ensemble des cookies et changer d'avis à tout moment. Pour plus d'informations, consultez l'onglet <a href="#">Cookie</a>.</p>
        <div class="cookieContainerBtn">
            <button type="button" id="cookieAccept" class="cookieAccept" onclick="window.location.href='../acceptCookies.php'">Accepter</button>
            <button type="button" id="cookieDelete" class="cookieDelete">Refuser</button>
        </div>
    </section>
    <img src="../../img/cookie.png" alt="Cookie" class="cookieImg" id="cookieImg">
    <script src="../../javascript/closeBanner.js"></script>
<?php }

if (isset($_COOKIE['user'])) {
    $username = $_COOKIE['user'];

$sqlClients = "SELECT username, password, email, admin FROM client
WHERE username = :username";
$queryClients = $db->prepare($sqlClients);
$queryClients->execute([
    "username" => $username
]);

$clients = $queryClients->fetch();

if ($clients['admin'] === '1') {

    // Add

    if (isset($_POST['submitAdd'])){

        if (isset($_POST['telephone']) && $_POST['telephone'] != ''
            && isset($_POST['raison_social']) && $_POST['raison_social'] != '') {

            $raison_social = htmlspecialchars(trim($_POST['raison_social']));
            $raison_social = stripslashes($raison_social);
            $raison_socialLower = strtolower($raison_social);
            $nom = htmlspecialchars(trim($_POST['nom']));
            $nom = stripslashes($nom);
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $prenom = stripslashes($prenom);
            $telephone = htmlspecialchars(trim($_POST['telephone']));
            $telephone = stripslashes($telephone);

            $a = 0;

            $sqlcheckFournisseurs = "SELECT * FROM fournisseur";

            $querycheckFournisseurs = $db->prepare($sqlcheckFournisseurs);
            $querycheckFournisseurs->execute();

            $checkFournisseurs = $querycheckFournisseurs->fetchAll();

            foreach ($checkFournisseurs as $checkFournisseur) {
                if (strtolower($checkFournisseur['raison_social']) === $raison_socialLower) {
                    $a++;
                }
            }

            if ($a > 0 ) {
                echo "<script>alert('Fournisseur ". $raison_social. " déjà enregistré')</script>";
            } else {

            $sql = "INSERT INTO fournisseur(raison_social, nom, prenom, telephone)
            VALUES (:raison_social, :nom, :prenom, :telephone);";

            $query = $db->prepare($sql);
            $query->execute([
                "raison_social" => $raison_social,
                "nom" => $nom,
                "prenom" => $prenom,
                "telephone" => $telephone,
            ]);

            header("Location:fournisseurs.php");

            }
        } else {
            echo "<script>alert('Merci de remplir correctement le formulaire');</script>";
        }
    }

    // Edit

    if (isset($_POST['submitEdit'])) {

        if (isset($_POST['raison_social']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['telephone'])) {

            if (!isset($_POST['id']) || intval($_POST['id']) == 0) {
                header('Location:fournisseurs.php');
            }

            $id = $_POST['id'];

            $sql = "SELECT * FROM fournisseur
            WHERE id_fournisseur = $id";

            $query = $db->prepare($sql);
            $query->execute();

            $fournisseur = $query->fetch();

            if ($fournisseur === false){
                header('Location:fournisseurs.php');
            }

            $raison_social = htmlspecialchars(trim($_POST['raison_social']));
            $raison_social = stripslashes($raison_social);
            $raison_socialLower = strtolower($raison_social);
            $nom = htmlspecialchars(trim($_POST['nom']));
            $nom = stripslashes($nom);
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $prenom = stripslashes($prenom);
            $telephone = htmlspecialchars(trim($_POST['telephone']));
            $telephone = stripslashes($telephone);

            $a = 0;

            $sqlcheckFournisseurs = "SELECT * FROM fournisseur";

            $querycheckFournisseurs = $db->prepare($sqlcheckFournisseurs);
            $querycheckFournisseurs->execute();

            $checkFournisseurs = $querycheckFournisseurs->fetchAll();

            foreach ($checkFournisseurs as $checkFournisseur) {
                if (strtolower($checkFournisseur['raison_social']) === $raison_socialLower && $raison_socialLower !== strtolower($checkFournisseur['raison_social'])) {
                    $a++;
                }
            }

            if ($a > 0 ) {
                echo "<script>alert('Fournisseur ". $raison_social. " déjà enregistré')</script>";
            } else {

                $sqlEdit = "UPDATE fournisseur
                            SET raison_social = :raison_social, nom = :nom, prenom = :prenom, telephone = :telephone
                            WHERE id_fournisseur = $id;";

                $queryUpdate = $db->prepare($sqlEdit);
                $queryUpdate->execute([
                    "raison_social" => $raison_social,
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "telephone" => $telephone,
                ]);

                header('Location:fournisseurs.php');
            }
        } else {
            header('Location: fournisseurs.php');
        }
    }

    // Suppression

    $sql = "SELECT * FROM fournisseur
    ORDER BY id_fournisseur DESC
    LIMIT 11";
    $query = $db->prepare($sql);
    $query->execute();

    $fournisseurs = $query->fetchAll();

    if (isset($_POST['submitDelete']) && isset($_POST['id'])) {

        $password = htmlspecialchars($_POST['password']);

        if ($clients['username'] && password_verify($password, $clients['password']) || $clients['email'] && password_verify($password, $clients['password'])) {

            $id = intval($_POST['id']);

            $sqlDelete = "DELETE FROM fournisseur_fleur
                        WHERE id_fournisseur = :id";
            $queryDelete = $db->prepare($sqlDelete);
            $queryDelete->execute([
                "id" => $id
            ]);

            $sql = "DELETE FROM fournisseur
                    WHERE id_fournisseur = :id";

            $query = $db->prepare($sql);
            $query->execute([
                "id" => $id
            ]);

            header('Location: fournisseurs.php');
        } else {
            echo "<script>alert('Mot de passe incorrect');</script>";

        }
    }

    include_once('../header.php'); ?>
    <section class="mainFournisseursContainer">
        <h1 class="mainClientsTitle">Nos fournisseurs</h1>
        <p class="mainFournisseursContent">Grâce à nos fournisseurs régionaux soigneusement choisi, vous pourrez profiter d'un large choix de roses, chrysanthèmes et de bien d'autres spécialement sélectionner et cultiver avec soin.</p>
    </section>
        <main class="mainFournisseurs">

        <!-- Add -->

            <section class="cardClientsAdd" id="cardClientsAdd"><button id="cardClientsPlus" type="button">+</button>
                <h1 class="mainAddTitle">Ajout fournisseur</h1>
                <form method="POST">
                    <div class="cardFournisseurContainer">
                        <div class="cardFournisseurFormContainer">
                            <label for="raison_social" class="cardFournisseurLabel">Raison Social *</label>
                            <input type="text" name="raison_social" placeholder="Fournisseur" class="cardFournisseurInput" required>
                        </div>
                        <div class="cardFournisseurFormContainer">
                            <label for="telephone" class="cardFournisseurLabel">Téléphone *</label>
                            <input type="tel" name="telephone" placeholder="Téléphone" class="cardFournisseurInput" required>
                        </div>
                        <div class="cardFournisseurFormContainer">
                            <label for="nom" class="cardFournisseurLabel">Nom</label>
                            <input type="text" name="nom" placeholder="Nom" class="cardFournisseurInput">
                        </div>
                        <div class="cardFournisseurFormContainer">
                            <label for="prenom" class="cardFournisseurLabel">Prénom</label>
                            <input type="text" name="prenom" placeholder="Prénom" class="cardFournisseurInput">
                        </div>
                    </div>
                    <input type="submit" name="submitAdd" value="Ajouter"  class='cardClientsAddSubmit'>
                </form>
            </section>

            <!-- Edit -->

            <?php foreach($fournisseurs as $fournisseur){ ?>

                <div class="cardFournisseurs">
                <?php $id_fournisseur = $fournisseur['id_fournisseur']; ?>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?=$id_fournisseur?>">
                        <div class="cardFournisseurContainer">
                            <div class="cardFournisseurFormContainer">
                                <label for="raison_social" class="cardFournisseurLabel">Raison Social *</label>
                                <input type="text" name="raison_social" value="<?= $fournisseur['raison_social']; ?>" placeholder="Fournisseur" class="cardFournisseurInput" required>
                            </div>
                            <div class="cardFournisseurFormContainer">
                                <label for="telephone" class="cardFournisseurLabel">Téléphone *</label>
                                <input type="tel" name="telephone" value="<?= $fournisseur['telephone']; ?>" placeholder="Téléphone" class="cardFournisseurInput" required>
                            </div>
                            <div class="cardFournisseurFormContainer">
                                <label for="nom" class="cardFournisseurLabel">Nom</label>
                                <input type="text" name="nom" value="<?= $fournisseur['nom']; ?>" placeholder="Nom" class="cardFournisseurInput">
                            </div>
                            <div class="cardFournisseurFormContainer">
                                <label for="prenom" class="cardFournisseurLabel">Prénom</label>
                                <input type="text" name="prenom" value="<?= $fournisseur['prenom']; ?>" placeholder="Prénom" class="cardFournisseurInput">
                            </div>
                        </div>
                        <div class="cardFournisseurAdmin">
                            <input type="submit" name="submitEdit" value="Modifier"  class='mainFournisseursEdit'>
                            <button type="button" id="deleteFournisseurFirstForm" class="mainFournisseursDelete" onclick="showDeleteForm(this)">Supprimer</button>
                        </div>
                    </form>

                    <!-- Delete -->

                    <form method="POST" class="deleteClientFinalForm" id="deleteFournisseurFinalForm">
                        <input type="hidden" name="id" value="<?= $id_fournisseur; ?>" >
                        <label for="password">Afin de supprimer définitivement votre compte, merci de saisir votre mot de passe </label>
                        <input type="password" name="password" placeholder="Mot de passe" class="cardFournisseurInputPasswordDelete">
                        <input type="submit" name="submitDelete" value="Supprimer" class="mainFournisseursDeleteConfirmation">
                    </form>

                </div>
            <?php } ?>

        </main>

<?php include_once('../footer.php'); ?>
<script src="../../javascript/addClient.js"></script>

<?php
}

if ($clients['admin'] === '0') {

    $sql = "SELECT * FROM fournisseur
    ORDER BY id_fournisseur DESC
    LIMIT 10";
    $query = $db->prepare($sql);
    $query->execute();

    $fournisseurs = $query->fetchAll();

    include_once('../header.php'); ?>

            <section class="mainFournisseursContainer">
                <h1 class="mainClientsTitle">Nos fournisseurs</h1>
                <p class="mainFournisseursContent">Grâce à nos fournisseurs régionaux soigneusement choisi, vous pourrez profiter d'un large choix de roses, chrysanthèmes et de bien d'autres spécialement sélectionner et cultiver avec soin.</p>
            </section>
           <main class="mainFournisseurs">

               <?php foreach($fournisseurs as $fournisseur){ ?>

                   <div class="cardFournisseurs">
                   <?php $id_fournisseur = $fournisseur['id_fournisseur']; ?>
                       <p class="mainFournisseursVariete">Raison Social : <?= $fournisseur['raison_social']; ?></p>
                       <p class="mainFournisseursCouleur">Nom : <?= $fournisseur['nom']; ?></p>
                       <p class="mainFournisseursPrix">Prénom : <?= $fournisseur['prenom']; ?></p>
                       <a class="mainFournisseursTelephoneClient" href="tel:<?= $fournisseur['telephone']; ?>"><?= $fournisseur['telephone']; ?></a>
                   </div>
               <?php } ?>

           </main>

<?php include_once('../footer.php');

    }

    } else {
        $sql = "SELECT * FROM fournisseur
        ORDER BY id_fournisseur DESC
        LIMIT 10";
        $query = $db->prepare($sql);
        $query->execute();

        $fournisseurs = $query->fetchAll();

        include_once('../header.php'); ?>

                <section class="mainFournisseursContainer">
                    <h1 class="mainClientsTitle">Nos fournisseurs</h1>
                    <p class="mainFournisseursContent">Grâce à nos fournisseurs régionaux soigneusement choisi, vous pourrez profiter d'un large choix de roses, chrysanthèmes et de bien d'autres spécialement sélectionner et cultiver avec soin.</p>
                </section>
                <main class="mainFournisseurs">

                    <?php foreach($fournisseurs as $fournisseur){ ?>

                        <div class="cardFournisseurs">
                        <?php $id_fournisseur = $fournisseur['id_fournisseur']; ?>
                            <p class="mainFournisseursVariete">Raison Social : <?= $fournisseur['raison_social']; ?></p>
                            <p class="mainFournisseursCouleur">Nom : <?= $fournisseur['nom']; ?></p>
                            <p class="mainFournisseursPrix">Prénom : <?= $fournisseur['prenom']; ?></p>
                            <a class="mainFournisseursTelephoneClient" href="tel:<?= $fournisseur['telephone']; ?>"><?= $fournisseur['telephone']; ?></a>
                        </div>
                    <?php } ?>

                </main>
                <?php include_once('../footer.php');

              } ?>
