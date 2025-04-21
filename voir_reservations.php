<?php
require_once 'db.php';

// Supprimer une réservation si demandé
if (isset($_GET['delete'])) {
    $reservation_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);
    header("Location: voir_reservations.php");
    exit;
}

// Récupérer toutes les réservations avec jointure sur la table des voitures
$sql = "SELECT 
            r.id AS reservation_id,
            c.model, c.brand, c.plate_number,
            r.client_name, r.client_national_id,
            r.start_date, r.end_date
        FROM reservations r
        JOIN cars c ON r.car_id = c.id
        ORDER BY r.start_date DESC";

$stmt = $pdo->query($sql);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Réservations</title>
    <style>
        /* Style du menu */
        nav {
            background-color: #333;
            padding: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin-right: 10px;
        }
        nav a:hover {
            background-color: #575757;
        }
    </style>
</head>
<body>

<!-- Menu -->
<nav>
<a href="cars_display.php">Home</a>
<a href="manage.php">Cars</a>
<a href="voir_reservations.php">Reservation</a>
<a href="logout.php">Logout</a>

</nav>

<h1>Réservations</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>Voiture</th>
        <th>Marque</th>
        <th>Plaque</th>
        <th>Client</th>
        <th>CIN / ID</th>
        <th>Date début</th>
        <th>Date fin</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($reservations as $res): ?>
        <tr>
            <td><?= htmlspecialchars($res['model']) ?></td>
            <td><?= htmlspecialchars($res['brand']) ?></td>
            <td><?= htmlspecialchars($res['plate_number']) ?></td>
            <td><?= htmlspecialchars($res['client_name']) ?></td>
            <td><?= htmlspecialchars($res['client_national_id']) ?></td>
            <td><?= htmlspecialchars($res['start_date']) ?></td>
            <td><?= htmlspecialchars($res['end_date']) ?></td>
            <td>
                <a href="modifier_reservation.php?id=<?= $res['reservation_id'] ?>">Modifier</a> |
                <a href="voir_reservations.php?delete=<?= $res['reservation_id'] ?>" onclick="return confirm('Supprimer cette réservation ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
