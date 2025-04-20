<?php
require_once 'db.php';  // Connexion à la base de données

// Vérifier si l'ID de la voiture est passé dans l'URL
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    // Récupérer les informations actuelles de la voiture à partir de la base de données
    $sql = "SELECT * FROM cars WHERE id = :car_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
    $stmt->execute();
    $car = $stmt->fetch();

    // Si les informations sont envoyées en POST, procéder à la mise à jour
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les nouvelles valeurs du formulaire
        $model = $_POST['model'];
        $brand = $_POST['brand'];
        $plate_number = $_POST['plate_number'];
        $price_per_day = $_POST['price_per_day'];
        
        // Mise à jour de la voiture
        $update_sql = "UPDATE cars SET model = :model, brand = :brand, plate_number = :plate_number, price_per_day = :price_per_day WHERE id = :car_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->bindParam(':model', $model);
        $update_stmt->bindParam(':brand', $brand);
        $update_stmt->bindParam(':plate_number', $plate_number);
        $update_stmt->bindParam(':price_per_day', $price_per_day);
        $update_stmt->bindParam(':car_id', $car_id);
        $update_stmt->execute();

        // Rediriger vers la page de gestion des voitures après modification
        header('Location: manage.php');
        exit;
    }
} else {
    // Si l'ID de la voiture n'est pas passé, rediriger vers manage.php
    header('Location: manage.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une voiture</title>
</head>
<body>
    <h1>Modifier une voiture</h1>

    <!-- Formulaire de modification de voiture -->
    <form action="edit_car.php?car_id=<?= $car['id'] ?>" method="post">
        <label>Modèle :</label><br>
        <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required><br><br>

        <label>Marque :</label><br>
        <input type="text" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required><br><br>

        <label>Plaque :</label><br>
        <input type="text" name="plate_number" value="<?= htmlspecialchars($car['plate_number']) ?>" required><br><br>

        <label>Prix par jour :</label><br>
        <input type="number" step="0.01" name="price_per_day" value="<?= htmlspecialchars($car['price_per_day']) ?>" required><br><br>

        <button type="submit">Sauvegarder les modifications</button>
    </form>
</body>
</html>
