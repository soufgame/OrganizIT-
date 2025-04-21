<?php
require_once 'db.php';

// Récupération des voitures
$sql = "SELECT * FROM cars";
$stmt = $pdo->query($sql);
$cars = $stmt->fetchAll();

function findCarImage($car_id) {
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($extensions as $ext) {
        $path = "uploads/images/{$car_id}.$ext";
        if (file_exists($path)) {
            return $path;
        }
    }
    return "uploads/images/default.png"; // image par défaut si rien trouvé
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des voitures</title>
    <style>
        .car-card {
            border: 1px solid #ccc;
            padding: 10px;
            width: 250px;
            display: inline-block;
            margin: 10px;
            text-align: center;
        }
        .car-card img {
            width: 200px;
            height: auto;
        }
        .reserve-btn {
            margin-top: 10px;
        }
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

<h1>voitures disponibles</h1>

<?php foreach ($cars as $car): ?>
    <div class="car-card">
        <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h3>
        <img src="<?= findCarImage($car['id']) ?>" alt="Image Voiture">
        <p>Plaque : <?= htmlspecialchars($car['plate_number']) ?></p>
        <p>Prix / jour : <?= htmlspecialchars($car['price_per_day']) ?> €</p>

        <!-- Récupère la prochaine date disponible -->
        <?php
        $sql_res = "SELECT MAX(end_date) AS last_date FROM reservations WHERE car_id = ?";
        $stmt_res = $pdo->prepare($sql_res);
        $stmt_res->execute([$car['id']]);
        $last_res = $stmt_res->fetch();
        $dispo = $last_res['last_date'] ?? date('Y-m-d');
        ?>


        <form method="get" action="reserve.php" class="reserve-btn">
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
            <button type="submit">Réserver</button>
        </form>
    </div>
<?php endforeach; ?>

</body>
</html>
