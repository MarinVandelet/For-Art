<?php
session_start();
require_once '../backend/db.php';

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <title>For'Art - Catégories</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../image/logoblanc.png" alt="Logo For'Art" width="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
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
        <h1 class="text-center mb-4">Catégories</h1>
        <div class="scroll-container" id="categories-container">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <a href="category.php?id=<?= htmlspecialchars($category['id']) ?>">
                        <img src="<?= htmlspecialchars($category['image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>" class="category-image">
                        <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="bg-dark text-center text-white py-3 mt-5">
        &copy; 2024 For'Art. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
