<?php

require_once("../connexion.php");

$link = "../../";
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

        /* je renvois l'utilisateur à la page index s'il n'y a pas de parametre id dans l'url de la page */
        if (!isset($_GET['id']) || intval($_GET['id']) == 0) {
            header('Location:clients.php');
        }

        $id = $_GET['id'];

        /* requete pour récupérer les informations d'un client */

        $sql = "SELECT img, variete.libelle AS variete, couleur.libelle AS couleur, fournisseur.raison_social AS fournisseur, stock, prix FROM `fournisseur_fleur`
                INNER JOIN fleur ON fleur.id_fleur = fournisseur_fleur.id_fleur
                INNER JOIN variete ON variete.id_variete = fleur.id_variete
                INNER JOIN couleur ON couleur.id_couleur = fleur.id_couleur
                INNER JOIN fournisseur on fournisseur.id_fournisseur = fournisseur_fleur.id_fournisseur
                WHERE fournisseur_fleur.id_fleur = :id";
        $query = $db->prepare($sql);
        $query->execute([
            'id' => $id
        ]);

        $fleur = $query->fetch();

        /* je renvois l'utilisateur à la page index si la fleur n'existe pas en base */

        if ($fleur === false){
            header('Location:fleurs.php');
        }

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

        $sqlFournisseur = "SELECT * FROM fournisseur
                            ORDER BY raison_social;";
        $queryFournisseur = $db->prepare($sqlFournisseur);
        $queryFournisseur->execute();
        $fournisseurs = $queryFournisseur->fetchAll();

        // Add Fleur

        if (isset($_POST['submit']) && isset($_POST['stock']) && isset($_FILES) && isset($_POST['prix']) && isset($_POST['variete']) && isset($_POST['couleur']) && isset($_POST['fournisseur'])) {

            $stock = intval(trim($_POST['stock']));
            $stock = stripslashes($stock);
            $prix = floatval(trim($_POST['prix']));
            $prix = stripslashes($prix);
            $img = "../../img/fleurs/".$_FILES['file']['name'];
            $id_variete = intval(trim($_POST['variete']));
            $id_variete = stripslashes($id_variete);
            $id_couleur = intval(trim($_POST['couleur']));
            $id_couleur = stripslashes($id_couleur);
            $id_fournisseur = intval(trim($_POST['fournisseur']));
            $id_fournisseur = stripslashes($id_fournisseur);

            move_uploaded_file($_FILES['file']['tmp_name'], '../../img/fleurs/'.$_FILES['file']['name']);

            $sqlUpdate = "UPDATE demo_fleuriste.fournisseur_fleur
                    SET id_fournisseur = :id_fournisseur, stock = :stock
                    WHERE id_fleur = :id";
            $queryUpdate = $db->prepare($sqlUpdate);
            $queryUpdate->execute([
                "id_fournisseur" => $id_fournisseur,
                "stock" => $stock,
                "id" => $id
            ]);

            if ($_FILES['file']['tmp_name'] !== "") {
                $sqlUpdate = "UPDATE fleur
                            SET id_variete = :id_variete, id_couleur = :id_couleur, img = :img, prix = :prix
                            WHERE id_fleur = :id";
                $queryUpdate = $db->prepare($sqlUpdate);
                $queryUpdate->execute([
                    "id" => $id,
                    "id_variete" => $id_variete,
                    "id_couleur" => $id_couleur,
                    "img" => $img,
                    "prix" => $prix
                ]);

                header('Location:editFleur.php?id='.$id);
            } else {
                $sqlUpdate = "UPDATE fleur
                            SET id_variete = :id_variete, id_couleur = :id_couleur, prix = :prix
                            WHERE id_fleur = :id";
                $queryUpdate = $db->prepare($sqlUpdate);
                $queryUpdate->execute([
                    "id" => $id,
                    "id_variete" => $id_variete,
                    "id_couleur" => $id_couleur,
                    "prix" => $prix
                ]);

            header('Location:editFleur.php?id='.$id);
            }

        }

        // Add Variete

        if (isset($_POST['submitFleur']) && isset($_POST['editVariete'])) {

            $newFleur = htmlspecialchars(trim($_POST['editVariete']));
            $newFleur = stripslashes($newFleur);
            $newFleurLower = strtolower($newFleur);


            $a = 0;

            foreach ($varietes as $variete) {
                if (strtolower($variete['libelle']) === $newFleurLower) {
                    $a++;
                }
            }

            if ($a > 0 ) {
                echo "<script>alert('Variété ". $newFleur. " déjà enregistrée')</script>";
            } else {
                $sqlNewFleur = "INSERT INTO variete(libelle)
                                VALUES (:variete);";
                $queryNewFleur = $db->prepare($sqlNewFleur);

                $queryNewFleur->execute([
                "variete" => $newFleur
                ]);

                header('Location:editFleur.php?id='.$id);
            }
        }

        // Add Couleur

        if (isset($_POST['submitCouleur']) && isset($_POST['editCouleur'])) {

            $newCouleur = htmlspecialchars(trim($_POST['editCouleur']));
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

            header('Location:editFleur.php?id='.$id);

            }
        }

        // Delete Variete

        if (isset($_POST['submitDeleteVariete']) && isset($_POST['deleteVariete'])) {

            $sqlDeleteVariete = "SELECT * FROM fleur_commande
                                INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
                                INNER JOIN variete ON variete.id_variete = fleur.id_variete
                                WHERE libelle = :libelle";
            $queryDeleteVariete = $db->prepare($sqlDeleteVariete);
            $queryDeleteVariete->execute([
                "libelle" => $_POST['deleteVariete']
            ]);

            $deleteVariete = $queryDeleteVariete->fetchAll();

            if ($deleteVariete != false) {
                echo "<script>alert('Impossible de supprimer la couleur, des commandes ont déjà été réalisé avec celle ci')</script>";
            } else {

                $sqlDelete = "DELETE FROM variete WHERE libelle = :libelle";
                $queryDelete = $db->prepare($sqlDelete);
                $queryDelete->execute([
                    'libelle' => $_POST['deleteVariete']
                ]);

                header('Location:editFleur.php?id='.$id);
            }

        }

        // Delete Variete

        if (isset($_POST['submitDeleteCouleur']) && isset($_POST['deleteCouleur'])) {

            $sqlDeleteCouleur = "SELECT * FROM fleur_commande
                                INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
                                INNER JOIN couleur ON couleur.id_couleur = fleur.id_couleur
                                WHERE libelle = :libelle";
            $queryDeleteCouleur = $db->prepare($sqlDeleteCouleur);
            $queryDeleteCouleur->execute([
                "libelle" => $_POST['deleteCouleur']
            ]);

            $deleteCouleur = $queryDeleteCouleur->fetchAll();

            if ($deleteCouleur != false) {
                echo "<script>alert('Impossible de supprimer la couleur, des commandes ont déjà été réalisé avec celle ci')</script>";
            } else {

                $sqlDelete = "DELETE FROM couleur WHERE libelle = :libelle";
                $queryDelete = $db->prepare($sqlDelete);
                $queryDelete->execute([
                    'libelle' => $_POST['deleteCouleur']
                ]);

                header('Location:editFleur.php?id='.$id);
            }
        }

    $title = "Modifier la fleur ". $fleur['variete']. " ". $fleur['couleur']. " - Wild";
    include_once('../header.php'); ?>

            <div class="cardClients" id="cardClients">
                <div class="cardClientsContent">

                    <section class="cardClientsContainerLeft">
                        <h1 class="cardClientsTitle">Bienvenue <?=$clients['prenom']?></h1>
                        <button type="button" id="addFleurBtn" class="cardClientsContainerLeftBtn" onclick="window.location.href='#addFleur'">Modifier fleur ></button>
                        <button type="button" id="addVarieteBtn" class="cardClientsContainerLeftBtn" onclick="window.location.href='#addVariete'">Gérer variété ></button>
                        <button type="button" id="addCouleurBtn" class="cardClientsContainerLeftBtn" onclick="window.location.href='#addCouleur'">Gérer couleur ></button>
                        <a href="../fournisseurs/fournisseurs.php"><button type="button" id="cardClientsContainerLeftBtnDeconnexion" class="cardClientsContainerLeftBtn">Ajout fournisseur ></button></a>
                    </section>

                    <!-- Edit -->

                    <section class="addFleurContainerRight" id="addFleur">
                        <h1 class="cardClientContainerRightTitle">Modifier les informations de <?= $fleur['variete'] ." ". $fleur['couleur']?></h1>
                        <img src="<?= $fleur['img'] ?>" class="editFleurImg">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $id; ?>">

                            <div class="addFleurContainerRightContainer">
                                <label for="variete" class="cardFournisseurLabel">Variété *</label>
                                <select name="variete" placeholder="Variété" class="cardFournisseurInput">
                                <?php foreach ($varietes as $variete) {
                                            if ($variete['libelle'] === $fleur['variete']) { ?>
                                            <option selected value="<?= $variete['id_variete']?>"><?=$variete['libelle']?></option>
                                    <?php   } else { ?>
                                                <option value="<?= $variete['id_variete']?>"><?=$variete['libelle']?></option>
                                    <?php   }
                                        } ?>
                                </select>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="couleur" class="cardFournisseurLabel">Couleur *</label>
                                <select name="couleur" placeholder="Couleur" class="cardFournisseurInput">
                                <?php foreach ($couleurs as $couleur) {
                                    if ($couleur['libelle'] === $fleur['couleur']) { ?>
                                            <option selected value="<?= $couleur['id_couleur']?>"><?=$couleur['libelle']?></option>
                                    <?php   } else { ?>
                                                <option value="<?= $couleur['id_couleur']?>"><?=$couleur['libelle']?></option>
                                    <?php   }
                                    } ?>
                                </select>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="prix" class="cardFournisseurLabel">Prix *</label>
                                <input type="number" step="any" name="prix" class="cardFournisseurInput" id="prix" placeholder="Prix *" value="<?= $fleur['prix']; ?>">
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="stock" class="cardFournisseurLabel">Stock *</label>
                                <input type="number" name="stock" class="cardFournisseurInput" id="stock" placeholder="Stock *" value="<?= $fleur['stock']; ?>">
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="fournisseur" class="cardFournisseurLabel">Fournisseur *</label>
                                <select name="fournisseur" placeholder="Fournisseur" class="cardFournisseurInput">
                                <?php foreach ($fournisseurs as $fournisseur) {
                                    if ($fournisseur['raison_social'] === $fleur['fournisseur']) { ?>
                                            <option selected value="<?= $fournisseur['id_fournisseur']?>"><?=$fournisseur['raison_social']?></option>
                                    <?php   } else { ?>
                                                <option value="<?= $fournisseur['id_fournisseur']?>"><?=$fournisseur['raison_social']?></option>
                                    <?php   }
                                    } ?>
                                </select>
                            </div>

                            <div class="addFleurContainerRightContainer">
                                <label for="file" class="cardFournisseurLabel">Image *</label>
                                <input type="file" size="2000000" accept=".jpg, .jpeg, .png" name="file" class="addFleurInputImg">
                            </div>

                            <input type="submit" name="submit" class="addFleurContainerRightSaveEdit" value="Modifier">

                        </form>
                    </section>

                    <!-- Edit Variete-->

                    <section class="addFleurContainerRightEdit" id="addVariete">
                        <h1 class="cardClientContainerRightTitle">Gérer les variétés</h1>
                        <div class="addFleurContainerRightEditFlex">
                            <form method="POST" id="addFleurVarieteForm">
                                <div class="addFleurContainerRightContainerEdit">
                                    <label for="editVariete" class="cardFournisseurLabel">Ajout variété *</label>
                                    <input type="text" name="editVariete" placeholder="Fleur" class="cardFournisseurInput" required>
                                    <input type="submit" name="submitFleur" class="addFleurContainerRightSaveEdit" value="Ajouter">
                                </div>
                            </form>

                            <!-- Delete Variete -->

                            <form method="POST" id="addFleurVarieteForm">
                                <div class="addFleurContainerRightContainerEdit">
                                    <label for="deleteVariete" class="cardFournisseurLabel">Supprimer variété *</label>
                                    <select type="text" name="deleteVariete" placeholder="Variété" class="cardFournisseurInput" required>
                                        <?php foreach ($varietes as $variete) { ?>
                                            <option value="<?= $variete['libelle']?>"><?=$variete['libelle']?></option>
                                        <?php  } ?>
                                    </select>
                                    <input type="submit" name="submitDeleteVariete" class="addFleurContainerRightSaveEdit" value="Supprimer">
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
                                    <label for="editCouleur" class="cardFournisseurLabel">Ajout couleur *</label>
                                    <input type="text" name="editCouleur" placeholder="Couleur" class="cardFournisseurInput" required>
                                <input type="submit" name="submitCouleur" class="addFleurContainerRightSaveEdit" value="Ajouter">
                            </div>
                        </form>

                        <!-- Delete Couleur -->

                        <form method="POST" id="addFleurVarieteForm">
                            <div class="addFleurContainerRightContainerEdit">
                                <label for="deleteVariete" class="cardFournisseurLabel">Supprimer couleur *</label>
                                <select type="text" name="deleteCouleur" placeholder="Couleur" class="cardFournisseurInput" required>
                                    <?php foreach ($couleurs as $couleur) { ?>
                                        <option value="<?= $couleur['libelle']?>"><?=$couleur['libelle']?></option>
                                    <?php  } ?>
                                </select>
                                <input type="submit" name="submitDeleteCouleur" class="addFleurContainerRightSaveEdit" value="Supprimer">
                            </div>
                        </form>
                    </div>
                </section>
            </div>

        <main>

    </main>
    <script src="../../javascript/switchTabFleur.js"></script>

    <?php include_once('../footer.php');

    } else {
        header('Location: ../../../index.php');
    }
} else {
    header('Location: ../../../index.php');
}

?>
