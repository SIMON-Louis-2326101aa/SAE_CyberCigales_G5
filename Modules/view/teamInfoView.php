<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Informations de l\'Équipe') ?></title>
    <link rel="stylesheet" href="assets/styles/stylesheet.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            padding: 30px 0;
        }

        .team-info-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            padding: 40px;
        }

        .team-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #ecf0f1;
            margin-bottom: 30px;
        }

        .team-header h1 {
            font-size: 2.5em;
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .team-badge {
            display: inline-block;
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 25px;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .info-card h3 {
            color: #333;
            font-size: 1.5em;
            margin-top: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card h3 .icon {
            font-size: 1.2em;
        }

        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            min-width: 120px;
        }

        .info-value {
            color: #333;
        }

        /* Section des membres */
        .members-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .member-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .member-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .member-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5em;
            font-weight: bold;
        }

        .member-details {
            display: flex;
            flex-direction: column;
        }

        .member-name {
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }

        .member-email {
            color: #666;
            font-size: 0.9em;
        }

        .member-joined {
            color: #999;
            font-size: 0.85em;
        }

        /* Progression */
        .progress-section {
            margin-top: 20px;
        }

        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            height: 30px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-fill {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 0.5s ease;
        }

        .progress-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .acte-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 8px;
        }

        .acte-label {
            font-weight: bold;
            color: #555;
        }

        .acte-value {
            color: #667eea;
            font-weight: bold;
        }

        /* Codes obtenus */
        .codes-section {
            margin-top: 20px;
        }

        .code-item {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 3px;
        }

        .code-label {
            font-size: 0.8em;
            color: #95a5a6;
            margin-bottom: 5px;
        }

        /* Boutons d'action */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #ecf0f1;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="team-info-container">
        <div class="team-header">
            <h1>🎮 Informations de l'Équipe</h1>
            <div class="team-badge">
                <?= htmlspecialchars($team['team_name'] ?? 'Équipe') ?>
                <?= ($team['is_solo'] ?? false) ? ' (Solo)' : ' (Groupe)' ?>
            </div>
        </div>

        <div class="info-grid">
            <!-- Informations générales -->
            <div class="info-card">
                <h3><span class="icon">📋</span> Détails de l'Équipe</h3>
                <div class="info-item">
                    <span class="info-label">Équipe :</span>
                    <span class="info-value"><?= htmlspecialchars($team['team_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Session :</span>
                    <span class="info-value">#<?= htmlspecialchars($team['session_id'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mode :</span>
                    <span class="info-value"><?= ($team['is_solo'] ?? false) ? '🎯 Solo' : '👥 Groupe' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Créée le :</span>
                    <span class="info-value"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($team['created_at'] ?? 'now'))) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Membres :</span>
                    <span class="info-value"><?= count($members ?? []) ?> joueur(s)</span>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="info-card">
                <h3><span class="icon">📊</span> Statistiques</h3>
                <?php if (!empty($stats['progress_by_acte'])): ?>
                    <div class="progress-details">
                        <?php foreach ($stats['progress_by_acte'] as $progress): ?>
                            <div class="acte-progress">
                                <span class="acte-label">
                                    Acte <?= htmlspecialchars($progress['acte']) ?>
                                </span>
                                <span class="acte-value">
                                    <?= htmlspecialchars($progress['completed_pages']) ?> énigme(s) résolue(s)
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">Aucune progression enregistrée pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Membres de l'équipe -->
        <div class="info-card">
            <h3><span class="icon">👥</span> Membres de l'Équipe</h3>
            <?php if (!empty($members)): ?>
                <ul class="members-list">
                    <?php foreach ($members as $member): ?>
                        <li class="member-item">
                            <div class="member-info">
                                <div class="member-avatar">
                                    <?= strtoupper(substr($member['prenom'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="member-details">
                                    <span class="member-name">
                                        <?= htmlspecialchars($member['prenom'] ?? '') ?> 
                                        <?= htmlspecialchars($member['nom'] ?? '') ?>
                                    </span>
                                    <span class="member-email">
                                        <?= htmlspecialchars($member['email'] ?? '') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="member-joined">
                                Rejoint le <?= htmlspecialchars(date('d/m/Y', strtotime($member['joined_at'] ?? 'now'))) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-data">Aucun membre dans cette équipe pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Codes obtenus -->
        <?php if (!empty($stats['codes'])): ?>
            <div class="info-card">
                <h3><span class="icon">🔐</span> Codes Obtenus</h3>
                <div class="codes-section">
                    <?php foreach ($stats['codes'] as $code): ?>
                        <div class="code-item">
                            <div class="code-label">Acte <?= htmlspecialchars($code['acte']) ?></div>
                            <div><?= htmlspecialchars($code['code_fragment']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Boutons d'action -->
        <div class="action-buttons">
            <a href="index.php?controller=redirection&action=openHomepage" class="btn btn-secondary">
                🏠 Retour à l'accueil
            </a>
            <a href="index.php?controller=game&action=startActe&acte=1" class="btn btn-primary">
                🎮 Continuer l'aventure
            </a>
            <form method="POST" action="index.php?controller=team&action=leaveTeam" style="display: inline;">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir quitter cette équipe ?')">
                    🚪 Quitter l'équipe
                </button>
            </form>
        </div>
    </div>
</body>
</html>

