<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Sélection du Mode') ?></title>
    <link rel="stylesheet" href="assets/styles/stylesheet.css">
    <style>
        .mode-selection-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .mode-selection-title {
            text-align: center;
            color: white;
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .mode-selection-subtitle {
            text-align: center;
            color: #f0f0f0;
            font-size: 1.2em;
            margin-bottom: 40px;
        }

        .mode-options {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .mode-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            width: 300px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .mode-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .mode-icon {
            font-size: 5em;
            margin-bottom: 20px;
        }

        .mode-title {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .mode-description {
            font-size: 1em;
            color: #666;
            line-height: 1.6;
        }

        .mode-features {
            list-style: none;
            padding: 0;
            margin-top: 20px;
            text-align: left;
        }

        .mode-features li {
            padding: 8px 0;
            color: #555;
            border-bottom: 1px solid #eee;
        }

        .mode-features li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 8px;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .back-link a:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="mode-selection-container">
        <h1 class="mode-selection-title">🎮 L'Héritage Chiffré</h1>
        <p class="mode-selection-subtitle">Choisissez votre mode de jeu</p>

        <div class="mode-options">
            <!-- Mode Groupe -->
            <a href="index.php?controller=team&action=showTeamSelection&mode=group" class="mode-card">
                <div class="mode-icon">👥</div>
                <div class="mode-title">Mode Groupe</div>
                <div class="mode-description">
                    Jouez en équipe et coopérez pour résoudre les énigmes ensemble.
                </div>
                <ul class="mode-features">
                    <li>4 à 6 joueurs par équipe</li>
                    <li>Choisissez Alice ou Bob</li>
                    <li>Coopération avec une autre équipe</li>
                    <li>Idéal pour l'événement du 18 mars</li>
                </ul>
            </a>

            <!-- Mode Solo -->
            <a href="index.php?controller=team&action=showTeamSelection&mode=solo" class="mode-card">
                <div class="mode-icon">🎯</div>
                <div class="mode-title">Mode Solo</div>
                <div class="mode-description">
                    Explorez l'aventure à votre rythme, seul(e) face aux énigmes.
                </div>
                <ul class="mode-features">
                    <li>Jouez en solitaire</li>
                    <li>Accès aux deux personnages</li>
                    <li>Codes automatiquement fusionnés</li>
                    <li>Idéal pour tester ou rejouer</li>
                </ul>
            </a>
        </div>

        <div class="back-link">
            <a href="index.php?controller=redirection&action=openHomepage">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>

