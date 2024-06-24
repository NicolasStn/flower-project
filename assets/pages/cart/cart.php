<?php

require_once("../connexion.php");

$link = "../../";
$script = "<script src='//cdnjs.cloudflare.com/ajax/libs/flickity/1.0.0/flickity.pkgd.js'></script>";
$pages = "../";
$home = "../../../";

if (isset($_COOKIE['user'])) {
    $username = $_COOKIE["user"];
}

if ($_COOKIE == null) { ?>
    <div class="cookieContainer" id="cookieContainer">
        <h1 class="cookieTitle">Des cookies pour une vie fleurie</h1>
        <p class="cookieContent">Bonjour ! Wild utilise des cookies techniques et des cookies fonctionnels. Vous pouvez accepter ou refuser l'ensemble des cookies et changer d'avis à tout moment. Pour plus d'informations, consultez l'onglet <a href="#">Cookie</a>.</p>
        <div class="cookieContainerBtn">
            <button type="button" id="cookieAccept" class="cookieAccept" onclick="window.location.href='../acceptCookies.php'">Accepter</button>
            <button type="button" id="cookieDelete" class="cookieDelete">Refuser</button>
        </div>
    </div>
    <img src="../../img/cookie.png" alt="Cookie" class="cookieImg" id="cookieImg">
    <script src="../../javascript/closeBanner.js"></script>
<?php }

if (isset($_COOKIE['visitor'])) {
    $username = $_COOKIE["visitor"];
}

// Add to cart

if (!empty($_POST['variete']) && !empty($_POST['couleur']) && !empty($_POST['submit'])) {

    $variete = $_POST['variete'];
    $couleur = $_POST['couleur'];
    $quantity = $_POST['quantity'];

    $sql = "SELECT couleur.libelle AS couleur, variete.libelle AS variete, prix, fleur.img AS img FROM fleur
            INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
            INNER JOIN variete ON fleur.id_variete = variete.id_variete
            INNER JOIN fournisseur_fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
            WHERE variete.libelle = :variete AND couleur.libelle = :couleur";

    $query = $db->prepare($sql);
    $query->execute([
        "variete" => $variete,
        "couleur" => $couleur,
    ]);

    $contents = $query->fetch();

    // Making an empty board

    $list_cart = [];

    // Creating user folder

    if (!is_dir('user')) {
        mkdir('user');
    }

    if (isset($_COOKIE['user'])) {

        $username = $_COOKIE["user"];

        $sqlUsername = "SELECT username FROM client
                        WHERE username = :username";

        $queryUsername = $db->prepare($sqlUsername);
        $queryUsername->execute([
            "username" => $username
        ]);

        $username = $queryUsername->fetch();

        // If favoris.json is existing, then execute the code

        if (is_file('user/'.$username[0].'-cart.json')) {
            $lists_cart = json_decode(file_get_contents('user/'.$username[0].'-cart.json'), true);

            if (!$lists_cart) {
                $file = 'user/'.$username[0].'-cart.json';
                unlink($file);
            }
        }

        if (is_file('user/'.$username[0].'-cart.json')) {
            $lists_cart = json_decode(file_get_contents('user/'.$username[0].'-cart.json'), true);

            $found = false;

            for ($i = 0; $i < count($lists_cart); $i++) {
                $list_cart = $lists_cart[$i];

                if ($list_cart['variete'] == $variete && $list_cart['couleur'] == $couleur) {
                    $quantities = $list_cart['quantity'] + $quantity;
                    $lists_cart[$i]['quantity'] = $quantities;
                    $found = true; // L'élément existe déjà dans le panier
                    break;
                }
            }

            if (!$found) {
                $cart = ['variete' => $contents['variete'], 'couleur' => $contents['couleur'], 'price' => $contents['prix'], 'quantity' => $quantity, 'img' => $contents['img']];
                $lists_cart[] = $cart;
            }

            file_put_contents('user/'.$username[0].'-cart.json', json_encode($lists_cart));
        } else {

            // If the repository is not existing, then the following code create it

            $cart = ['variete' => $contents['variete'], 'couleur' => $contents['couleur'], 'price' => $contents['prix'], 'quantity' => $quantity, 'img' => $contents['img']];
            $lists_cart[] = $cart;

            file_put_contents('user/'.$username[0].'-cart.json',json_encode($lists_cart));
        }

        // Redirecting to cart.php to avoid the form to be uploaded again

        header('Location: cart.php');
    } else if (isset($_COOKIE['visitor'])) {

        $username = $_COOKIE["visitor"];

        // If favoris.json is existing, then execute the code

        if (is_file('user/'.$username.'-cart.json')) {
            $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

            if (!$lists_cart) {
                $file = 'user/'.$username.'-cart.json';
                unlink($file);
            }
        }

        if (is_file('user/'.$username.'-cart.json')) {
            $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

            $found = false;

            for ($i = 0; $i < count($lists_cart); $i++) {
                $list_cart = $lists_cart[$i];

                if ($list_cart['variete'] == $variete && $list_cart['couleur'] == $couleur) {
                    $quantities = $list_cart['quantity'] + $quantity;
                    $lists_cart[$i]['quantity'] = $quantities;
                    $found = true; // L'élément existe déjà dans le panier
                    break;
                }
            }

            if (!$found) {
                $cart = ['variete' => $contents['variete'], 'couleur' => $contents['couleur'], 'price' => $contents['prix'], 'quantity' => $quantity, 'img' => $contents['img']];
                $lists_cart[] = $cart;
            }

            file_put_contents('user/'.$username.'-cart.json', json_encode($lists_cart));
        } else {

            // If the repository is not existing, then the following code create it

            $cart = ['variete' => $contents['variete'], 'couleur' => $contents['couleur'], 'price' => $contents['prix'], 'quantity' => $quantity, 'img' => $contents['img']];
            $lists_cart[] = $cart;

            file_put_contents('user/'.$username.'-cart.json',json_encode($lists_cart));
        }

        // Redirecting to cart.php to avoid the form to be uploaded again

        header('Location: cart.php');
    }
}

// Update quantity

if (!empty($_POST['variete']) && !empty($_POST['couleur']) && !empty($_POST['newQuantity'])) {

    $variete = $_POST['variete'];
    $couleur = $_POST['couleur'];
    $quantity = $_POST['newQuantity'];

    // Making an empty board

    $list_cart = [];

    if (isset($_COOKIE['user'])) {

        $username = $_COOKIE["user"];

        $sqlUsername = "SELECT username FROM client
                        WHERE username = :username";

        $queryUsername = $db->prepare($sqlUsername);
        $queryUsername->execute([
            "username" => $username
        ]);

        $username = $queryUsername->fetch();

        // If favoris.json is existing, then execute the code

        if (is_file('user/'.$username[0].'-cart.json')) {
            $lists_cart = json_decode(file_get_contents('user/'.$username[0].'-cart.json'), true);

            if (!$lists_cart) {
                $file = 'user/'.$username[0].'-cart.json';
                unlink($file);
            }
        }

        $lists_cart = json_decode(file_get_contents('user/'.$username[0].'-cart.json'), true);

        $found = false;

        foreach ($lists_cart as $index => $cart) {
            if ($cart['variete'] == $variete && $cart['couleur'] == $couleur) {
                $lists_cart[$index]['quantity'] = $quantity;
                $found = true; // L'élément existe déjà dans le panier
                break;
            }
        }

        if (!$found) {
            $lists_cart[] = array(
                'variete' => $variete,
                'couleur' => $couleur,
                'quantity' => $quantity
            );
        }

        // Mettez à jour le fichier JSON avec les nouvelles données
        file_put_contents('user/'.$username[0].'-cart.json', json_encode($lists_cart));

        // Redirecting to cart.php to avoid the form to be uploaded again

        header('Location: cart.php');
    } else if (isset($_COOKIE['visitor'])) {

        $username = $_COOKIE["visitor"];

        // If favoris.json is existing, then execute the code

        if (is_file('user/'.$username.'-cart.json')) {
            $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

            if (!$lists_cart) {
                $file = 'user/'.$username.'-cart.json';
                unlink($file);
            }
        }

        $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

        $found = false;

        foreach ($lists_cart as $index => $cart) {
            if ($cart['variete'] == $variete && $cart['couleur'] == $couleur) {
                $lists_cart[$index]['quantity'] = $quantity;
                $found = true; // L'élément existe déjà dans le panier
                break;
            }
        }

        if (!$found) {
            $lists_cart[] = array(
                'variete' => $variete,
                'couleur' => $couleur,
                'quantity' => $quantity
            );
        }

        // Mettez à jour le fichier JSON avec les nouvelles données
        file_put_contents('user/'.$username.'-cart.json', json_encode($lists_cart));

        // Redirecting to cart.php to avoid the form to be uploaded again

        header('Location: cart.php');
    }
}

// Delete from cart

if (!empty($_POST['variete']) && !empty($_POST['couleur']) && !empty($_POST['cartDelete'])) {

    $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

    $variete = $_POST['variete'];
    $couleur = $_POST['couleur'];

    $updated_lists_cart = [];

    foreach ($lists_cart as $index => $cart) {
        if (!($cart['variete'] == $variete && $cart['couleur'] == $couleur)) {
            $updated_lists_cart[] = $cart;
        }
    }

    // Mettez à jour le fichier JSON avec les nouvelles données
    file_put_contents('user/'.$username.'-cart.json', json_encode($updated_lists_cart));

    header('Location: cart.php');

}

// If the user has already been on the site, his articles are shown

$title = "Mon panier - Wild";
include_once('../header.php'); ?>

<h1 class="searchResultsTitle">Votre panier</h1>

<?php $a = 0;

if (isset($_COOKIE["user"])) {
    $username = $_COOKIE["user"];
}

if (isset($_COOKIE["visitor"])) {
    $username = $_COOKIE["visitor"];
}

if ($_COOKIE != null) {

    if (is_file('user/'.$username.'-cart.json')) {
        $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

        if ($lists_cart) {
            $total = 0;

            foreach ($lists_cart as $index => $cart) { ?>
                <div class="maincartContainer">
                    <div class="cartCardContainer">
                        <div class="dayCommandeCardContainerArticles">
                        <a href="../fleurs/seeFleur.php?variete=<?=$cart['variete']?>&couleur=<?=$cart['couleur']?>" class="cartFleurLink">
                            <div class="dayCommandeCardPosition">
                                <img src="<?= $cart['img'] ?>" class="cartCardImg">
                                <p class="dayCommandeCardFleur"><?= $cart['variete'] . " " . $cart['couleur'] ?></p>
                            </div>
                        </a>
                            <div class="dayCommandeCardPosition">
                                <div class="cartDeleteContainer">
                                    <p class="dayCommandeCardPrice"><?= $cart['price'] ?>€</p>
                                    <form method="post">
                                        <input type="hidden" name="variete" value="<?= $cart['variete'] ?>">
                                        <input type="hidden" name="couleur" value="<?= $cart['couleur'] ?>">
                                        <input type="submit" value="Supprimer" name="cartDelete" class="cartDeleteBtn">
                                    </form>
                                </div>
                                <p class="cartCommandeCardContent">x</p>
                                <form method="post" id="fleursFormTri<?= $index ?>" onchange="submitForm('fleursFormTri<?= $index ?>')" class="cartFormUpdateQuantity">
                                    <input type="hidden" name="variete" value="<?= $cart['variete'] ?>">
                                    <input type="hidden" name="couleur" value="<?= $cart['couleur'] ?>">
                                    <p class="cartCommandeCardContentMobile">x</p>
                                    <select id="quantity<?= $index ?>" name="newQuantity" class="cartFormQuantity">
                                        <?php for ($i = 1; $i < 21; $i++) {
                                            if ($i == $cart['quantity']) { ?>
                                                <option value="<?= $i ?>" class="cartFormQuantity" selected><?= $i ?></option>
                                            <?php } else { ?>
                                                <option value="<?= $i ?>" class="cartFormQuantity"><?= $i ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php $total += ($cart['quantity'] * $cart['price']);

            $datedefault =  date("Y-m-d");
            $datemin = date('Y-m-d', strtotime($datedefault. ' + 1 day'));
            } ?>

            <script>
                function submitForm(formId) {
                    document.getElementById(formId).submit();
                }
            </script>

            <div class="cartCardContainerArticles">
                <p class="dayCommandeCardTotal"></p>
                <p class="dayCommandeCardTotal">Total : <?=$total?>€</p>
            </div>

                <form action='confirmCommand.php' method='post' class="cartForm">
                    <div class="cartFormContainer">
                        <input type='hidden' name='id' value="<?= $username ?>">
                        <input type="date" name="date" min="<?= $datemin ?>" class="cartFormDate" required>
                        <select name="hours" class="cartFormHours" required>
                            <?php for ($i = 10; $i <= 19; $i++) {
                                echo "<option class='seeFleurQuantityOption' value='$i:00:00'>$i:00</option>";
                            } ?>
                        </select>
                    </div>
                    <input class='cartFormInput' type='submit' name='submit' value='Réserver'>
                </form>

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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                            <div class="cardFleursImgShapeIndex">
                                <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                            </div>
                            <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                            <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                        </a>
                    </div>

                <?php } ?>

            </div>

            <a href="../fleurs/fleurs.php" class="homeSeeMore">Voir plus ></a>

        <?php } else { ?>

            <div class='searchCart'>
                <p>Votre panier ne comprend actuellement aucune fleur</p>
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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                            <div class="cardFleursImgShapeIndex">
                                <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                            </div>
                            <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                            <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                        </a>
                    </div>

                <?php } ?>

            </div>

            <a href="../fleurs/fleurs.php" class="homeSeeMore">Voir plus ></a>

        <?php }
    } else { ?>

            <div class='searchCart'>
                <p>Votre panier ne comprend actuellement aucune fleur</p>
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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                            <div class="cardFleursImgShapeIndex">
                                <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                            </div>
                            <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                            <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                        </a>
                    </div>

                <?php } ?>

            </div>

            <a href="../fleurs/fleurs.php" class="homeSeeMore">Voir plus ></a>

    <?php }

include_once('../footer.php');

} else { ?>

    <div class='searchCart'><p>Tant que vous n'avez pas accepté les cookies, aucun article ne peut être ajouté à votre panier</p></div>

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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
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
                        <a href="../fleurs/seeFleur.php?variete=<?=$fleur['variete']."&couleur=".$fleur['couleur']?>" class="cardFleursLink">
                            <div class="cardFleursImgShapeIndex">
                                <img alt="Fleur <?= $fleur['variete'].' '. $fleur['couleur']; ?>" src="<?= $fleur['img'];?>" class="cardFleursImg" id="cardFleursImg">
                            </div>
                            <p class="mainFleursVarieteCouleur" id="mainFleursVariete"><?= $fleur['variete']. " - ". $fleur['couleur']; ?></p>
                            <p class="mainFleursPrix" id="mainFleursPrix"><?= $fleur['prix']."€"; ?></p>
                        </a>
                    </div>

                <?php } ?>

            </div>

            <a href="../fleurs/fleurs.php" class="homeSeeMore">Voir plus ></a>

<?php }

?>
