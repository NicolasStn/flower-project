<?php

require_once('connexion.php');
$link = "../";
$title = "Commandes du jour - Wild";
$script = "";
$pages = "";
$home = "../../";

use PHPMailer\PHPMailer\PHPMailer;
require '../../vendor/autoload.php';

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

        include_once('unlinkCart.php');

        // Confirmation

        if (isset($_POST['submitAccepted']) && $_POST['id']) {

            $sqlUpdate =    "UPDATE commande
                            SET valider = 1
                            WHERE id_commande = :id";
            $queryUpdate = $db->prepare($sqlUpdate);
            $queryUpdate->execute([
                "id" => $_POST['id']
            ]);

            $sqlClient =    "SELECT date_reservation, hours, email FROM client
                            INNER JOIN commande ON client.id_client = commande.id_client
                            WHERE id_commande = :commande";
            $queryClient = $db->prepare($sqlClient);
            $queryClient->execute([
                "commande" => $_POST['id']
            ]);

            $client = $queryClient->fetch();

            $phpdate = strtotime( $client['date_reservation'] );
            $mysqldate = date( 'd/m/Y', $phpdate );
            $hours = substr($client['hours'], 0, -3);

            $subject = 'Votre commande est prete !';
            $message = "Nous vous informons que votre commande numéro <a href='PHP/17-02/assets/pages/clients/commandeClient.php?commandeNumber=". $_POST['id']. "'>". $_POST['id']. "</a> en date du <strong>". $mysqldate. "</strong> à <strong>". $hours."</strong>
            est prête.<br>Nous vous prions de bien vouloir vous rendre en magasin afin de la récuperer.</p>
                                <p>Merci et à bientôt !</p>";
            $email = 'no-reply@wild.fr';

            $mail = new PHPMailer(true);

            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;            //Enable verbose debug output
            $mail->isSMTP();                                     //Send using SMTP
            $mail->Host       = 'localhost:1025';            //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                            //Enable SMTP authentication
            // $mail->Username   = 'no-reply@wild.fr';                      //SMTP username
            // $mail->Password   = '';                       //SMTP password
            //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;   //Enable implicit TLS encryption
            // $mail->SMTPSecure = 'ssl';
            // $mail->Port       = 465;                           //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($email);
            $mail->addAddress($client['email']); //adresse mail oú arrive les mails
            // $mail->addAddress('exemple@exemple.fr'); //adresse mail oú arrive les mails
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com'); // ajouter copie caché
            // $mail->addBCC('bcc@example.com');

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

            header('Location: dayCommande.php');

        }

        if (isset($_POST['submitDeleted']) && $_POST['id']) {

            $sqlUpdate =    "UPDATE fournisseur_fleur
                            SET stock = stock + (SELECT quantity FROM fleur_commande WHERE num_commande = :id AND fleur_commande.id_fleur = fournisseur_fleur.id_fleur)
                            WHERE EXISTS (SELECT * FROM fleur_commande WHERE num_commande = :id AND fleur_commande.id_fleur = fournisseur_fleur.id_fleur)";
            $queryUpdate = $db->prepare($sqlUpdate);
            $queryUpdate->execute([
                "id" => $_POST['id']
            ]);

            $sqlDeleteLignes = "DELETE FROM demo_fleuriste.fleur_commande WHERE num_commande = :num";
            $queryDeleteLignes = $db->prepare($sqlDeleteLignes);
            $queryDeleteLignes->execute([
                "num" => $_POST['id']
            ]);

            $sqlDeleteCommande = "DELETE FROM demo_fleuriste.commande WHERE id_commande = :num";
            $queryDeleteCommande = $db->prepare($sqlDeleteCommande);
            $queryDeleteCommande->execute([
                "num" => $_POST['id']
            ]);

            header('Location: dayCommande.php');

        }

        $date = date('Y-m-d');

        $sqlCommande = "SELECT id_commande, valider, hours, gender, prenom, nom FROM commande
                        INNER JOIN client ON commande.id_client = client.id_client
                        WHERE date_reservation = :dateCommande
                        ORDER BY commande.id_client, hours, date_reservation";

        $queryCommande = $db->prepare($sqlCommande);
        $queryCommande->execute([
            "dateCommande" => $date
        ]);

        $commandes = $queryCommande->fetchAll();

        include_once('header.php'); ?>

            <section class="dayCommandeContainer">
                <h1 class="searchResultsTitle">Commande du jour : <?= date('d/m/Y') ?></h1>

            <?php if ($commandes !== false) {

                $count = 0;

                foreach ($commandes as $commande) {

                    if ($commande['valider'] !== "1") {

                        $count++;

                        $num_commande = $commande['id_commande'];

                        $sqlArticles = "SELECT num_commande, img, variete.libelle AS variete, couleur.libelle AS couleur, quantity, prix FROM fleur_commande
                                        INNER JOIN fleur ON fleur_commande.id_fleur = fleur.id_fleur
                                        INNER JOIN variete ON fleur.id_variete = variete.id_variete
                                        INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                                        WHERE num_commande = :num_commande";

                        $queryArticles = $db->prepare($sqlArticles);
                        $queryArticles->execute([
                            "num_commande" => $num_commande
                        ]);

                        $articles = $queryArticles->fetchAll();

                        $total = 0;

                        $hours = substr($commande['hours'], 0, -3); ?>

                        <section class="mainDayCommandeContainer">

                            <p class="dayCommandeCardContent">Commande numéro <a href="clients/commandeClient.php?commandeNumber=<?=$commande['id_commande']?>"><?=$commande['id_commande']?></a> de <?=$commande['gender']. " ".$commande['prenom']. " ". $commande['nom']?> </p>
                            <p class="dayCommandeCardContent">Commande du <strong><?=date("d/m/Y"). "</strong> pour <strong>". $hours?></strong></p>

                            <div class="dayCommandeCardContainer">

                                <?php foreach ($articles as $article) { ?>

                                    <div class="dayCommandeCardContainerArticles">

                                        <?php $strimg = substr($article['img'], 3); ?>

                                        <a href="fleurs/seeFleur.php?variete=<?=$article['variete']?>&couleur=<?=$article['couleur']?>" class="cartFleurLink">
                                            <div class="dayCommandeCardPosition">
                                                <img src="<?= $strimg ?>" class="dayCommandeCardImg">
                                                <p class="dayCommandeCardFleur"><?=$article['variete']. " ". $article['couleur']?></p>
                                            </div>
                                        </a>
                                        <div class="dayCommandeCardPosition">
                                            <p class="dayCommandeCardPrice"><?=$article['prix']?>€</p>
                                            <p class="dayCommandeCardContent">x <?=$article['quantity']?></p>
                                        </div>
                                    </div>

                                <?php $total += ($article['quantity'] * $article['prix']);
                                } ?>

                                <div class="dayCommandeCardContainerArticles">
                                    <p class="dayCommandeCardContent"></p>
                                    <p class="dayCommandeCardTotal">Total : <?=$total?>€</p>
                                </div>

                                <div class="dayBtnContainer">
                                    <form method="post" class="dayCommandeCardForm">
                                            <input type="hidden" name="id" value="<?=$num_commande?>">
                                            <input type="submit" name="submitAccepted" value="Confirmer" class="dayCommandeBtnAccept">
                                    </form>

                                    <form method="post" class="dayCommandeCardForm">
                                        <input type="hidden" name="id" value="<?=$num_commande?>">
                                        <input type="submit" name="submitDeleted" value="Annuler" class="dayCommandeBtnDelete">
                                    </form>
                                </div>
                        </div>

                    </section>
                    <?php }
                }

                if ($count <= 0) {
                    echo "<div class='searchCommand'><p>Plus aucune commande n'est a préparer, tout a été livré !</p></div>";
                }
            } else {
                echo "<p>Aucune commande aujourd'hui</p>";
            } ?>
        </section>
    <?php } else {
        header("Location: ".$_SERVER['HTTP_REFERER']);
    }
} else {
    header("Location: ".$_SERVER['HTTP_REFERER']);
}

include_once('footer.php'); ?>

<script src="../javascript/scroll.js"></script>
