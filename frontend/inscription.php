<?php
require_once '../backend/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if (strlen($password) >= 8) {
            if ($password === $confirm_password) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $error = "Le nom d'utilisateur est déjà pris.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->execute();

                    header("Location: login.php");
                    exit;
                }
            } else {
                $error = "Les mots de passe ne correspondent pas.";
            }
        } else {
            $error = "Le mot de passe doit contenir au moins 8 caractères.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <title>For'Art - Créer un compte</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../image/logoblanc.png" alt="Logo For'Art" width="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="categories.php">Catégories</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="myposts.php">Mes Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../backend/logout.php">
                            <img src="../image/logout.png" alt="Déconnexion" width="25">
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <img src="../image/login.png" alt="Connexion" width="25">
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav><br>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Créer un compte</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="form-control" required>
                <div class="invalid-feedback">Veuillez entrer un nom d'utilisateur.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                <div class="invalid-feedback">Le mot de passe doit contenir au moins 8 caractères.</div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <div class="invalid-feedback">Veuillez confirmer le mot de passe.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
        </form>
    </div>

    <footer class="bg-dark text-center text-white py-3 mt-5">
        &copy; 2024 For'Art. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

