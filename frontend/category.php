<?php
session_start();
require_once '../backend/db.php';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->bindParam(':id', $category_id);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts 
                            JOIN users ON posts.user_id = users.id
                            WHERE posts.category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: categories.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <title><?= htmlspecialchars($category['name']) ?> - For'Art</title>
    <style>
        .banner {
            position: relative;
            height: 300px;
            background-image: url('<?= htmlspecialchars($category['image']) ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center; 
            color: white;
            text-align: center;
        }
        .banner h1 {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            margin: 0;
        }
        .post-list {
            margin-top: 20px;
        }
        .post-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .post-item h3 {
            cursor: pointer;
        }
    </style>
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
                        <li class="nav-item"><a class="nav-link" href="myposts.php">Mes Posts</a></li>
                        <li class="nav-item"><a class="nav-link" href="../backend/logout.php"><img src="../image/logout.png" alt="Déconnexion" width="25"></a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><img src="../image/login.png" alt="Connexion" width="25"></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav><br>

    <div class="banner">
        <h1><?= htmlspecialchars($category['name']) ?></h1>
    </div>
    
    <div class="container post-list">
        <h2>Posts dans cette catégorie</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <h3><a href="post.php?id=<?= $post['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($post['title']) ?></a></h3>
                <p><strong>Auteur :</strong> <?= htmlspecialchars($post['username']) ?> | <strong>Créé le :</strong> <?= htmlspecialchars($post['created_at']) ?></p>
                <p><?= htmlspecialchars($post['content']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <footer class="bg-dark text-center text-white py-3 mt-5">
        &copy; 2024 For'Art. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
