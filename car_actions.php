<?php
require_once 'db.php'; // Connexion à MySQL

// Vérifier l'action soumise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    // Récupérer l'action (ajouter, modifier, supprimer)
    $action = $_POST['action'];

    if ($action == 'add') {
        // Récupérer les informations du formulaire
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
            // Définir le chemin de destination temporaire
            $upload_dir = 'uploads/images/';
            
            // Ajouter les informations de la voiture dans la base de données
            $sql = "INSERT INTO cars (model, brand, plate_number, price_per_day) 
                    VALUES (:model, :brand, :plate_number, :price_per_day)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':model' => $model,
                ':brand' => $brand,
                ':plate_number' => $plate_number,
                ':price_per_day' => $price_per_day
            ]);
            
            // Récupérer l'ID de la voiture récemment ajoutée
            $car_id = $pdo->lastInsertId();
            
            // Renommer l'image avec l'ID de la voiture
            $new_image_name = $car_id . '.' . pathinfo($image_name, PATHINFO_EXTENSION);
            $image_path = $upload_dir . $new_image_name;

            // Déplacer l'image dans le dossier de destination
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Mettre à jour la base de données avec le chemin de l'image
                $sql = "UPDATE cars SET image = :image_path WHERE id = :car_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':image_path' => $image_path,
                    ':car_id' => $car_id
                ]);

                echo "Voiture ajoutée avec succès !";
            } else {
                echo "Erreur lors de l'upload de l'image.";
            }
        }
    }
    
    // Vous pouvez ajouter d'autres actions comme la modification et la suppression ici
    // Par exemple, pour la suppression :
    if ($action == 'delete') {
        $car_id = $_POST['car_id'];
        
        // Supprimer l'image de l'upload (si nécessaire)
        $sql = "SELECT image FROM cars WHERE id = :car_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':car_id' => $car_id]);
        $car = $stmt->fetch();
        $image_path = $car['image'];
        if (file_exists($image_path)) {
            unlink($image_path); // Supprimer le fichier image
        }
        
        // Supprimer la voiture de la base de données
        $sql = "DELETE FROM cars WHERE id = :car_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':car_id' => $car_id]);
        
        echo "Voiture supprimée avec succès !";
    }
    
    // Pour la modification, vous pouvez ajouter une logique similaire
}
?>
