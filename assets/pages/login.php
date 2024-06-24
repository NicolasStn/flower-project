<?php

if (isset($_COOKIE['user'])) {
    header('Location: ../../index.php');
}

if ($_COOKIE == null) {
    include_once('cookiesBanner.php');
}

require_once("connexion.php");
$link = "../";
$title = "Se connecter - Wild";
$script = "";
$pages = "";
$home = "../../";

if (isset($_POST['submit']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $username = stripslashes($username);
    $password = htmlspecialchars($_POST['password']);

    $sql = "SELECT `username`, `email`, `password` FROM client WHERE username = :user OR email = :user";
    $query = $db->prepare($sql);
    $query->execute([
        "user" => $username,
    ]);

    $user = $query->fetch();

    if ($user === false) {

        echo "<p class=usernameUsed>Mot de passe ou nom d'utilisateur/email incorrect.</p>";
    } else if ($user['username'] && password_verify($password, $user['password']) || $user['email'] && password_verify($password, $user['password'])) {

        if ($_COOKIE != null) {

            $sqlVerif = "SELECT `username`, `email` FROM client WHERE username = :user OR email = :user";
            $queryVerif = $db->prepare($sqlVerif);
            $queryVerif->execute([
                "user" => $username,
            ]);

            $userVerif = $queryVerif->fetch();

            $cookie_name = "user";
            $cookie_value = $userVerif['username'];

            if (isset($_COOKIE['visitor'])) {
                setcookie("visitor", $_COOKIE['visitor'], time() - (86400 * 30), "/");
            }

            setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
            header('Location:../../index.php');

        } else {
            echo "<script>alert('Merci d'accepter les cookies');</script>";
        }

    } else {
        echo "<p class=usernameUsed>Mot de passe ou nom d'utilisateur/email incorrect.</p>";
    }
}

include_once('header.php'); ?>

<section class="loginCard">
    <h1 class="loginCardTitle">Se connecter</h1>
    <form action="" method="post">
        <label for="username" class="loginCardLabelMail">Username / email *</label>
        <input type="text" placeholder="Username ou Email" name="username" class="loginCardInputMail" required>
        <label for="username" class="loginCardLabel">Mot de passe *</label>
        <div class="passwordEyesContainer">
            <input type="password" id="shownPasswordLogin" placeholder="Mot de passe" name="password" class="loginCardInputPassword" required>
            <img id="showPasswordLogin" src="../img/eye-regular.svg" class="showPasswordLogin">
            <img id="hidePasswordLogin" src="../img/eye-slash-regular.svg" class="hidePasswordLogin">
        </div>
        <a href="forgetPassword.php" class="loginCardPassword">Mot de passe oublié ?</a>
        <input type="submit" value="Se connecter" name="submit" class="loginCardSubmit">
    </form>
    <div class="loginCardCreate">
        <div class="loginCardSeparate"></div>
        <a href="createAccount.php" class="loginCardBtn">Créer un compte</a>
    </div>
</section>

<?php include_once('footer.php'); ?>

<script src="../javascript/showPassword.js"></script>
