<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <title>For'Art - Accueil</title>
</head>
<body>
    <video class="video-bg" autoplay muted loop>
        <source src="../video/ABSTRACT.mp4" type="video/mp4">
        Votre navigateur ne supporte pas les vidéos HTML5.
    </video>
    <div class="overlay"></div>
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
</nav>

    <div class="content">
        <h1 class="mb-4">Bienvenue sur <b><span id="gradient-text">For'Art</span></b></h1>
        <p class="lead">Explorez des sujets passionnants sur l'art et échangez avec d'autres passionnés.</p><br>
        <a href="categories.php" class="explorer-btn">Explorer les catégories</a>
    </div>

    <footer>
        &copy; 2024 For'Art. Tous droits réservés.
    </footer>

    <span id="gradient-text">For'Art</span>

<script>
    const textElement = document.getElementById('gradient-text');
    const colors = ['#0000FF', '#D84B9A', '#FFF000'];
    const text = textElement.innerText; 
    const totalColors = colors.length;
    textElement.innerText = '';

            // Fait grace à l'aide de l'IA pour cette partie de couleur

    for (let i = 0; i < text.length; i++) {
        const charSpan = document.createElement('span');
        charSpan.innerText = text[i];
        const gradientIndex = (i / text.length) * (totalColors - 1);
        const startColor = colors[Math.floor(gradientIndex)];
        const endColor = colors[Math.ceil(gradientIndex)];
        const ratio = gradientIndex - Math.floor(gradientIndex);

        const interpolateColor = (start, end, factor) => {
            const startRGB = parseInt(start.slice(1), 16);
            const endRGB = parseInt(end.slice(1), 16);
            const r = Math.round(((endRGB >> 16) - (startRGB >> 16)) * factor + (startRGB >> 16));
            const g = Math.round((((endRGB >> 8) & 0xff) - ((startRGB >> 8) & 0xff)) * factor + ((startRGB >> 8) & 0xff));
            const b = Math.round(((endRGB & 0xff) - (startRGB & 0xff)) * factor + (startRGB & 0xff));
            return `rgb(${r}, ${g}, ${b})`;
        };

        const letterColor = interpolateColor(startColor, endColor, ratio);
        charSpan.style.color = letterColor;
        textElement.appendChild(charSpan);
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
