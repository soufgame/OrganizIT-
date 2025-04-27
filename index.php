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
        header("Location: cars_display.php");  // Redirection vers accueil.html
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $logged_in ? 'Accueil' : 'Connexion'; ?></title>
    <style>
        :root {
            --primary-color: #333;
            --secondary-color: #666;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .container {
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            margin-top: 5px;
        }
        .btn {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error-message {
            color: red;
            margin: 10px 0;
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <?php if ($logged_in): ?>
            <header>
                <h1>Bienvenue sur OrganizIT</h1>
            </header>
            <section aria-label="Informations utilisateur">
                <p>Vous êtes connecté en tant qu'administrateur.</p>
                <nav>
                    <a href="?logout=1" class="btn" role="button">Se déconnecter</a>
                </nav>
            </section>

            <?php
            // Gérer la déconnexion
            if (isset($_GET['logout'])) {
                session_destroy();
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
            ?>
        <?php else: ?>
            <header>
                <h1>Connexion à OrganizIT</h1>
            </header>
            <section aria-label="Formulaire de connexion">
                <?php if ($error): ?>
                    <div class="error-message" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="" novalidate>
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control"
                               required 
                               aria-required="true">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control"
                               required 
                               aria-required="true">
                    </div>

                    <button type="submit" class="btn">Se connecter</button>
                </form>
            </section>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> OrganizIT. Tous droits réservés.</p>
    </footer>
</body>
</html>
