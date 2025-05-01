<?php
require_once 'db.php';

function getAvailableRanges($reservations, $today)
{
    $available = [];
    $last_end = $today;

    foreach ($reservations as $res) {
        $start = $res['start_date'];
        $end = $res['end_date'];

        if ($last_end < $start) {
            $available[] = ['start' => $last_end, 'end' => date('Y-m-d', strtotime($start . ' -1 day'))];
        }

        if ($end >= $last_end) {
            $last_end = date('Y-m-d', strtotime($end . ' +1 day'));
        }
    }

    $available[] = ['start' => $last_end, 'end' => date('Y-m-d', strtotime('+1 year'))];

    return $available;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'];
    $client_name = $_POST['client_name'];
    $client_national_id = $_POST['client_national_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Vérifier conflits de dates
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE car_id = ? AND NOT (end_date < ? OR start_date > ?)");
    $stmt->execute([$car_id, $start_date, $end_date]);
    $conflicts = $stmt->fetchAll();

    if (count($conflicts) > 0) {
        echo "<p>🚫 Erreur : cette voiture est déjà réservée durant cette période.</p>";
        echo '<a href="javascript:history.back()">Retour</a>';
        exit;
    }

    // Continuer si pas de conflit
    $stmt = $pdo->prepare("SELECT price_per_day FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();

    if ($car) {
        $price_per_day = $car['price_per_day'];
        $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
        if ($days < 1) $days = 1;

        $total_price = $days * $price_per_day;

        $stmt = $pdo->prepare("INSERT INTO reservations (car_id, client_name, client_national_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$car_id, $client_name, $client_national_id, $start_date, $end_date, $total_price]);

        echo "<p>✅ Réservation enregistrée avec succès !</p>";
        echo '<a href="cars_display.php">Retour à la liste des voitures</a>';
        exit;
    } else {
        echo "<p>Erreur : voiture introuvable.</p>";
        exit;
    }
}

if (!isset($_GET['car_id'])) {
    echo "ID de voiture manquant.";
    exit;
}

$car_id = $_GET['car_id'];

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    echo "Voiture non trouvée.";
    exit;
}

$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT start_date, end_date FROM reservations WHERE car_id = ? AND end_date >= ? ORDER BY start_date ASC");
$stmt->execute([$car_id, $today]);
$reservations = $stmt->fetchAll();

$availableRanges = getAvailableRanges($reservations, $today);

$price_per_day = $car['price_per_day'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver la voiture</title>
    <script>
        function calculerPrix() {
            const start = new Date(document.getElementById("start_date").value);
            const end = new Date(document.getElementById("end_date").value);
            const pricePerDay = parseFloat(document.getElementById("price_per_day").value);

            if (!isNaN(start.getTime()) && !isNaN(end.getTime())) {
                let diffTime = end - start;
                let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (days < 1) days = 1;

                document.getElementById("days").value = days;
                document.getElementById("total_price").value = (days * pricePerDay).toFixed(2) + " €";
            }
        }
    </script>
</head>
<body>

<h1>Réserver : <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h1>

<?php if ($reservations): ?>
    <p>🚫 La voiture est déjà réservée pendant les périodes suivantes :</p>
    <ul>
        <?php foreach ($reservations as $res): ?>
            <li><?= htmlspecialchars($res['start_date']) ?> ➜ <?= htmlspecialchars($res['end_date']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>✅ Cette voiture n'a encore aucune réservation.</p>
<?php endif; ?>


<form method="post" action="reserve.php">
    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
    <input type="hidden" id="price_per_day" value="<?= $price_per_day ?>">

    <label>Nom du client :</label><br>
    <input type="text" name="client_name" required><br><br>

    <label>CIN / ID national :</label><br>
    <input type="text" name="client_national_id" required><br><br>

    <label>Date de début :</label><br>
    <input type="date" id="start_date" name="start_date" min="<?= $today ?>" required onchange="calculerPrix()"><br><br>

    <label>Date de fin :</label><br>
    <input type="date" id="end_date" name="end_date" min="<?= $today ?>" required onchange="calculerPrix()"><br><br>

    <label>Nombre de jours :</label><br>
    <input type="text" id="days" readonly><br><br>

    <label>Prix total estimé :</label><br>
    <input type="text" id="total_price" readonly><br><br>

    <button type="submit">Confirmer la réservation</button>
</form>
<a href="cars_display.php">
    <button type="button">⬅ Retour</button>
</a>


</body>
</html>
