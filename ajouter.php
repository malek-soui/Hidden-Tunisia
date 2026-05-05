<?php
require 'connexion_db.php';
session_start();

$message_succes = "";
$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $region = $_POST['region'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $image_url = 'images/default.jpg';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'images/';
        $file_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . time() . '_' . $file_name;
        
        // Ensure images directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $target_file;
        } else {
            $message_erreur = "Failed to upload the image.";
        }
    }

    if (!empty($nom) && !empty($description) && empty($message_erreur)) {
        try {
            $stmt = $db->prepare("INSERT INTO lieux (nom, description, image_url, region, type_destination) VALUES (:nom, :description, :image_url, :region, :type_destination)");
            $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':image_url' => $image_url,
                ':region' => $region,
                ':type_destination' => $categorie
            ]);
            $message_succes = "Le lieu a été ajouté avec succès !";
        } catch (Exception $e) {
            $message_erreur = "Erreur : " . $e->getMessage();
        }
    } else if (empty($message_erreur)) {
        $message_erreur = "Veuillez remplir les champs.";
    }
}
?>
<!DOCTYPE html> 
<html lang="en" dir="ltr">
<head> 
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hidden Tunisia</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body style="background-color: #FAF8F3;"> 
    <header class="top-bar" style="display: flex ; align-items: center; gap: 10px;" >
        <h2 style="width: 50%;"><b><em><font face="italique"><span style="color:#4B3A2F;">Hidden</span> Tunisia</font></em></b></h2>
    <div class="bar" style="width: 50%;">
            <a href="#" onclick="window.location.href='index.html'">Home</a> |
            <a href="#" onclick="window.location.href='explorer.php'">Explore</a> |
            <a href="#" onclick="window.location.href='ajouter.php'">Add Place</a> |
            <a href="#" onclick="window.location.href='contact.php'">contact</a> |
            <a href="#" onclick="window.location.href='favoris.php'">Favorite</a> |
            <a href="#" onclick="window.location.href='about.html'">About</a> 
            <?php if(isset($_SESSION['id_user'])): ?>
            | <a href="#" onclick="window.location.href='logout.php'">Logout</a>
            <?php else: ?>
            | <a href="#" onclick="window.location.href='login.php'">Login</a>
            <?php endif; ?> 
    </div>
    </header>

    <?php if(!empty($message_succes)) echo "<p style='text-align:center; color:green;'>$message_succes</p>"; ?>
    <?php if(!empty($message_erreur)) echo "<p style='text-align:center; color:red;'>$message_erreur</p>"; ?>

    <form class="form" method="POST" action="ajouter.php" enctype="multipart/form-data" onsubmit="return validerAjoutLieu();">
        <h1 style="text-align: center; align-self: center;color:#4B3A2F ; margin-top: 70px;"><em>Add New Place</em></h1>

        <input class="input" type="text" placeholder="Name" name="nom" id="nom_lieu">

        <textarea class="input" placeholder="Description" name="description" id="description_lieu"></textarea>

        <select class="select s2" name="region" id="region_lieu">
            <option>Destination Area</option>
            <option value="South">South</option>
            <option value="Sahel">Sahel</option>
            <option value="Cap Bon">Cap Bon</option>
            <option value="North">North</option>
            <option value="Tunis">Tunis</option>
        </select>

        <select class="select s2" name="categorie" id="categorie_lieu">
            <option>Destination Type</option>
            <option value="Viewpoint">Viewpoint</option>
            <option value="Nature Spot">Nature Spot</option>
            <option value="Historical Site">Historical Site</option>
            <option value="Café">Café</option>
            <option value="Village">Village</option>
        </select>
        
        <input class="boutonUP" type="file" name="image" accept="image/*" style="width: 250px;">
        <input style="align-self: center;" class="bouton" type="submit" value="submit">       
    </form>

    <footer class="fo" style="margin-top: 50px;">
        <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
    </footer>
</body>
</html>
