<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plan du site</title>
    <link rel="stylesheet" href="/public/assets/styles/stylesheet.css">
    <link rel="icon" href="/public/assets/images/favicon.ico">
    <style>
        /* Styles simples pour une liste claire */
        .sitemap-container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .sitemap-container h1 { margin-bottom: 1rem; }
        .sitemap-list { list-style: none; padding-left: 0; }
        .sitemap-list li { margin: .5rem 0; }
        .sitemap-list a { text-decoration: none; color: #0a58ca; }
        .sitemap-list a:hover { text-decoration: underline; }
    </style>
    </head>
<body>
    <div class="sitemap-container">
        <h1>Plan du site</h1>
        <ul class="sitemap-list">
            <?php if (!empty($links) && is_array($links)): ?>
                <?php foreach ($links as $link): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($link['href']); ?>">
                            <?php echo htmlspecialchars($link['label']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Aucun lien disponible.</li>
            <?php endif; ?>
        </ul>
        <p>
            <a href="index.php?controller=homepage&action=openHomepage">Retour à l'accueil</a>
        </p>
    </div>
</body>
</html>


