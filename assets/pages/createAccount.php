<?php

if (isset($_COOKIE['user'])) {
    header('Location:../index.php');
}

if ($_COOKIE == null) {
    include_once('cookiesBanner.php');
}

require_once("connexion.php");
$link = "../";
$title = "Créer mon compte - Wild";
$script = "";
$pages = "";
$home = "../../";

if (isset($_POST['honeyPot'])) {
    header('Location: login.php');
}

if (isset($_POST['submit']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['gender']) && isset($_POST['email']) && isset($_POST['confirm_password']) && isset($_POST['nom'])
&& isset($_POST['prenom']) && isset($_POST['telephone']) && !empty($_POST['case']) && empty($_POST['honeyPot'])) {

    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $username = htmlspecialchars(trim($_POST['username']));
    $usernameLower = strtolower($username);
    $email = htmlspecialchars(trim($_POST['email']));
    $emailLower = strtolower($email);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $password = htmlspecialchars($_POST['password']);
    $confirmpassword = htmlspecialchars($_POST['confirm_password']);
    $adresse = isset($_POST['adresse']) ? htmlspecialchars(trim($_POST['adresse'])) : 'NULL';
    $code_postal = isset($_POST['code_postal']) ? htmlspecialchars(trim($_POST['code_postal'])) : 'NULL';
    $ville = isset($_POST['ville']) ? htmlspecialchars(trim($_POST['ville'])) : null;
    $gender = $_POST['gender'];
    $ucl = preg_match('/[A-Z]/', $password); // Uppercase Letter
    $lcl = preg_match('/[a-z]/', $password); // Lowercase Letter
    $dig = preg_match('/\d/', $password); // Numeral
    $nos = preg_match('/\W/', $password); // Non-alpha/num characters (allows underscore)

    if ($gender === null) {
        echo "<script>alert('Sélectionner votre genre')</script>";
    } else if ($prenom === "") {
        echo "<script>alert('Prénom obligatoire')</script>";
    } else if ($prenom !== "" && strlen($_POST['prenom']) > 80) {
        echo "<script>alert('Prénom trop long. Si le champ est correctement renseigné, merci de nous contacter')</script>";
    } else if ($nom === "") {
        echo "<script>alert('Nom obligatoire')</script>";
    } else if ($nom !== "" && strlen($_POST['nom']) > 80) {
        echo "<script>alert('Nom trop long. Si le champ est correctement renseigné, merci de nous contacter')</script>";
    } else if ($usernameLower === "") {
        echo "<script>alert('Username obligatoire')</script>";
    } else if ($usernameLower !== "" && strlen($_POST['username']) > 0 && strlen($_POST['username']) < 8) {
        echo "<script>alert('Identifiant trop court : 8 caractères minimum')</script>";
    } else if ($usernameLower !== "" && strlen($_POST['username']) > 50) {
        echo "<script>alert('Identifiant trop long : 50 caractères maximum')</script>";
    } else if ($password === "") {
        echo "<script>alert('Mot de passe obligatoire')</script>";
    } else if ($password !== "" && strlen($_POST['password']) > 0 && strlen($_POST['username']) < 8) {
        echo "<script>alert('Mot de passe trop court : 8 caractères minimum')</script>";
    } else if ($password !== "" && strlen($_POST['password']) > 8 && !$ucl && !$lcl && !$dig && !$nos) {
        echo "<script>alert('Une majuscule, minuscule, chiffre et caractère spécial requis')</script>";
    } else if ($password !== $confirmpassword) {
        echo "<script>alert('Les mots de passe doivent correspondre')</script>";
    } else {

        $sqlUsername = "SELECT username FROM client WHERE username = :user";
        $queryUsername = $db->prepare($sqlUsername);
        $queryUsername->execute([
            "user" => $usernameLower,
        ]);
        $user = $queryUsername->fetch();

        $sqlEmail = "SELECT email FROM client WHERE email = :email";
        $queryEmail = $db->prepare($sqlEmail);
        $queryEmail->execute([
            "email" => $emailLower,
        ]);
        $userEmail = $queryEmail->fetch();

        $sqlTelephone = "SELECT telephone FROM client WHERE telephone = :telephone";
        $queryTelephone = $db->prepare($sqlTelephone);
        $queryTelephone->execute([
            "telephone" => $telephone,
        ]);
        $userTelephone = $queryTelephone->fetch();

        if (!empty($user)) {
            echo "<p class='usernameUsed'>Nom d'utilisateur déjà utilisé.</p>";
        } else if (!empty($userEmail)) {
            echo "<p class='usernameUsed'>Email déjà utilisé.</p>";
        } else if (!empty($userTelephone)) {
            echo "<p class='usernameUsed'>Téléphone déjà utilisé.</p>";
        } else {
            $hashedpwd = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO `client`(nom, prenom, telephone, adresse, code_postal, ville, username, `password`, email, gender)
                VALUES (:nom, :prenom, :telephone, :adresse, :code_postal, :ville, :user, :pwd, :email, :gender)";
        $query = $db->prepare($sql);
        $query->execute([
            "nom" => $nom,
            "prenom" => $prenom,
            "telephone" => $telephone,
            "adresse" => $adresse,
            "code_postal" => $code_postal,
            "ville" => $ville,
            "user" => $username,
            "pwd" => $hashedpwd,
            "email" => $email,
            "gender" => $gender
        ]);

        header('Location:login.php');

        }
    }
}

    include_once('header.php'); ?>

    <h1 class="createAccountTitle">Inscription</h1>
<section class="createAccountCard">
    <form method="post">
        <input type="checkbox" name="honeyPot" value="honeyPot" class="honeyPot">
        <div class="createAccountCardInputGender">
            <input type="radio" name="gender" value="M" required>M
            <input type="radio" name="gender" value="Mme" required>Mme
            <input type="radio" name="gender" value="Autre" required>Autre
        </div>
        <div class="createAccountCardInputNom">
            <label for="nom" class="loginCardLabelMail">Nom *</label>
            <input type="text" name="nom" id="nom" placeholder="Nom *" class="createAccountCardInput" value="<?php if (!empty($nom)) { echo $nom; }?>" required>
        </div>
        <div class="createAccountCardInputPrenom">
            <label for="prenom" class="loginCardLabelMail">Prénom *</label>
            <input type="text" name="prenom" id="prenom" placeholder="Prénom *" class="createAccountCardInput" value="<?php if (!empty($prenom)) { echo $prenom; }?>" required>
        </div>
        <div class="createAccountCardInputUsername">
            <label for="username" class="loginCardLabelMail">Username *</label>
            <input type="text" name="username" id="username" placeholder="Nom d'utilisateur *" class="createAccountCardInput" value="<?php if (!empty($username)) { echo $username; }?>" required>
        </div>
        <div class="createAccountCardInputEmail">
            <label for="username" class="loginCardLabelMail">Email *</label>
            <input type="email" name="email" id="email" placeholder="exemple@contact.fr *" class="createAccountCardInput" value="<?php if (!empty($email)) { echo $email; }?>" required>
        </div>
        <div class="createAccountCardInputTelephone">
            <label for="telephone" class="loginCardLabelMail">Téléphone *</label>
            <input type="tel" name="telephone" id="telephone" placeholder="Téléphone *" class="createAccountCardInput" value="<?php if (!empty($telephone)) { echo $telephone; }?>" required>
        </div>
        <div class="createAccountCardInputAdresse">
            <label for="adresse" class="loginCardLabelMail">Adresse</label>
            <input type="text" name="adresse" id="adresse" placeholder="Adresse" class="createAccountCardInput" value="<?php if (!empty($adresse)) { echo $adresse; }?>">
        </div>
        <div class="createAccountCardInputCP">
            <label for="code_postal" class="loginCardLabelMail">Code Postal</label>
            <input type="number" name="code_postal" id="code_postal" placeholder="Code Postal" class="createAccountCardInput" value="<?php if (!empty($code_postal)) { echo $code_postal; }?>">
        </div>
        <div class="createAccountCardInputVille">
            <label for="ville" class="loginCardLabelMail">Ville</label>
            <input type="text" name="ville" id="ville" placeholder="Ville" class="createAccountCardInput" value="<?php if (!empty($ville)) { echo $ville; }?>">
        </div>
        <div class="createPasswordEyesContainer">
        <label for="password" class="loginCardLabelMail">Mot de passe *</label>
        <input type="password" name="password" id="shownPasswordLogin" placeholder="Mot de passe *" class="createAccountCardInput" required>
            <img id="showPasswordLogin" src="../img/eye-regular.svg" class="createShowPasswordLogin">
            <img id="hidePasswordLogin" src="../img/eye-slash-regular.svg" class="createHidePasswordLogin">
        </div>
        <div class="createConfirmPasswordEyesContainer">
        <label for="confirm_password" class="loginCardLabelMail">Confirmer le mot de passe *</label>
        <input type="password" name="confirm_password" id="shownPasswordConfirm" placeholder="Confirmation mot de passe *" class="createAccountCardInput" required>
            <img id="showPasswordConfirm" src="../img/eye-regular.svg" class="createShowPasswordLogin">
            <img id="hidePasswordConfirm" src="../img/eye-slash-regular.svg" class="createHidePasswordLogin">
        </div>
        <div class="createAccountCardCheckbox">
            <input type="checkbox" name="case" value="on" required><label for="case">* En cochant cette case, j'accepte que mes informations soient utilisées pour le traitement de ma demande. <a href="#">En savoir plus.</a></label>
        </div>
        <p class="createAccountContent">*Champs obligatoires</p>
        <input type="submit" name="submit" value="Créer mon compte" class="createAccountCardSubmit" onclick="verification()" required>
    </form>
</section>

<?php include_once('footer.php'); ?>

<script src="../javascript/scroll.js"></script>
<script src="../javascript/showPassword.js"></script>
