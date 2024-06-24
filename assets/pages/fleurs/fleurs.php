<?php

require_once('../connexion.php');

$link = "../../";
$title = "Nos fleurs - Wild";
$script = "";
$pages = "../";
$home = "../../../";

if (!isset($_GET['page'])) {
    $page = 1;
    $limit = 20;
    $limitShow = $limit * $page;
}

if (isset($_GET['page'])) {
    $page = 1 * $_GET['page'];
    $limit = 20;
    $limitShow = $limit * $page;
}

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

    include_once('../header.php'); ?>

    <section class="mainFleursContainer">
        <h1 class="mainClientsTitle">Nos fleurs</h1>
        <h2 class="mainFleursSecondTitle">Fleurs de la région</h2>
        <p class="mainFleursContent">Découvrez dès maintenant les fleurs de notre belle région, parfaite pour toute occasion grâce à notre large gamme de choix. Réservation en un éclair partout en France.
        Vous pourrez profiter d'un large choix de roses, chrysanthèmes et de bien d'autres spécialement sélectionner et cultiver avec soin par nos fournisseurs régionaux exclusivement.</p>
    </section>
    <div class="fleursForm" id="fleursForm">
        <form method="get" id="fleursFormTri">
            <select name="tri" id="tri" class="planningFormTri">
                <option value="default">Par Default</option>
                <option value="alph-asc">Par Ordre alphabétique A - Z</option>
                <option value="alph-desc">Par Ordre alphabétique Z - A</option>
                <option value="color-asc">Par Couleur</option>
                <option value="color-desc">Par Couleur décroissant</option>
                <option value="prix-asc">Par Prix croissant</option>
                <option value="prix-desc">Par Prix décroissant</option>
            </select>

            <?php if (isset($_GET['page'])) {
                echo "<input type='hidden' value='". $_GET['page']. "'>";
            } ?>

            <input type="submit" name="submitBtn" class="planningFormSubmit" id="submitBtn" value="Appliquer">
        </form>
    </div>

    <main class="mainFleurs">

        <?php

        if (isset($_GET['tri'])) { // execution du sql en fonction de l'option choisi plus haut avec le select
            if ($_GET['tri'] === 'default') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY fleur.id_fleur DESC
                LIMIT $limitShow";
            } else if ($_GET['tri'] ===  'alph-asc') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY variete.libelle ASC
                LIMIT $limitShow";
            } else if ($_GET['tri'] === 'alph-desc') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY variete.libelle DESC
                LIMIT $limitShow";
            } else if ($_GET['tri'] ===  'color-asc') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY couleur.libelle ASC
                LIMIT $limitShow";
            } else if ($_GET['tri'] ===  'color-desc') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY couleur.libelle DESC
                LIMIT $limitShow";
            } else if ($_GET['tri'] ===  'prix-asc') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY fleur.prix ASC
                LIMIT $limitShow";
            } else if ($_GET['tri'] ===  'prix-desc') {
                $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                GROUP BY fleur.id_fleur
                ORDER BY fleur.prix DESC
                LIMIT $limitShow";
            } else {
            $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
            INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
            INNER JOIN variete ON fleur.id_variete = variete.id_variete
            INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
            GROUP BY fleur.id_fleur
            ORDER BY fleur.id_fleur DESC
            LIMIT $limitShow";
            }

            $query = $db->prepare($sql);
            $query->execute();

            $fleurs = $query->fetchAll();

            if (isset($_COOKIE['user'])) {

                $username = $_COOKIE['user'];

                $sqlClients = "SELECT username, admin FROM client
                                WHERE username = :username";
                $queryClients = $db->prepare($sqlClients);
                $queryClients->execute([
                    "username" => $username
                ]);

                $clients = $queryClients->fetch();

                if ($clients['admin'] === '1') { ?>

                    <div class="cardClientsAdd" id="cardClientsAdd"><button  onclick="window.location.href='addFleur.php'" id="cardClientsPlus" class="cardFleursAddBtn">+</button></div>
                <?php } }

            foreach($fleurs as $fleur) { ?>

                <?php $id_fleur = $fleur['id_fleur']; ?>
                <div class="cardFleurs" id="cardFleurs">

                    <?php if (intval($fleur['stock']) < 1) {
                        echo    "<div class='cardFleursRupture'>
                                    <p class='cardFleursRuptureParagraph'>Rupture de stock</p>
                                </div>";
                    } ?>

                    <a href="seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                        <div class="cardFleursImgShape">
                            <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                        </div>
                        <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                        <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                        <?php   if (isset($_COOKIE['user'])) {

                            $username = $_COOKIE['user'];

                            $sqlClients = "SELECT username, admin FROM client
                            WHERE username = :username";
                            $queryClients = $db->prepare($sqlClients);
                            $queryClients->execute([
                                "username" => $username
                            ]);

                            $clients = $queryClients->fetch();

                            if ($clients['admin'] === '1') { ?>
                                                    <p class="mainFleursStock" id="mainFleursStock">Stock : <?= $fleur['stock']; ?></p>
                                                    <a href='editFleur.php?id=<?=$id_fleur?>'><img alt='Trash' src='../../img/edit.png' class='cardFleursEdit'></a>
                                                    <a href='deleteFleur.php?id=<?=$id_fleur?>'><img alt='Trash' src='../../img/trash.png' class='cardFleursTrash'></a>
                            <?php } } ?>
                    </a>
                </div>
            <?php } ?>
    </main>

    <?php } else { ?>

    <?php

    $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
    INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
    INNER JOIN variete ON fleur.id_variete = variete.id_variete
    INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
    GROUP BY fleur.id_fleur
    ORDER BY fleur.id_fleur DESC
    LIMIT $limitShow";

    $query = $db->prepare($sql);
    $query->execute();

    $fleurs = $query->fetchAll();

    if (isset($_COOKIE['user'])) {

        $username = $_COOKIE['user'];

        $sqlClients = "SELECT username, admin FROM client
        WHERE username = :username";
        $queryClients = $db->prepare($sqlClients);
        $queryClients->execute([
            "username" => $username
        ]);

        $clients = $queryClients->fetch();

        if ($clients['admin'] === '1') { ?>

            <div class="cardClientsAdd" id="cardClientsAdd"><button  onclick="window.location.href='addFleur.php'" id="cardClientsPlus" class="cardFleursAddBtn">+</button></div>
        <?php } }

    foreach($fleurs as $fleur) {
        $id_fleur = $fleur['id_fleur']; ?>
        <div class="cardFleurs" id="cardFleurs">
            <?php if (intval($fleur['stock']) < 1) {
                echo    "<div class='cardFleursRupture'>
                            <p class='cardFleursRuptureParagraph'>Rupture de stock</p>
                        </div>";
            } ?>
            <a href="seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                <div class="cardFleursImgShape">
                    <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                </div>
                <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                <?php if (isset($_COOKIE['user'])) {

                    $username = $_COOKIE['user'];

                    $sqlClients = "SELECT username, admin FROM client
                    WHERE username = :username";
                    $queryClients = $db->prepare($sqlClients);
                    $queryClients->execute([
                        "username" => $username
                    ]);

                    $clients = $queryClients->fetch();

                    if ($clients['admin'] === '1') { ?>
                        <p class="mainFleursStock" id="mainFleursStock">Stock : <?= $fleur['stock']; ?></p>
                        <a href='editFleur.php?id=<?=$id_fleur?>'><img alt='Trash' src='../../img/edit.png' <?php if (intval($fleur['stock']) > 0) { ?> class='cardFleursEdit' <?php } if (intval($fleur['stock']) < 1) { ?> class='cardFleursEditRupture' <?php } ?>></a>
                        <a href='deleteFleur.php?id=<?=$id_fleur?>'><img alt='Trash' src='../../img/trash.png' <?php if (intval($fleur['stock']) > 0) { ?> class='cardFleursTrash' <?php } if (intval($fleur['stock']) < 1) { ?> class='cardFleursTrashRupture' <?php } ?>></a>
                    <?php } } ?>
                </a>
            </div>
    <?php } ?>
    </main>

    <?php }

    include_once('../footer.php'); ?>
    <script src="../../javascript/fleursTri.js"></script>
