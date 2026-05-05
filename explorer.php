<?php
require 'connexion_db.php';
session_start();

$region_filter = isset($_GET['region']) ? $_GET['region'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

$query = "SELECT * FROM lieux WHERE statut = 1";
$params = [];

if ($region_filter && $region_filter !== 'Destination Area') {
    $query .= " AND region = :region";
    $params[':region'] = $region_filter;
}
if ($type_filter && $type_filter !== 'Destination Type') {
    $query .= " AND type_destination = :type";
    $params[':type'] = $type_filter;
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html> 
<html lang="an" dir=ltr">
<head> 
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hidden Tunisia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #FAF8F3;"> 
    <header class="top-bar" style="display: flex ; align-items: center; gap: 10px;" >
        <h2 style="width: 50%;"><b><em><font face=italique><span style="color:#4B3A2F;">Hidden</span> Tunisia</font></em></b></h2>
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
    <div style="min-height: 100vh; height: auto; padding-bottom: 50px;" class="box0">
        <div class="box1" style="height: 25vh;">
            <h1><em>Explore Places</em></h1>
            <form method="GET" action="explorer.php" style="display: inline-block;">
                <select name="region" class="select" onchange="this.form.submit()">
                    <option <?php if($region_filter == 'Destination Area') echo 'selected'; ?>>Destination Area</option>
                    <option <?php if($region_filter == 'South') echo 'selected'; ?>>South</option>
                    <option <?php if($region_filter == 'Sahel') echo 'selected'; ?>>Sahel</option>
                    <option <?php if($region_filter == 'Cap Bon') echo 'selected'; ?>>Cap Bon</option>
                    <option <?php if($region_filter == 'North') echo 'selected'; ?>>North</option>
                    <option <?php if($region_filter == 'Tunis') echo 'selected'; ?>>Tunis</option>
                </select>
                <select name="type" class="select" onchange="this.form.submit()">
                    <option <?php if($type_filter == 'Destination Type') echo 'selected'; ?>>Destination Type</option>
                    <option <?php if($type_filter == 'Viewpoint') echo 'selected'; ?>>Viewpoint</option>
                    <option <?php if($type_filter == 'Nature Spot') echo 'selected'; ?>>Nature Spot</option>
                    <option <?php if($type_filter == 'Historical Site') echo 'selected'; ?>>Historical Site</option>
                    <option <?php if($type_filter == 'Café') echo 'selected'; ?>>Café</option>
                    <option <?php if($type_filter == 'Village') echo 'selected'; ?>>Village</option>
                </select>
                <noscript><button type="submit" class="bouton bou" style="padding: 8px 20px; font-size: 16px; margin-left: 10px;">Filter</button></noscript>
            </form>
        </div>
        <hr id="ligne">
        <div style="min-height: 70vh; display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; padding: 20px;">
            <?php 
            if (count($lieux) == 0) {
                echo "<p style='text-align:center; font-size: 20px; width: 100%;'>La base de donn&eacute;es est vide. Ajoutez des lieux depuis la page Add Place !</p>";
            } else {
                foreach ($lieux as $lieu) {
                    $id = $lieu['id_lieu'];
                    $nom = $lieu['nom'];
                    $image = $lieu['image_url'];
                ?>
                <div class="card" style="height: 50vh; width: 45vh; margin-bottom: 20px;">
                    <div style="height: 30vh; width: 100%; background-image: url('<?php echo htmlspecialchars($image); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                    <div class="b" style="height: 20vh; width: 100%;">
                        <h3 style="margin-bottom: 5px;"><em><?php echo htmlspecialchars($nom); ?></em></h3>
                        <button onclick="window.location.href='details.php?id=<?php echo $id; ?>'" class="bouton bou"><font face="italique">Explore</font></button>
                    </div>
                </div>
                <?php } 
            } ?>
        </div>
    </div>
        <footer class="fo">
        <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
    </footer>
</body>
</html>