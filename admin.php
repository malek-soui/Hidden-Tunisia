<?php
require 'connexion_db.php';
session_start();

// Protection de la page (Optionnel : on peut vérifier si le role est 'admin')
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] != "admin") {
    header("Location: explorer.php");
    exit();
}

$message_succes = "";
$message_erreur = "";

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM lieux WHERE id_lieu = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $message_succes = "Lieu supprimé avec succès.";
    } catch (Exception $e) {
        $message_erreur = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Traitement de l'acceptation / refus
if (isset($_GET['action']) && in_array($_GET['action'], ['accept', 'decline', 'wait']) && isset($_GET['id'])) {
    try {
        $statut = 0;
        if ($_GET['action'] == 'accept') $statut = 1;
        if ($_GET['action'] == 'decline') $statut = -1;
        
        $stmt = $db->prepare("UPDATE lieux SET statut = :statut WHERE id_lieu = :id");
        $stmt->execute([':statut' => $statut, ':id' => $_GET['id']]);
        $message_succes = "Statut du lieu mis à jour avec succès.";
    } catch (Exception $e) {
        $message_erreur = "Erreur lors de la mise à jour du statut : " . $e->getMessage();
    }
}

// Traitement de la modification (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_lieu = $_POST['id_lieu'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $region = $_POST['region'];

    try {
        $stmt = $db->prepare("UPDATE lieux SET nom = :nom, description = :desc, region = :reg WHERE id_lieu = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':desc' => $description,
            ':reg' => $region,
            ':id' => $id_lieu
        ]);
        $message_succes = "Lieu mis à jour avec succès.";
    } catch (Exception $e) {
        $message_erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

// Récupérer le lieu à modifier si action=edit_form
$lieu_a_modifier = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit_form' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM lieux WHERE id_lieu = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $lieu_a_modifier = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer tous les lieux pour l'affichage
$stmt = $db->query("SELECT * FROM lieux");
$lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="an" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hidden Tunisia - Administration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #f5efe2;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #c5a269;
            text-align: left;
            color: #4B3A2F;
        }

        th {
            background-color: #c5a269;
            color: white;
        }

        .action-btn {
            padding: 5px 10px;
            background-color: #4B3A2F;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }

        .action-btn.delete {
            background-color: red;
        }
    </style>
</head>

<body style="background-color: #FAF8F3;">
    <header class="top-bar" style="display: flex ; align-items: center; gap: 10px;">
        <h2 style="width: 50%;"><b><em>
                    <font face=italique><span style="color:#4B3A2F;">Hidden</span> Tunisia</font>
                </em></b></h2>
        <div class="bar" style="width: 50%;">
            <a href="index.html">Home</a> |
            <a href="explorer.php">Explore</a> |
            <a href="ajouter.php">Add Place</a> |
            <a href="contact.php">Contact</a> |
            <a href="favoris.php">Favorite</a> |
            <a href="about.html">About</a>
            | <a href="logout.php">Logout</a>
        </div>
    </header>

    <h1 style="text-align: center; color:#4B3A2F; margin-top: 40px;"><em>Espace Administration</em></h1>

    <?php if (!empty($message_succes)) echo "<p style='text-align:center; color:green; font-weight:bold;'>$message_succes</p>"; ?>
    <?php if (!empty($message_erreur)) echo "<p style='text-align:center; color:red; font-weight:bold;'>$message_erreur</p>"; ?>

    <!-- Formulaire de modification -->
    <?php if ($lieu_a_modifier): ?>
        <form class="form" method="POST" action="admin.php" style="height:auto; margin-bottom: 50px;">
            <h2 style="color:#4B3A2F;">Modifier le lieu : <?php echo htmlspecialchars($lieu_a_modifier['nom']); ?></h2>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_lieu" value="<?php echo $lieu_a_modifier['id_lieu']; ?>">

            <input class="input" type="text" name="nom" value="<?php echo htmlspecialchars($lieu_a_modifier['nom']); ?>" required>
            <textarea class="input" name="description" required><?php echo htmlspecialchars($lieu_a_modifier['description']); ?></textarea>
            <input class="input" type="text" name="region" value="<?php echo htmlspecialchars($lieu_a_modifier['region']); ?>" required>

            <button type="submit" class="bouton btn">Enregistrer les modifications</button>
            <a href="admin.php" style="margin-top:10px; color:#4B3A2F;">Annuler</a>
        </form>
    <?php endif; ?>

    <!-- Tableau des lieux -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Région</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($lieux as $lieu): ?>
            <tr>
                <td><?php echo $lieu['id_lieu']; ?></td>
                <td><?php echo htmlspecialchars($lieu['nom']); ?></td>
                <td><?php echo htmlspecialchars($lieu['region']); ?></td>
                <td>
                    <?php 
                        if ($lieu['statut'] == 1) echo "<span style='color:green;'>Accepté</span>";
                        elseif ($lieu['statut'] == -1) echo "<span style='color:red;'>Refusé</span>";
                        else echo "<span style='color:orange;'>En attente</span>";
                    ?>
                </td>
                <td>
                    <?php if ($lieu['statut'] != 1): ?>
                        <a href="admin.php?action=accept&id=<?php echo $lieu['id_lieu']; ?>" class="action-btn" style="background-color: green;">Accepter</a>
                    <?php endif; ?>
                    <?php if ($lieu['statut'] != -1): ?>
                        <a href="admin.php?action=decline&id=<?php echo $lieu['id_lieu']; ?>" class="action-btn" style="background-color: darkorange;">Refuser</a>
                    <?php endif; ?>
                    <a href="admin.php?action=edit_form&id=<?php echo $lieu['id_lieu']; ?>" class="action-btn">Modifier</a>
                    <a href="admin.php?action=delete&id=<?php echo $lieu['id_lieu']; ?>" class="action-btn delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce lieu ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div style="text-align: center; margin-top: 30px; margin-bottom: 50px;">
        <a href="ajouter.php" class="bouton" style="text-decoration:none;">Ajouter un nouveau lieu</a>
    </div>

    <footer class="fo">
        <p style="margin :0 0 !important;"><em>© 2026 Hidden Tunisia.</em></p>
    </footer>
</body>

</html>