<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
require_once '../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = trim($_POST['category']);

    if (!empty($title) && !empty($content) && !empty($category_id)) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, category_id, user_id) VALUES (:title, :content, :category_id, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        header('Location: myposts.php');
        exit;
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT p.id, p.title, p.created_at, c.name AS category_name 
                        FROM posts p
                        JOIN categories c ON p.category_id = c.id
                        WHERE p.user_id = :user_id 
                        ORDER BY p.created_at DESC");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user_posts = $stmt->fetchAll();

$stmt_replies = $pdo->prepare("SELECT r.*, p.title AS post_title 
                               FROM replies r
                               JOIN posts p ON r.post_id = p.id
                               WHERE r.user_id = :user_id
                               ORDER BY r.created_at DESC");
$stmt_replies->bindParam(':user_id', $_SESSION['user_id']);
$stmt_replies->execute();
$user_replies = $stmt_replies->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <title>For'Art - Mes Posts</title>
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
    <div class="row">
        <div class="col-md-6">
            <h2>Créer un Post</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="myposts.php" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Titre du Post</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Contenu du Post</label>
                    <textarea id="content" name="content" class="form-control" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Catégorie</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Choisir une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="publication">Publier le Post</button>
            </form>
        </div>

        <div class="col-md-6">
            <h2>Mes Posts</h2>

            <?php if (count($user_posts) > 0): ?>
                <?php foreach ($user_posts as $post): ?>
                    <div class="post-card">
                        <h4><?= htmlspecialchars($post['title']) ?></h4>
                        <p class="category-name">Catégorie: <?= htmlspecialchars($post['category_name']) ?></p>
                        <p><small>Publié le <?= date('d/m/Y', strtotime($post['created_at'])) ?></small></p>
                        <a href="post.php?id=<?= $post['id'] ?>" class="btn btn-info">Voir les réponses</a>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun post trouvé.</p>
            <?php endif; ?>

            <h2>Mes Réponses</h2>

            <?php if (count($user_replies) > 0): ?>
                <?php foreach ($user_replies as $reply): ?>
                    <div class="reply-card">
                        <h5>Réponse à : <?= htmlspecialchars($reply['post_title']) ?></h5>
                        <p><strong>Titre de la réponse:</strong> <?= htmlspecialchars($reply['title']) ?></p>
                        <p><small>Répondu le <?= date('d/m/Y', strtotime($reply['created_at'])) ?></small></p>
                        <p><?= htmlspecialchars($reply['content']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune réponse trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="bg-dark text-center text-white py-3 mt-5">
    &copy; 2024 For'Art. Tous droits réservés.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>