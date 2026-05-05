<?php
require 'connexion_db.php';
session_start();

$message_succes = "";
$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_complet = $_POST['nom_complet'] ?? '';
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (!empty($nom_complet) && !empty($email) && !empty($mot_de_passe)) {
        try {
            // Vérifier si l'email existe déjà
            $stmt_check = $db->prepare("SELECT id_user FROM utilisateurs WHERE email = :email");
            $stmt_check->execute([':email' => $email]);
            
            if ($stmt_check->rowCount() > 0) {
                $message_erreur = "Cet email est déjà utilisé.";
            } else {
                // Hachage du mot de passe pour la sécurité
                $mdp_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                
                // Insertion dans la base de données
                $stmt = $db->prepare("INSERT INTO utilisateurs (nom_complet, email, mot_de_passe) VALUES (:nom, :email, :mdp)");
                $stmt->execute([
                    ':nom' => $nom_complet,
                    ':email' => $email,
                    ':mdp' => $mdp_hash
                ]);
                $message_succes = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            }
        } catch (Exception $e) {
            $message_erreur = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    } else {
        $message_erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html> 
<html lang="an" dir="ltr">
<head> 
    <title>Hidden Tunisia - Inscription</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body> 
    <header class="top-bar" style="display: flex ; align-items: center; gap: 10px;" >
        <h2 style="width: 50%;"><b><em><font face=italique><span style="color:#4B3A2F;">Hidden</span> Tunisia</font></em></b></h2>
    <div class="bar" style="width: 50%;">
            <a href="index.html">Home</a> |
            <a href="explorer.php">Explore</a> |
            <a href="ajouter.php">Add Place</a> |
            <a href="contact.php">Contact</a> |
            <a href="favoris.php">Favorite</a> |
            <a href="about.html">About</a> 
            | <a href="login.php">Login</a>
    </div>
    </header>

    <?php if(!empty($message_succes)) echo "<p style='text-align:center; color:green; margin-top:20px; font-weight:bold;'>$message_succes <br> <a href='login.php'>Cliquez ici pour vous connecter</a></p>"; ?>
    <?php if(!empty($message_erreur)) echo "<p style='text-align:center; color:red; margin-top:20px; font-weight:bold;'>$message_erreur</p>"; ?>

    <?php if(empty($message_succes)): ?>
    <form class="form" method="POST" action="register.php" onsubmit="return validerAuth();">
        <h1 style="text-align: center; align-self: center;color:#4B3A2F ; margin-top: 70px;"><em>Register</em></h1>

        <input class="input" type="text" id="nom_auth" name="nom_complet" placeholder="Full Name">
        <input class="input" type="email" id="email_auth" name="email" placeholder="Email">
        <input class="input" type="password" id="mdp_auth" name="mot_de_passe" placeholder="Password">
        <button type="submit" class="bouton btn"><font face=italique>Register</font></button>
        <p style="text-align: center; margin-top: 10px;">Already have an account? <a href="login.php">Login here</a></p>
    </form>
    <?php endif; ?>
    
    <footer class="fo">
        <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
    </footer>
</body>
</html>
