<?php

require_once("connexion.php");
$link = "../";
$title = "Mot de passe réinitialisé - Wild";
$script = "";
$pages = "";
$home = "../../";

if (isset($_COOKIE['user'])) {
    header('Location: ../../index.php');
}

if ($_COOKIE == null) {
    include_once('cookiesBanner.php');
}

  include_once('header.php'); ?>

    <div class="confirmPassword">
        <p class="confirmPasswordContent">Votre demande de réinitialisation de mot de passe a bien été prise en compte. Si votre adresse mail est lié à un compte existant, vous recevrez d'ici peu un lien vous permettant de réinitialiser votre mot de passe.
            Merci de vérifier vos mails !</p>
    </div>

    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <script src="../../javascript/returnTop.js"></script>
    <script src="../../javascript/scroll.js"></script>

    <?php include_once('footer.php'); ?>
