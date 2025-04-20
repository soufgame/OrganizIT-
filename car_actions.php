<?php
require_once 'db.php'; // Connexion à la base de données

// Récupérer toutes les voitures pour les afficher
function getCars() {
    global $pdo;
    $sql = "SELECT * FROM cars";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Ajouter une voiture
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer l'action
    $action = $_POST['action'];

    // Ajouter une voiture
    if ($action == 'add') {
        $model = $_POST['model'];
        $brand = $_POST['brand'];
        $plate_number = $_POST['plate_number'];
        $price_per_day = $_POST['price_per_day'];

        // Traitement de l'image
        $image = $_FILES['image'];
        $image_name = $image['name'];
        $image_tmp_name = $image['tmp_name'];
        $image_error = $image['error'];

        // Si l'image est correctement téléchargée
        if ($image_error === UPLOAD_ERR_OK) {
            // Définir le chemin de destination
            $upload_dir = 'uploads/images/';
            $image_path = $upload_dir . basename($image_name);

            // Déplacer l'image dans le dossier de destination
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Ajouter les informations de la voiture dans la base de données
                $sql = "INSERT INTO cars (model, brand, plate_number, price_per_day, image) 
                        VALUES (:model, :brand, :plate_number, :price_per_day, :image_path)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':model' => $model,
                    ':brand' => $brand,
                    ':plate_number' => $plate_number,
                    ':price_per_day' => $price_per_day,
                    ':image_path' => $image_path
                ]);

                echo "Voiture ajoutée avec succès !";
            } else {
                echo "Erreur lors de l'upload de l'image.";
            }
        }
    }

    // Modifier une voiture
    if ($action == 'edit') {
        $car_id = $_POST['car_id'];
        // Assurez-vous de récupérer les nouveaux champs à modifier
        $new_model = $_POST['model'];
        $sql = "UPDATE cars SET model = :new_model WHERE id = :car_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':new_model' => $new_model,
            ':car_id' => $car_id
        ]);
        echo "Voiture modifiée avec succès !";
    }

    // Supprimer une voiture
    if ($action == 'delete') {
        $car_id = $_POST['car_id'];

        // Supprimer la voiture de la base de données
        $sql = "DELETE FROM cars WHERE id = :car_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':car_id' => $car_id
        ]);
        echo "Voiture supprimée avec succès !";
    }
}

// Récupérer les voitures à afficher dans manage.php
$cars = getCars();
?>
