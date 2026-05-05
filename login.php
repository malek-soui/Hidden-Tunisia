<?php
require 'connexion_db.php';
session_start();

$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (!empty($email) && !empty($mot_de_passe)) {
        try {
            $stmt = $db->prepare("SELECT id_user, nom_complet, mot_de_passe, role FROM utilisateurs WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                // Création de la session
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nom_complet'] = $user['nom_complet'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: explorer.php");
                exit();
            } else {
                $message_erreur = "Email ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            $message_erreur = "Erreur de connexion : " . $e->getMessage();
        }
    } else {
        $message_erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html> 
<html lang="an" dir="ltr">
<head> 
    <title>Hidden Tunisia - Connexion</title>
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

    <?php if(!empty($message_erreur)) echo "<p style='text-align:center; color:red; margin-top:20px; font-weight:bold;'>$message_erreur</p>"; ?>

    <form class="form" method="POST" action="login.php" onsubmit="return validerAuth();">
        <h1 style="text-align: center; align-self: center;color:#4B3A2F ; margin-top: 70px;"><em>Login</em></h1>

        <input class="input" type="email" id="email_auth" name="email" placeholder="Email">
        <input class="input" type="password" id="mdp_auth" name="mot_de_passe" placeholder="Password">
        <button type="submit" class="bouton btn"><font face=italique>Login</font></button>
        <p style="text-align: center; margin-top: 10px;">Don't have an account? <a href="register.php">Register here</a></p>
    </form>
    
    <footer class="fo">
        <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
    </footer>
</body>
</html>
