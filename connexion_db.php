<?php
try {
    // Connexion à la base de données avec PDO comme indiqué dans le cours (Partie 6)
    $db = new PDO('mysql:host=localhost;dbname=hiddentunisia;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(Exception $e) {
    // En cas d'erreur, on arrête tout et on affiche le message
    die('Erreur : '.$e->getMessage());
}
?>
