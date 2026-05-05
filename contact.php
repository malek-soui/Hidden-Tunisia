<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$message_succes = "";
$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($nom) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $destinataire = "maleksoui05@gmail.com";
            $sujet = "Message de " . $nom;
            $contenu = "Nom: $nom\nEmail: $email\n\nMessage:\n$message\n";

            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';       // Use your SMTP server (e.g. Gmail)
                $mail->SMTPAuth   = true;
                $mail->Username   = 'maleksoui05@gmail.com'; // Replace with a real email
                $mail->Password   = 'rktd rnnu efxt jgen';    // Replace with an App Password (no spaces)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom($email, $nom); // Sender's submitted info
                $mail->addAddress($destinataire); // Add destination you want to reach
                $mail->addReplyTo($email, $nom);

                // Content
                $mail->isHTML(false);
                $mail->Subject = $sujet;
                $mail->Body    = $contenu;

                $mail->send();
                $message_succes = "Votre message a été envoyé avec succès via PHPMailer !";
            } catch (Exception $e) {
                $message_erreur = "Erreur: Le message n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $message_erreur = "Email non valide.";
        }
    } else {
        $message_erreur = "Veuillez remplir les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="an" dir=ltr">

<head>
    <title>Hidden Tunisia</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>

<body>
    <header class="top-bar" style="display: flex ; align-items: center; gap: 10px;">
        <h2 style="width: 50%;"><b><em>
                    <font face=italique><span style="color:#4B3A2F;">Hidden</span> Tunisia</font>
                </em></b></h2>
        <div class="bar" style="width: 50%;">
            <a href="#" onclick="window.location.href='index.html'">Home</a> |
            <a href="#" onclick="window.location.href='explorer.php'">Explore</a> |
            <a href="#" onclick="window.location.href='ajouter.php'">Add Place</a> |
            <a href="#" onclick="window.location.href='contact.php'">contact</a> |
            <a href="#" onclick="window.location.href='favoris.php'">Favorite</a> |
            <a href="#" onclick="window.location.href='about.html'">About</a>
            <?php if (isset($_SESSION['id_user'])): ?>
                | <a href="#" onclick="window.location.href='logout.php'">Logout</a>
            <?php else: ?>
                | <a href="#" onclick="window.location.href='login.php'">Login</a>
            <?php endif; ?>
        </div>
    </header>
    <?php if (!empty($message_succes)) echo "<p style='text-align:center; color:green;'>$message_succes</p>"; ?>
    <?php if (!empty($message_erreur)) echo "<p style='text-align:center; color:red;'>$message_erreur</p>"; ?>
    <form class="form" method="POST" action="contact.php" onsubmit="return validerContact();" novalidate>
        <h1 style="text-align: center; align-self: center;color:#4B3A2F ; margin-top: 70px;"><em>Contact Us</em></h1>

        <input class="input" type="text" placeholder="Name" name="nom" id="nom_contact">
        <input class="input" type="email" placeholder="Email" name="email" id="email_contact">
        <textarea class="input" placeholder="Message" name="message" id="message_contact"></textarea>
        <button type="submit" class="bouton btn">
            <font face=italique>Send Message</font>
        </button>
        <div>
            <form></form>
        </div>
        <footer class="fo">
            <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
        </footer>
</body>

</html>