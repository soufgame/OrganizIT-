<?php
$host = 'localhost'; // Adresse du serveur MySQL
$db = 'cars'; // Nom de la base de données
$user = 'root'; // Nom d'utilisateur de la base de données
$pass = ''; // Mot de passe de la base de données
$charset = 'utf8';

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options pour PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Création de la connexion PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En cas d'échec de connexion, afficher l'erreur
    die("Échec de la connexion à la base de données : " . $e->getMessage());
}
?>
