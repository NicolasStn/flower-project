<?php

require_once("../connexion.php");

use PHPMailer\PHPMailer\PHPMailer;
require '../../../vendor/autoload.php';

if (isset($_COOKIE['user'])) {

    if (isset($_POST['id']) && isset($_POST['date']) && isset($_POST['hours']) && isset($_POST['submit'])) {

        $username = $_POST['id'];
        $date = date('Y-m-j H:i:s');

        if (is_file('user/'.$username.'-cart.json')) {

            $number_commande = "BC-". uniqid();
            $lists_cart = json_decode(file_get_contents('user/'.$username.'-cart.json'), true);

            $sqlClient =    "SELECT * FROM client
                            WHERE username = :username";
            $queryClient = $db->prepare($sqlClient);
            $queryClient->execute([
                "username" => $username
            ]);

            $client = $queryClient->fetch();

            $sqlLigneCommand =   "INSERT INTO commande(id_commande, id_client, date_commande, date_reservation, `hours`)
                                VALUES (:id_commande, :id_client, :date_commande, :date_reservation, :hours)";

            $queryLigneCommand = $db->prepare($sqlLigneCommand);
            $queryLigneCommand->execute([
                "id_commande" => $number_commande,
                "id_client" => $client['id_client'],
                "date_commande" => $date,
                "date_reservation" => $_POST['date'],
                "hours" => $_POST['hours']
            ]);

            foreach ($lists_cart as $cart) {

                $sqlFlower =    "SELECT * FROM fleur
                                INNER JOIN couleur ON fleur.id_couleur = couleur.id_couleur
                                INNER JOIN variete ON fleur.id_variete = variete.id_variete
                                WHERE couleur.libelle = :couleur AND variete.libelle = :variete";

                $queryFlower = $db->prepare($sqlFlower);
                $queryFlower->execute([
                    "couleur" => $cart['couleur'],
                    "variete" => $cart['variete'],
                ]);

                $listFlower = $queryFlower->fetch();

                $flower = $listFlower['id_fleur'];

                $sqlCommand =   "INSERT INTO fleur_commande(num_commande, id_fleur, quantity)
                                VALUES (:num_commande, :id_fleur, :quantity)";

                $queryCommand = $db->prepare($sqlCommand);
                $queryCommand->execute([
                    "num_commande" => $number_commande,
                    "id_fleur" => $flower,
                    "quantity" => $cart['quantity'],
                ]);

                $sqlOldStock =  "SELECT stock FROM fournisseur_fleur
                                WHERE id_fleur = :id_fleur";

                $queryOldStock = $db->prepare($sqlOldStock);
                $queryOldStock->execute([
                    "id_fleur" => $flower,
                ]);

                $oldStock = $queryOldStock->fetch();

                $newStock = $oldStock['stock'] - $cart['quantity'];

                $sqlStock =     "UPDATE fournisseur_fleur
                                SET stock = :stock
                                WHERE id_fleur = :id_fleur";

                $queryStock = $db->prepare($sqlStock);
                $queryStock->execute([
                    "id_fleur" => $flower,
                    "stock" => $newStock,
                ]);
            }

            $file = 'user/'.$username.'-cart.json';
            unlink($file);

            $phpdate = strtotime( $_POST['date'] );
            $mysqldate = date( 'd/m/Y', $phpdate );
            $hours = substr($_POST['hours'], 0, -3);

            $subject = 'Confirmation de commande';
            $message = "Votre commande numéro <a href='PHP/17-02/assets/pages/clients/commandeClient.php?commandeNumber=". $number_commande. "'>". $number_commande. "</a> a bien été prise en compte.<br>
            Votre commande sera prête le <strong>". $mysqldate. "</strong> à <strong>". $hours."</strong></p>
                                <p>Merci pour votre commande, et à bientôt !</p>";
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

            header('Location: commande.php?id='. $number_commande);

        } else {
            header("Location: ".$_SERVER['HTTP_REFERER']);
        }
    } else {
        header("Location: ".$_SERVER['HTTP_REFERER']);
    }
} else if (isset($_COOKIE['visitor'])) {
    header('Location: ../login.php');
} else {
    header("Location: ".$_SERVER['HTTP_REFERER']);
}

?>
