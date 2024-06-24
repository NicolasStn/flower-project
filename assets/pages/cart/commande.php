<?php

require_once("../connexion.php");

$link = "../../";
$script = "";
$pages = "../";
$home = "../../../";

if (isset($_COOKIE['user'])) {

    if (isset($_GET['id'])) {

        $username = $_COOKIE['user'];

        $commande = $_GET['id'];

        $sqlClient =    "SELECT * FROM client
                        INNER JOIN commande ON client.id_client = commande.id_client
                        WHERE id_commande = :commande";
        $queryClient = $db->prepare($sqlClient);
        $queryClient->execute([
            "commande" => $commande
        ]);

        $client = $queryClient->fetch();

        $hours = substr($client['hours'], 0, -3);

        $sqlAdmin =   "SELECT * FROM client
                    WHERE username = :username";
        $queryAdmin = $db->prepare($sqlAdmin);
        $queryAdmin->execute([
        "username" => $username
        ]);

        $admin = $queryAdmin->fetch();

        if ($client === false) {
            header("Location: ".$_SERVER['HTTP_REFERER']);
        }

        if ($client['id_client'] !== $admin['id_client'] && $admin['admin'] !== "1") {
            header("Location: ".$_SERVER['HTTP_REFERER']);
        }

        if ($client['username'] === $username || $admin['admin'] === '1') {

              $title = "Confirmation de commande". $commande. " - Wild";
              include_once('../header.php'); ?>

                <h1 class="mainConfirmationTitle">Confirmation commande</h1>
                <div class="commandeCart">
                    <?php $phpdate = strtotime( $client['date_reservation'] );
                    $mysqldate = date( 'd/m/Y', $phpdate );

                    echo "<p>Nous vous informons que votre commande numéro <a href='../clients/commandeClient.php?commandeNumber=". $_GET['id']. "'>". $_GET['id']. "</a> a bien été prise en compte. <br>Vous recevrez d'ici peu un email récapitulatif de votre commande à l'adresse <a href='../clients/clients.php'>". $client['email']. "</a></p>
                                <p>Votre commande sera prête le <strong>". $mysqldate. "</strong> à <strong>". $hours."</strong></p>
                                <p>Merci pour votre commande, et à bientôt chez Wild!</p>
                </div>";

                include_once('../footer.php');

              }
    } else {
        header("Location: ../../../index.php");
    }
} else {
    header("Location: ../../../index.php");
}

?>
