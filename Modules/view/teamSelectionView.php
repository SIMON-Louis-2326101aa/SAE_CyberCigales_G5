<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Sélection de l\'Équipe') ?></title>
    <link rel="stylesheet" href="assets/styles/stylesheet.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }

        .team-selection-container {
            max-width: 900px;
            width: 90%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            padding: 50px;
        }

        .team-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .team-header h1 {
            font-size: 2.5em;
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .team-header .subtitle {
            font-size: 1.2em;
            color: #666;
        }

        .mode-badge {
            display: inline-block;
            padding: 8px 20px;
            background: <?= ($is_solo ?? false) ? '#f39c12' : '#3498db' ?>;
            color: white;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Messages d'erreur et succès */
        .error-message {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message {
            background: #27ae60;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Affichage de l'équipe actuelle */
        .current-team-info {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .current-team-info h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .current-team-info p {
            margin: 10px 0;
            color: #555;
        }

        .current-team-info .change-team-btn {
            background: #e67e22;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 10px;
        }

        .current-team-info .change-team-btn:hover {
            background: #d35400;
        }

        /* Formulaire de sélection */
        .team-form {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .team-options {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .team-option {
            position: relative;
        }

        .team-option input[type="radio"] {
            display: none;
        }

        .team-card {
            background: #f8f9fa;
            border: 3px solid #ddd;
            border-radius: 15px;
            padding: 30px;
            width: 250px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .team-option input[type="radio"]:checked + .team-card {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            transform: scale(1.05);
        }

        .team-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
        }

        .team-character {
            font-size: 5em;
            margin-bottom: 15px;
        }

        .team-name {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .team-description {
            font-size: 0.95em;
            color: #666;
            line-height: 1.5;
        }

        /* Session ID (si mode groupe) */
        .session-selection {
            text-align: center;
        }

        .session-selection label {
            font-size: 1.2em;
            color: #333;
            display: block;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .session-selection select {
            padding: 12px 20px;
            font-size: 1.1em;
            border: 2px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            background: white;
            transition: border-color 0.3s ease;
        }

        .session-selection select:focus {
            border-color: #667eea;
            outline: none;
        }

        /* Bouton de validation */
        .submit-section {
            text-align: center;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 50px;
            border: none;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.6);
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Lien retour */
        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
        }
    </style>
    <script>
        function validateForm() {
            const teamSelected = document.querySelector('input[name="team_name"]:checked');
            if (!teamSelected) {
                alert('Veuillez sélectionner une équipe (Alice ou Bob).');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="team-selection-container">
        <div class="team-header">
            <span class="mode-badge">
                <?= ($is_solo ?? false) ? '🎯 Mode Solo' : '👥 Mode Groupe' ?>
            </span>
            <h1>Sélection de l'Équipe</h1>
            <p class="subtitle">Choisissez votre personnage pour commencer l'aventure</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                ✓ <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($current_team) && $current_team): ?>
            <div class="current-team-info">
                <h3>📋 Votre équipe actuelle</h3>
                <p><strong>Équipe :</strong> <?= htmlspecialchars($current_team['team_name']) ?></p>
                <p><strong>Session :</strong> <?= htmlspecialchars($current_team['session_id']) ?></p>
                <p><strong>Mode :</strong> <?= $current_team['is_solo'] ? 'Solo' : 'Groupe' ?></p>
                <form method="POST" action="index.php?controller=team&action=leaveTeam" style="display: inline;">
                    <button type="submit" class="change-team-btn" onclick="return confirm('Êtes-vous sûr de vouloir quitter votre équipe actuelle ?')">
                        Changer d'équipe
                    </button>
                </form>
                <a href="index.php?controller=team&action=showTeamInfo" style="margin-left: 10px; color: #3498db; text-decoration: none;">
                    Voir les détails →
                </a>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=team&action=joinTeam" class="team-form" onsubmit="return validateForm()">
            <input type="hidden" name="is_solo" value="<?= ($is_solo ?? false) ? '1' : '0' ?>">

            <!-- Sélection de l'équipe (Alice ou Bob) -->
            <div class="team-options">
                <label class="team-option">
                    <input type="radio" name="team_name" value="Alice" required>
                    <div class="team-card">
                        <div class="team-character">👧</div>
                        <div class="team-name">Alice</div>
                        <div class="team-description">
                            Jeune passionnée de cryptographie, Alice découvre une lettre mystérieuse qui la mènera sur les traces de ses ancêtres.
                        </div>
                    </div>
                </label>

                <label class="team-option">
                    <input type="radio" name="team_name" value="Bob" required>
                    <div class="team-card">
                        <div class="team-character">👦</div>
                        <div class="team-name">Bob</div>
                        <div class="team-description">
                            Expert en cybersécurité en herbe, Bob reçoit également une lettre codée qui changera sa compréhension du passé.
                        </div>
                    </div>
                </label>
            </div>

            <!-- Sélection de la session (uniquement en mode groupe) -->
            <?php if (!($is_solo ?? false)): ?>
                <div class="session-selection">
                    <label for="session_id">Numéro de session :</label>
                    <select name="session_id" id="session_id" required>
                        <option value="1">Session 1 - 14h00</option>
                        <option value="2">Session 2 - 14h45</option>
                        <option value="3">Session 3 - 15h30</option>
                        <option value="4">Session 4 - 16h15</option>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="session_id" value="0">
            <?php endif; ?>

            <!-- Bouton de validation -->
            <div class="submit-section">
                <button type="submit" class="submit-btn">
                    <?= ($is_solo ?? false) ? '🚀 Commencer l\'aventure' : '🤝 Rejoindre l\'équipe' ?>
                </button>
            </div>
        </form>

        <div class="back-link">
            <a href="index.php?controller=team&action=showModeSelection">← Changer de mode</a>
        </div>
    </div>
</body>
</html>

