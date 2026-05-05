<?php
require 'connexion_db.php';
session_start();

// On s'assure d'avoir l'ID de l'utilisateur
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

                    // Check for removal action
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_lieu'])) {
                        $remStmt = $db->prepare("DELETE FROM favoris WHERE id_user = :uid AND id_lieu = :lid");
                        $remStmt->execute([':uid' => $_SESSION['id_user'], ':lid' => $_POST['remove_lieu']]);
                        header("Location: favoris.php");
                        exit();
                    }

                    $stmt = $db->prepare("SELECT l.* FROM lieux l INNER JOIN favoris f ON l.id_lieu = f.id_lieu WHERE f.id_user = :uid");
                    $stmt->execute([':uid' => $_SESSION['id_user']]);
                    $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
<!DOCTYPE html>
<html lang="an" dir=ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Hidden Tunisia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-bar" style="display: flex ; align-items: center; gap: 10px;">
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
    <h1 style="font-size: 45px; text-align: center; align-self: center;color:#4B3A2F ; margin-top: 70px;"><em>My
            Favorite Places</em></h1>

    <div style="height: 100vh;" class="box0">

        <div style="margin-top: 70px; height: 70vh; display: flex; flex-wrap: wrap; justify-content: center; gap: 40px;">
            <?php 
            $margins = ['margin-left:150px;', '', 'margin-right: 150px;'];
            
            if (count($favoris) == 0) {
                echo "<p style='text-align:center; font-size: 20px; width: 100%;'>Vous n'avez pas encore de favoris.</p>";
            } else {
                for ($i = 0; $i < count($favoris); $i++) {
                    $nom = $favoris[$i]['nom'];
                    $image = $favoris[$i]['image_url'];
                    $margin = isset($margins[$i]) ? $margins[$i] : '';
            ?>
            <div class="card" style="height: 50vh;width:45vh; <?php echo $margin; ?>">
                <div style="height: 30vh;width:45vh; background-image: url('<?php echo htmlspecialchars($image); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                <div class="b" style="height: 20vh;width:45vh;">
                    <h3 style="margin-bottom: 0px;"><?php if($i==0) echo "<em>"; ?><?php echo htmlspecialchars($nom); ?><?php if($i==0) echo "</em>"; ?></h3>
                    <form method="POST" action="favoris.php" style="display:inline;">
                        <input type="hidden" name="remove_lieu" value="<?php echo $favoris[$i]['id_lieu']; ?>">
                        <button type="submit" class="bouton bou btn fav-remove" style="padding: 20px 25px;">
                            <i class="fa-solid fa-heart full"></i>
                            <i class="fa-regular fa-heart empty"></i>
                            Remove From Favorite
                        </button>
                    </form>
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

