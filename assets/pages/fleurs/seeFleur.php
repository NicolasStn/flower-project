<?php

require_once('../connexion.php');
$link = "../../";
$script = "<script src='//cdnjs.cloudflare.com/ajax/libs/flickity/1.0.0/flickity.pkgd.js'></script>";
$pages = "../";
$home = "../../../";

if (!isset($_GET['variete']) || !isset($_GET['couleur'])) {
    header('Location:fleurs.php');
}

if (isset($_GET['variete']) && isset($_GET['couleur'])) {
    $variete = $_GET['variete'];
    $couleur = $_GET['couleur'];
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

$sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
        INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
        INNER JOIN variete ON fleur.id_variete = variete.id_variete
        INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
        WHERE variete.libelle = '$variete' AND couleur.libelle = '$couleur'";

$query = $db->prepare($sql);
$query->execute();

$fleurs = $query->fetch();

    $title = $fleurs['variete']." ".$fleurs['couleur'];
    include_once('../header.php');

    if (isset($_COOKIE['user'])) {
        $username = $_COOKIE['user'];

        $sqlClients = "SELECT username, admin FROM client
        WHERE username = :username";
        $queryClients = $db->prepare($sqlClients);
        $queryClients->execute([
            "username" => $username
        ]);

        $clients = $queryClients->fetch();

        if ($clients['admin'] === '1') {  ?>
            <div class="seeFleurAdmin">
        <?php } else { ?>
            <div class="seeFleur">
        <?php }
        } else {
            echo "<div class='seeFleur'>";
        } ?>

        <img alt="Fleur" class="seeFleurImg" src="<?=$fleurs['img']?>">
        <section class="seeFleurContainer">

            <?php

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
                    <a href='deleteFleur.php?id=<?=$fleurs['id_fleur']?>'><img alt='Trash' src='../../img/trash.png' class='cardFleursTrash'></a>
                    <a href='editFleur.php?id=<?=$fleurs['id_fleur']?>'><img alt='Trash' src='../../img/edit.png' class='cardFleursEdit'></a>
                <?php } } ?>
            <h1 class="seeFleurVariete"><?=$fleurs['variete']. " ". $fleurs['couleur']?></h1>
            <p class="seeFleurDesc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil, totam! Iste reiciendis ea suscipit. Odit nesciunt ut, sequi consequatur dolore nemo reiciendis praesentium, fugit magnam placeat adipisci consequuntur necessitatibus distinctio.</p>
            <?php if ($fleurs['stock'] > 0) { ?>
                <p class="seeFleurPrice"><?=$fleurs['prix']."€ / u"?></p>
                <form method="post" action="../cart/cart.php" class="seeFleurForm">
            <?php } ?>
                    <input type="hidden" name="variete" <?= "value='".$fleurs['variete']."'"?>>
                    <input type="hidden" name="couleur" <?= "value='".$fleurs['couleur']."'"?>>
            <?php if ($fleurs['stock'] > 0) { ?>

                    <label for="quantity" class="seeFleurQuantity">Quantité : </label>
                    <select id="quantity" name="quantity" class="seeFleurQuantity">
                        <?php for ($i = 1; $i < 21; $i++) { ?>
                            <option class="seeFleurQuantityOption" value="<?=$i?>"><?=$i?></option>
                        <?php } ?>
                    </select>
                <?php }
                if ($fleurs['stock'] > 0) { ?>
                    <input type="submit" class="seeFleurCart" value="Ajouter au panier" name="submit">
                <?php } else { ?>
                    <button class="seeFleurCartRupture">Rupture de stock</button>
                <?php } ?>

                </form>
        </section>
    </div>

    <div class="homeFlowerSeparate"></div>
    <h1 class="homeFlowerSeparateTitle">Ces produits pourraient vous intéresser</h1>

    <div class="homeFlowerContainer">

        <?php

        $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
        INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
        INNER JOIN variete ON fleur.id_variete = variete.id_variete
        INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
        GROUP BY fleur.id_fleur
        ORDER BY RAND()
        LIMIT 4";

        $query = $db->prepare($sql);
        $query->execute();

        $fleurs = $query->fetchAll();

        foreach($fleurs as $fleur) {
            $id_fleur = $fleur['id_fleur']; ?>
            <div class="cardFleurs" id="cardFleurs">
                <?php if (intval($fleur['stock']) < 1) {
                    echo    "<div class='cardFleursRupture'>
                                <p class='cardFleursRuptureParagraph'>Rupture de stock</p>
                            </div>";
                } ?>
                <a href="seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                    <div class="cardFleursImgShapeIndex">
                        <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                    </div>
                    <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                    <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                </a>
            </div>

        <?php } ?>

    </div>

    <div class="gallery js-flickity" data-flickity-options='{ "wrapAround": true }'>

        <?php

        $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fournisseur_fleur.stock AS stock, fleur.img AS img, fleur.id_fleur AS id_fleur FROM fleur
        INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
        INNER JOIN variete ON fleur.id_variete = variete.id_variete
        INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
        GROUP BY fleur.id_fleur
        ORDER BY RAND()
        LIMIT 4";

        $query = $db->prepare($sql);
        $query->execute();

        $fleurs = $query->fetchAll();

        foreach($fleurs as $fleur) {
            $id_fleur = $fleur['id_fleur']; ?>
            <div class="gallery-cell" id="cardFleurs">
                <?php if (intval($fleur['stock']) < 1) {
                    echo    "<div class='cardFleursRupture'>
                                <p class='cardFleursRuptureParagraph'>Rupture de stock</p>
                            </div>";
                } ?>
                <a href="seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                    <div class="cardFleursImgShapeIndex">
                        <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                    </div>
                    <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                    <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                </a>
            </div>

        <?php } ?>

    </div>

    <a href="fleurs.php" class="homeSeeMore">Voir plus ></a>

<?php include_once('../footer.php'); ?>
