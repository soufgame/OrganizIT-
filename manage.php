

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Voitures</title>
    <link rel="stylesheet" href="style.css">
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

<h1>Gestion des Voitures</h1>

<!-- Formulaire pour ajouter une voiture -->
<h2>Ajouter une voiture</h2>
<form action="car_actions.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add">
    
    <label>Modèle :</label><br>
    <input type="text" name="model" required><br><br>

    <label>Marque :</label><br>
    <input type="text" name="brand" required><br><br>

    <label>Plaque :</label><br>
    <input type="text" name="plate_number" required><br><br>

    <label>Prix par jour :</label><br>
    <input type="number" step="0.01" name="price_per_day" required><br><br>

    <label>Image :</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit">Ajouter Voiture</button>
</form>

<hr>

<!-- Liste des voitures -->
<h2>Liste des voitures</h2>
<table border="1">
    <tr>
        <th>Modèle</th>
        <th>Marque</th>
        <th>Plaque</th>
        <th>Prix par jour</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>

    <?php
    require_once 'db.php';
    $sql = "SELECT * FROM cars";
    $stmt = $pdo->query($sql);
    $cars = $stmt->fetchAll();

    foreach ($cars as $car) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($car['model']) . '</td>';
        echo '<td>' . htmlspecialchars($car['brand']) . '</td>';
        echo '<td>' . htmlspecialchars($car['plate_number']) . '</td>';
        echo '<td>' . htmlspecialchars($car['price_per_day']) . '€</td>';
        echo '<td><img src="' . htmlspecialchars($car['image']) . '" width="100" alt="Image de la voiture"></td>';
        echo '<td>';
        // Formulaire de modification
        echo '<form action="edit_car.php" method="get" style="display:inline;">
                <input type="hidden" name="car_id" value="' . $car['id'] . '">
                <button type="submit" class="btn reserver-btn">Modifier</button>
              </form>';
        // Formulaire de suppression
        echo '<form action="car_actions.php" method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="car_id" value="' . $car['id'] . '">
                <button type="submit" class="btn supprimer-btn">Supprimer</button>
              </form>';
        echo '</td>';
        echo '</tr>';
    }
    ?>
</table>

</body>
</html>
