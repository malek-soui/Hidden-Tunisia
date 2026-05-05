<?php
require 'connexion_db.php';
session_start();

$id_lieu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM lieux WHERE id_lieu = :id");
$stmt->execute([':id' => $id_lieu]);
$lieu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lieu) {
    die("Place not found.");
}

$is_favori = false;
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

if ($id_user) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'add_favori') {
            // Check if it's already favored to prevent duplicate UI issues
            $checkStmt = $db->prepare("SELECT 1 FROM favoris WHERE id_user = :user AND id_lieu = :lieu");
            $checkStmt->execute([':user' => $id_user, ':lieu' => $id_lieu]);
            if (!$checkStmt->fetchColumn()) {
                try {
                    $insertStmt = $db->prepare("INSERT INTO favoris (id_user, id_lieu) VALUES (:user, :lieu)");
                    $insertStmt->execute([':user' => $id_user, ':lieu' => $id_lieu]);
                } catch (PDOException $e) {
                    if ($e->getCode() == '23000') { // Constraint violation (User does not exist)
                        session_destroy();
                        header("Location: login.php");
                        exit;
                    }
                }
            }
        } elseif ($_POST['action'] === 'remove_favori') {
            $delStmt = $db->prepare("DELETE FROM favoris WHERE id_user = :user AND id_lieu = :lieu");
            $delStmt->execute([':user' => $id_user, ':lieu' => $id_lieu]);
        }
        header("Location: details.php?id=" . $id_lieu);
        exit;
    }

    $favStmt = $db->prepare("SELECT 1 FROM favoris WHERE id_user = :user AND id_lieu = :lieu");
    $favStmt->execute([':user' => $id_user, ':lieu' => $id_lieu]);
    $is_favori = $favStmt->fetchColumn() ? true : false;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header("Location: login.php");
    exit;
}
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

<body style="background-color: #FAF8F3;">
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
    <div class="ma">
        <h1 style="height: 5vh; width: 195vh;margin:auto; font: size 100px;margin-top: 25px;margin-bottom: 25px;">
            <em><b><?php echo htmlspecialchars($lieu['nom']); ?></b></em></h1>
        <div class="photo1" style="height: 65vh;width: 195vh;margin:auto; background-image: url('<?php echo htmlspecialchars($lieu['image_url']); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
        <div style="height: 10vh; width: 195vh;margin: auto;">
            <h2><em><?php echo nl2br(htmlspecialchars($lieu['description'])); ?></em></h2>
        </div>
        <div style="height: 7vh; width: 195vh;margin: auto;">
            <h1><em>Destination Area: <?php echo htmlspecialchars($lieu['region']); ?> | Destination Type: <?php echo htmlspecialchars($lieu['type_destination'] ?? 'Unknown'); ?> </em></h1>
        </div>
        <div style="height: 14vh; width: 195vh;margin: auto;">
            <form method="POST" action="details.php?id=<?php echo $id_lieu; ?>" style="display:inline;">
                <?php if($is_favori): ?>
                    <input type="hidden" name="action" value="remove_favori">
                    <button type="submit" class="bouton bou btn fav-remove">
                        <i class="fa-solid fa-heart full"></i>
                        <i class="fa-regular fa-heart empty"></i>
                        Remove From Favorite
                    </button>
                <?php else: ?>
                    <input type="hidden" name="action" value="add_favori">
                    <button type="submit" class="bouton bou btn fav-add">
                        <i class="fa-regular fa-heart empty"></i>
                        <i class="fa-solid fa-heart full"></i>
                        Add To Favorite
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <footer class="fo">
        <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
    </footer>
</body>
</html>