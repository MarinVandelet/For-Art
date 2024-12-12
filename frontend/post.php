<?php
session_start();
require_once '../backend/db.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id > 0) {
    $stmt = $pdo->prepare("SELECT posts.*, users.username, categories.image AS category_image 
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            JOIN categories ON posts.category_id = categories.id
                            WHERE posts.id = :post_id");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt_replies = $pdo->prepare("SELECT replies.*, users.username AS reply_username 
                                   FROM replies 
                                   JOIN users ON replies.user_id = users.id
                                   WHERE replies.post_id = :post_id
                                   ORDER BY replies.created_at ASC");
    $stmt_replies->bindParam(':post_id', $post_id);
    $stmt_replies->execute();
    $replies = $stmt_replies->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: categories.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO replies (post_id, user_id, title, content, created_at) 
                            VALUES (:post_id, :user_id, :title, :content, NOW())");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->execute();

    header("Location: post.php?id=" . $post_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?= htmlspecialchars($post['title']) ?> - For'Art</title>
    <style>
        .banner {
            position: relative;
            height: 300px;
            background-image: url('<?= htmlspecialchars($post['category_image']) ?>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 50px 0;
        }
        .banner h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .chat-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }
        .post-message {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .reply-message {
            background-color: #e0e0e0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            margin-left: 30px;
        }
        .reply-title {
            font-weight: bold;
            font-size: 1.1rem;
        }
    </style>
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
        <h1><?= htmlspecialchars($post['title']) ?></h1>
    </div>

    <div class="container chat-container">
        <div class="post-message">
            <p><strong><?= htmlspecialchars($post['username']) ?></strong> (<?= htmlspecialchars($post['created_at']) ?>)</p>
            <p><?= htmlspecialchars($post['content']) ?></p>
        </div>

        <?php foreach ($replies as $reply): ?>
            <div class="reply-message">
                <p class="reply-title"><?= htmlspecialchars($reply['title']) ?></p>
                <p><strong><?= htmlspecialchars($reply['reply_username']) ?></strong> (<?= htmlspecialchars($reply['created_at']) ?>)</p>
                <p><?= htmlspecialchars($reply['content']) ?></p>
            </div>
        <?php endforeach; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="post.php?id=<?= $post_id ?>" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Titre de la réponse</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Votre réponse</label>
                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Répondre</button>
            </form>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-center text-white py-3 mt-5">
        &copy; 2024 For'Art. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
