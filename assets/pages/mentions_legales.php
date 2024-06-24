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
        <h1 class="mentionsTitle">Mentions légales</h1>
        <p class="mentionsContent">Le site Wild est édité par Nicolas Stenger, au capital de 0€, numéro RCS inconnu par les autorités, dont le siège social est situé sur son fauteuil.</p>
        <h2 class="mentionsSubTitle">Directeur de la publication</h2>
        <p class="mentionsContent">Nicolas Stenger</p>
        <h2 class="mentionsSubTitle">Contact</h2>
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
