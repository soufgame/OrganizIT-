<?php
session_start();
$error = "";
$logged_in = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'admin') {
        // Connexion réussie
        $_SESSION['logged_in'] = true;
        header("Location: accueil.html");  // Redirection vers accueil.html
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $logged_in = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $logged_in ? 'Accueil' : 'Connexion'; ?></title>
</head>
<body>
    <?php if ($logged_in): ?>
        <!-- Page d'accueil -->
        <h2>Bienvenue sur la page d'accueil</h2>
        <p>Vous êtes connecté en tant qu'administrateur.</p>
        <a href="?logout=1">Se déconnecter</a>

        <?php
        // Gérer la déconnexion
        if (isset($_GET['logout'])) {
            session_destroy();
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        ?>
    <?php else: ?>
        <!-- Page de connexion -->
        <h2>Page de Connexion</h2>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label>Nom d'utilisateur:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Mot de passe:</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Se connecter</button>
        </form>
    <?php endif; ?>
</body>
</html>
