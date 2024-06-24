<?php

require_once("connexion.php");
$link = "../";
$title = "Mentions légales - Wild";
$script = "";
$pages = "";
$home = "../../";

if ($_COOKIE == null) {
    include_once('cookiesBanner.php');
}

  include_once('header.php'); ?>

    <section class="mentions">
        <h1 class="mentionsTitle">Politique de confidentialité</h1>
        <p class="mentionsContent">Wild ainsi que les membres de notre équipe s’engagent à l’utilisation des données uniquement pour l’exécution des commandes, des échanges ...</p>
        <h2 class="mentionsSubTitle">Quelles sont les données personnelles collectées ?</h2>
        <p class="mentionsContent">Nous collectons notamment votre nom, prénom, mail, téléphone, coordonnées ainsi que votre historique de commande.</p>
        <h2 class="mentionsSubTitle">Dans quel but vos données sont-elles collectées ?</h2>
        <p class="mentionsContent">Gestion des commandes</p>
        <p class="mentionsContent">Échange si nécessaire</p>
        <h2 class="mentionsSubTitle">Des questions ? Contact</h2>
        <a href="mailto:contact@wild.com" class="mentionsLink">contact@wild.com</a>
        <a href="tel:0686596702" class="mentionsLink">06.86.59.67.02</a>
        <p class="mentionsContent">Du lundi au samedi 8h - 20h</p>
        <p class="mentionsContent">25 Place des épars</p>
        <p class="mentionsContentLast">28000 Chartres</p>
    </section>

    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <script src="../../javascript/returnTop.js"></script>
    <script src="../../javascript/scroll.js"></script>

    <?php include_once('footer.php'); ?>
