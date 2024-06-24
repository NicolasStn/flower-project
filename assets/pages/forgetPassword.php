<?php

if (isset($_COOKIE['user'])) {
    header('Location: ../../index.php');
}

if ($_COOKIE == null) {
    include_once('cookiesBanner.php');
}

require_once("connexion.php");
$link = "../";
$title = "Mot de passe oublié - Wild";
$script = "";
$pages = "";
$home = "../../";

use PHPMailer\PHPMailer\PHPMailer;
require '../../vendor/autoload.php';


if (isset($_POST['submit']) && isset($_POST['username'])) {

    $username = htmlspecialchars(trim($_POST['username']));

    $sql = "SELECT `username`, `email` FROM client WHERE username = :user OR email = :user";
    $query = $db->prepare($sql);
    $query->execute([
        "user" => $username,
    ]);

    $user = $query->fetch();

    if ($user === false) {
        echo "<p class=usernameUsed>Username ou email inconnu</p>";
    } else {
        // VARIABLES

        $subject = 'Reinitialisation mot de passe';
        $message = "Vous avez demandé une réinitialisation de votre mot de passe.<br>Si vous êtes à l'origine de cette action,
        merci de cliquez sur le lien suivant.<br>Si vous n'êtes pas à l'origine de cette action, veuillez ignorer ce mail.";
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
        $mail->addAddress($user['email']); //adresse mail oú arrive les mails
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
        header('Location: confirmedPassword.php');
    }
}

include_once('header.php'); ?>

<section class="loginCard">
    <h1 class="resetPasswordTitle">Réinitialisation</h1>
    <form method="post">
        <label for="username" class="loginCardLabelMail">Username / email *</label>
        <input type="text" placeholder="Username ou Email" name="username" class="loginCardInput" required>
        <input type="submit" value="Réinitialiser" name="submit" class="loginCardSubmit">
    </form>
    <div class="loginCardCreate">
        <div class="loginCardSeparate"></div>
        <a href="login.php" class="resetCardBtn">Se connecter</a>
        <a href="createAccount.php" class="resetCardBtn">Créer un compte</a>
    </div>
</section>

<?php include_once('footer.php'); ?>
