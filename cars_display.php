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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            color: #2c3e50;
        }

        /* Style du menu */
        nav {
            background-color: #333;
            padding: 12px 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            margin-right: 15px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #575757;
            border-radius: 5px;
        }

        /* Voiture card style */
        .car-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 30px auto;
            max-width: 1200px;
        }

        .car-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 15px;
            padding: 20px;
            width: 270px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .car-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .car-card h3 {
            color: #34495e;
            margin: 10px 0;
        }

        .car-card p {
            color: #555;
            font-size: 15px;
            margin: 6px 0;
        }

        .reserve-btn {
            margin-top: 15px;
        }

        .reserve-btn button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reserve-btn button:hover {
            background-color: #2980b9;
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

<h1>Voitures disponibles</h1>

<div class="car-container">
<?php foreach ($cars as $car): ?>
    <div class="car-card">
        <img src="<?= findCarImage($car['id']) ?>" alt="Image Voiture">
        <h3><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h3>
        <p>Plaque : <?= htmlspecialchars($car['plate_number']) ?></p>
        <p>Prix / jour : <strong><?= htmlspecialchars($car['price_per_day']) ?> €</strong></p>

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
</div>

</body>
</html>
