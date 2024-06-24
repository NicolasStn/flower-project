<?php

require_once('../connexion.php');

$link = "../../";
$title = "Ajouter fleur - Wild";
$script = "";
$pages = "../";
$home = "../../../";

if ($_COOKIE != null) {

    $username = $_COOKIE['user'];

    $sqlClients = "SELECT username, admin FROM client
                    WHERE username = :username";
    $queryClients = $db->prepare($sqlClients);
    $queryClients->execute([
        "username" => $username
    ]);

    $clients = $queryClients->fetch();

    if ($clients['admin'] === '1') {

        $sql = "SELECT * FROM fournisseur";
        $query = $db->prepare($sql);
        $query->execute();
        $fournisseurs = $query->fetchAll();

        $sqlVariete = "SELECT * FROM variete
                        ORDER BY libelle;";
        $queryVariete = $db->prepare($sqlVariete);
        $queryVariete->execute();
        $varietes = $queryVariete->fetchAll();

        $sqlCouleur = "SELECT * FROM couleur
                        ORDER BY libelle;";
        $queryCouleur = $db->prepare($sqlCouleur);
        $queryCouleur->execute();
        $couleurs = $queryCouleur->fetchAll();

        if (isset($_POST['submit'])) {

            if (isset($_POST['variete']) && $_POST['variete'] != '' && isset($_POST['couleur']) && $_POST['couleur'] != '' && isset($_POST['prix']) && $_POST['prix'] != ''
            && isset($_POST['stock']) && $_POST['stock'] != '' && isset($_POST['fournisseur']) && $_POST['fournisseur'] != '') {

                $variete = htmlspecialchars(trim($_POST['variete']));
                $variete = stripslashes($variete);
                $couleur = htmlspecialchars(trim($_POST['couleur']));
                $couleur = stripslashes($couleur);
                $prix = floatval(trim($_POST['prix']));
                $prix = stripslashes($prix);
                $stock = intval(trim($_POST['stock']));
                $stock = stripslashes($stock);
                $fournisseur = htmlspecialchars(trim($_POST['fournisseur']));
                $fournisseur = stripslashes($fournisseur);
                $img = "../../img/fleurs/".$_FILES['file']['name'];

                $sqlCheckExist = "SELECT * FROM fleur
                                WHERE id_couleur = :couleur AND id_variete = :variete";
                $queryCheckExist = $db->prepare($sqlCheckExist);
                $queryCheckExist->execute([
                    "variete" => $variete,
                    "couleur" => $couleur,
                ]);

                $checkExist = $queryCheckExist->fetch();

                if ($checkExist == false) {

                    move_uploaded_file($_FILES['file']['tmp_name'], '../../img/fleurs/'.$_FILES['file']['name']);

                    $sqlPrix = "INSERT INTO fleur(id_variete, id_couleur, prix, img)
                                VALUES (:variete, :couleur, :prix, :img);";
                    $query = $db->prepare($sqlPrix);

                    $query->execute([
                        "variete" => $variete,
                        "couleur" => $couleur,
                        "prix" => $prix,
                        "img" => $img
                    ]);
                    $id_fleur = $db->lastInsertId();

                    $sqlStock = "INSERT INTO fournisseur_fleur(id_fournisseur, id_fleur, stock)
                                VALUES (:fournisseur, :fleur, :stock);";
                    $query = $db->prepare($sqlStock);

                    $query->execute([
                        "fournisseur" => $fournisseur,
                        "fleur" => $id_fleur,
                        "stock" => $stock,
                    ]);

                    header("Location:fleurs.php");
                } else {
                    echo "<script>alert(`Fleur déjà enregistrée. N'hésitez pas à aller l'éditer !`)</script>";
                }

            } else {
                header('Location: addFleur.php');
                echo "<script>alert('Merci de remplir correctement le formulaire');</script>";
            }
        }

        // Add Variete

        if (isset($_POST['submitFleur']) && isset($_POST['newFleur'])) {

            $newFleur = htmlspecialchars(trim($_POST['newFleur']));
            $newFleur = stripslashes($newFleur);
            $newFleurLower = strtolower($newFleur);

            $a = 0;

            foreach ($varietes as $variete) {
                if (strtolower($variete['libelle']) === $newFleurLower) {
                    $a++;
                }
            }

            if ($a > 0 ) {
                echo "<script>alert('Variété ". $newFleur. " déjà enregistrée');</script>";
            } else {

                $sqlNewFleur = "INSERT INTO variete(libelle)
                                VALUES (:variete);";
                $queryNewFleur = $db->prepare($sqlNewFleur);

                $queryNewFleur->execute([
                "variete" => $newFleur
                ]);

                header('Location:addFleur.php');

            }
        }

        // Add Couleur

        if (isset($_POST['submitCouleur']) && isset($_POST['newCouleur'])) {

            $newCouleur = htmlspecialchars(trim($_POST['newCouleur']));
            $newCouleur = stripslashes($newCouleur);
            $newCouleurLower = strtolower($newCouleur);

            $a = 0;

            foreach ($couleurs as $couleur) {
                if (strtolower($couleur['libelle']) === $newCouleurLower) {
                    $a++;
                }
            }

            if ($a > 0 ) {
                echo "<script>alert('Couleur ". $newCouleur. " déjà enregistrée')</script>";
            } else {

                $sqlNewCouleur = "INSERT INTO couleur(libelle)
                                    VALUES (:variete);";
                $queryNewCouleur = $db->prepare($sqlNewCouleur);

                $queryNewCouleur->execute([
                "variete" => $newCouleur
                ]);

                header("Location:addFleur.php");

            }
        }

        // Delete Variete

        if (isset($_POST['submitdeleteVariete']) && isset($_POST['deleteVariete'])) {

            $deleteVariete = htmlspecialchars(trim($_POST['deleteVariete']));

            $sqlDeleteVariete = "SELECT * FROM fleur_commande
                                INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
                                INNER JOIN variete ON variete.id_variete = fleur.id_variete
                                WHERE variete.id_variete = :id";
            $queryDeleteVariete = $db->prepare($sqlDeleteVariete);
            $queryDeleteVariete->execute([
                "id" => $deleteVariete
            ]);

            $checkDeleteVariete = $queryDeleteVariete->fetchAll();

            if ($checkDeleteVariete != false) {
                echo "<script>alert('Impossible de supprimer la variété, des commandes ont déjà été réalisé avec celle ci')</script>";
            } else {

                $sqlDeleteVariete = "DELETE FROM variete WHERE id_variete = :id";

                $queryDeleteVariete = $db->prepare($sqlDeleteVariete);
                $queryDeleteVariete->execute([
                    "id" => $deleteVariete
                ]);

                header('Location:addFleur.php');
            }
        }

        // Delete Couleur

        if (isset($_POST['submitdeleteCouleur']) && isset($_POST['deleteCouleur'])) {

            $deleteCouleur = htmlspecialchars(trim($_POST['deleteCouleur']));

            $sqlDeleteCouleur = "SELECT * FROM fleur_commande
                                INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
                                INNER JOIN couleur ON couleur.id_couleur = fleur.id_couleur
                                WHERE couleur.id_couleur = :id";
            $queryDeleteCouleur = $db->prepare($sqlDeleteCouleur);
            $queryDeleteCouleur->execute([
                "id" => $deleteCouleur
            ]);

            $checkDeleteCouleur = $queryDeleteCouleur->fetchAll();

            if ($checkDeleteCouleur != false) {
                echo "<script>alert('Impossible de supprimer la couleur, des commandes ont déjà été réalisé avec celle ci')</script>";
            } else {

                $sqlDeleteCouleur = "DELETE FROM couleur WHERE id_couleur = :id";

                $queryDeleteCouleur = $db->prepare($sqlDeleteCouleur);
                $queryDeleteCouleur->execute([
                    "id" => $deleteCouleur
                ]);

                header('Location:addFleur.php');
            }
        }

        include_once('../header.php');  ?>

            <main class="mainClients">

            <div class="cardClients" id="cardClients">
                <div class="cardClientsContent">

                    <section class="cardClientsContainerLeft">
                        <h1 class="cardClientsTitle">Bienvenue <?=$clients['prenom']?></h1>
                        <button type="button" id="addFleurBtn" class="cardClientsContainerLeftBtn" onclick="window.location.href='#addFleur'">Ajout fleur ></button>
                        <button type="button" id="addVarieteBtn" class="cardClientsContainerLeftBtn" onclick="window.location.href='#addVariete'">Gérer variété ></button>
                        <button type="button" id="addCouleurBtn" class="cardClientsContainerLeftBtn" onclick="window.location.href='#addCouleur'">Gérer couleur ></button>
                        <a href="../fournisseurs/fournisseurs.php"><button type="button" id="cardClientsContainerLeftBtnDeconnexion" class="cardClientsContainerLeftBtn">Ajout fournisseur ></button></a>
                    </section>

                    <!-- Add -->

                    <section class="addFleurContainerRight" id="addFleur">
                        <h1 class="cardClientContainerRightTitle">Ajout fleur</h1>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="addFleurContainerRightContainer">
                                <label for="variete" class="cardFournisseurLabel">Variété *</label>
                                <select type="text" name="variete" placeholder="Variété" class="cardFournisseurInput" required>
                                    <?php foreach ($varietes as $variete) { ?>
                                        <option value="<?= $variete['id_variete']?>"><?=$variete['libelle']?></option>
                                    <?php  } ?>
                                </select>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="couleur" class="cardFournisseurLabel">Couleur *</label>
                                <select name="couleur" placeholder="Couleur" class="cardFournisseurInput" required>
                                    <?php foreach ($couleurs as $couleur) { ?>
                                        <option value="<?= $couleur['id_couleur']?>"><?=$couleur['libelle']?></option>
                                    <?php  } ?>
                                </select>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="prix" class="cardFournisseurLabel">Prix *</label>
                                <input type="number" step="any" name="prix" placeholder="Prix" class="cardFournisseurInput" required>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="stock" class="cardFournisseurLabel">Stock *</label>
                                <input type="number" name="stock" placeholder="Stock" class="cardFournisseurInput" required>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="fournisseur" class="cardFournisseurLabel">Fournisseur *</label>
                                <select name="fournisseur" id="fournisseur" class="cardFournisseurInput" required>
                                    <?php foreach ($fournisseurs as $fournisseur) { ?>
                                        <option value="<?= $fournisseur['id_fournisseur']?>"><?=$fournisseur['raison_social']?></option>
                                    <?php  } ?>
                                </select>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="file" class="cardFournisseurLabel">Image *</label>
                                <input type="file" size="2000000" accept=".jpg, .jpeg, .png" name="file" class="addFleurInputImg" required>
                            </div>

                            <input type="submit" name="submit" class="addFleurContainerRightSaveEdit" value="Ajouter">
                        </form>
                    </section>

                    <!-- Edit Variete-->

                    <section class="addFleurContainerRightEdit" id="addVariete">
                        <h1 class="cardClientContainerRightTitle">Gérer les variétés</h1>
                        <div class="addFleurContainerRightEditFlex">
                            <form method="POST" id="addFleurVarieteForm">
                                <div class="addFleurContainerRightContainerEdit">
                                    <label for="newFleur" class="cardFournisseurLabel">Ajout variété *</label>
                                    <input type="text" name="newFleur" placeholder="Fleur" class="cardFournisseurInput" required>
                                    <input type="submit" name="submitFleur" class="addFleurContainerRightSaveEdit" value="Ajouter">
                                </div>
                            </form>

                            <!-- Delete Variete -->

                            <form method="POST" id="addFleurVarieteForm">
                                <div class="addFleurContainerRightContainerEdit">
                                    <label for="deleteVariete" class="cardFournisseurLabel">Supprimer variété *</label>
                                    <select type="text" name="deleteVariete" placeholder="Variété" class="cardFournisseurInput" required>
                                        <?php foreach ($varietes as $variete) { ?>
                                            <option value="<?= $variete['id_variete']?>"><?=$variete['libelle']?></option>
                                        <?php  } ?>
                                    </select>
                                    <input type="submit" name="submitdeleteVariete" class="addFleurContainerRightSaveEdit" value="Supprimer">
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Edit Couleur -->

                    <section class="addFleurContainerRightEdit" id="addCouleur">
                        <h1 class="cardClientContainerRightTitle">Gérer les couleurs</h1>
                        <div class="addFleurContainerRightEditFlex">
                            <form method="POST" id="addFleurCouleurForm">
                                <div class="addFleurContainerRightContainerEdit">
                                    <label for="newFleur" class="cardFournisseurLabel">Ajout couleur *</label>
                                    <input type="text" name="newCouleur" placeholder="Couleur" class="cardFournisseurInput" required>
                                <input type="submit" name="submitCouleur" class="addFleurContainerRightSaveEdit" value="Ajouter">
                            </div>
                        </form>

                        <!-- Delete Couleur -->

                        <form method="POST" id="addFleurVarieteForm">
                            <div class="addFleurContainerRightContainerEdit">
                                <label for="deleteVariete" class="cardFournisseurLabel">Supprimer couleur *</label>
                                <select type="text" name="deleteCouleur" placeholder="Couleur" class="cardFournisseurInput" required>
                                    <?php foreach ($couleurs as $couleur) { ?>
                                        <option value="<?= $couleur['id_couleur']?>"><?=$couleur['libelle']?></option>
                                    <?php  } ?>
                                </select>
                                <input type="submit" name="submitdeleteCouleur" class="addFleurContainerRightSaveEdit" value="Supprimer">
                            </div>
                        </form>
                    </div>
                </section>
            </div>

        </main>
        <script src="../../javascript/switchTabFleur.js"></script>
        <?php include_once('../footer.php'); ?>

    <?php

    } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: ../../../index.php');
}

?>
