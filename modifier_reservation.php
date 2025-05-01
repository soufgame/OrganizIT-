<?php
require_once 'db.php';

// Vérifier si l’ID est présent
if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>ID de réservation non spécifié.</p>";
    exit;
}

$reservation_id = $_GET['id'];

// Récupérer les infos actuelles de la réservation
$stmt = $pdo->prepare("
    SELECT r.*, c.price_per_day, c.model, c.id AS car_id 
    FROM reservations r 
    JOIN cars c ON r.car_id = c.id 
    WHERE r.id = ?
");
$stmt->execute([$reservation_id]);
$res = $stmt->fetch();

if (!$res) {
    echo "<p style='color:red;'>Réservation introuvable.</p>";
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];
    $client_national_id = $_POST['client_national_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end)->days;

    if ($interval < 1) {
        $message = "❌ La date de fin doit être après la date de début.";
    } else {
        // Vérifier conflit
        $car_id = $res['car_id'];

        $conflict = $pdo->prepare("
            SELECT * FROM reservations 
            WHERE car_id = ? 
            AND id != ? 
            AND NOT (end_date < ? OR start_date > ?)
        ");
        $conflict->execute([$car_id, $reservation_id, $start_date, $end_date]);

        if ($conflict->rowCount() > 0) {
            $message = "❌ Conflit : la voiture est déjà réservée à ces dates.";
        } else {
            $total_price = $interval * $res['price_per_day'];

            $update = $pdo->prepare("UPDATE reservations 
                SET client_name = ?, client_national_id = ?, start_date = ?, end_date = ?, total_price = ? 
                WHERE id = ?");
            $update->execute([
                $client_name,
                $client_national_id,
                $start_date,
                $end_date,
                $total_price,
                $reservation_id
            ]);

            header("Location: voir_reservations.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Modifier Réservation</title>
</head>
<body>

<h1>Modifier Réservation</h1>
<p>Voiture : <strong><?= htmlspecialchars($res['model']) ?></strong> | Prix par jour : <strong><?= htmlspecialchars($res['price_per_day']) ?> €</strong></p>

<?php if ($message): ?>
    <p style="color: red;"><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <label>Nom du client :</label><br>
    <input type="text" name="client_name" value="<?= htmlspecialchars($res['client_name']) ?>" required><br><br>

    <label>CIN / ID :</label><br>
    <input type="text" name="client_national_id" value="<?= htmlspecialchars($res['client_national_id']) ?>" required><br><br>

    <label>Date début :</label><br>
    <input type="date" name="start_date" value="<?= $res['start_date'] ?>" required><br><br>

    <label>Date fin :</label><br>
    <input type="date" name="end_date" value="<?= $res['end_date'] ?>" required><br><br>

    <button type="submit">✅ Mettre à jour</button>
</form>

<p><a href="voir_reservations.php">⬅ Retour</a></p>

</body>
</html>
