<?php

/**
 * Vue Terminal - Émulateur de terminal Kitty, Bureau & Barre des tâches (Easter Egg)
 *
 * @author SAE CyberCigales G5
 */

$userLogin = 'cigale';
if (!empty($_SESSION['prenom'])) {
    $cleanPrenom = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$_SESSION['prenom']));
    if ($cleanPrenom !== '') {
        $userLogin = $cleanPrenom;
    }
}

// Détection du mode Easter Egg DinoTofu (uniquement si explicitement demandé via paramètre GET)
$easterMode = ($_GET['easter'] ?? '') === 'dinotofu' ? 'dinotofu' : null;
$isDinoTofu = ($easterMode === 'dinotofu');
?>

<main id="terminal-page" class="terminal-main-container" data-user-login="<?= htmlspecialchars($userLogin) ?>" data-easter="<?= $isDinoTofu ? 'dinotofu' : '' ?>">
    <script>
        document.body.dataset.userLogin = <?= json_encode($userLogin) ?>;
    </script>

    <!-- Espace de bureau (Workspace où la fenêtre peut flotter et se déplacer) -->
    <div class="desktop-workspace" id="desktop-workspace">
        <div class="terminal-window" id="terminal-window">
            <!-- Barre de titre style KDE / Windows (Déplaçable à la souris) -->
            <div class="terminal-titlebar" id="terminal-titlebar">
                <div class="titlebar-left">
                    <img src="./assets/images/CigTerm.svg" alt="CigTerm" class="terminal-app-icon" width="18" height="18">
                    <span class="terminal-title-text" id="terminal-title-text"><?= $isDinoTofu ? 'CigTerm [DinoTofu] — ' : 'CigTerm — ' ?><?= htmlspecialchars($userLogin) ?>@cyber: ~</span>
                </div>
                <div class="titlebar-controls">
                    <button type="button" class="ctrl-btn ctrl-min" id="terminal-btn-min" title="Réduire" aria-label="Réduire">
                        <span>&#x2500;</span>
                    </button>
                    <button type="button" class="ctrl-btn ctrl-max" id="terminal-btn-max" title="Agrandir / Restaurer" aria-label="Agrandir">
                        <span>&#x25A2;</span>
                    </button>
                    <button type="button" class="ctrl-btn ctrl-close" id="terminal-btn-close" title="Fermer" aria-label="Fermer">
                        <span>&#x2715;</span>
                    </button>
                </div>
            </div>

            <!-- Corps du terminal -->
            <div class="terminal-body" id="terminal-body">
                <div class="terminal-banner" id="terminal-banner">
                    <pre class="ascii-art<?= $isDinoTofu ? ' ascii-dinotofu' : '' ?>" id="terminal-ascii-art"><?php if (!$isDinoTofu): ?>
   ____      _                ____ _             _           
  / ___|   _| |__   ___ _ __./ ___(_) __ _  __ _| | ___  ___ 
 | |  | | | | '_ \ / _ \ '__| |   | |/ _` |/ _` | |/ _ \/ __|
 | |__| |_| | |_) |  __/ |  | |___| | (_| | (_| | |  __/\__ \
  \____\__, |_.__/ \___|_|   \____|_|\__, |\__,_|_|\___||___/
       |___/                         |___/                    
<?php endif; ?></pre>
                    <p class="banner-hint" id="terminal-banner-hint">
                        <?= $isDinoTofu ? '[Easter Egg : DinoTofu Edition] Système initialisé. Tapez <span class="cmd-highlight">help</span> pour l\'aide ou testez la commande <span class="cmd-highlight">echo "Hello World"</span>.' : 'Système initialisé. Tapez <span class="cmd-highlight">help</span> pour l\'aide ou testez la commande <span class="cmd-highlight">echo "Hello World"</span>.' ?>
                    </p>
                </div>

                <!-- Historique des commandes exécutées -->
                <div id="terminal-output"></div>

                <!-- Ligne de saisie active avec prompt Kali Linux (Double ligne) -->
                <div class="terminal-input-block" id="terminal-active-line">
                    <div class="kali-prompt-line1">
                        <span class="p-bracket">┌──(</span><span class="p-user"><?= htmlspecialchars($userLogin) ?></span><span class="p-symbol">㉿</span><span class="p-host">cyber</span><span class="p-bracket">)-[</span><span class="p-path">~</span><span class="p-bracket">]</span>
                    </div>
                    <div class="kali-prompt-line2">
                        <span class="p-arrow">└─$</span>
                        <input
                            type="text"
                            id="terminal-input"
                            class="terminal-input"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false"
                            autofocus
                        />
                    </div>
                </div>
            </div>

            <!-- Poignées de redimensionnement de la fenêtre (bords et coins) -->
            <div class="resize-handle resize-n" data-dir="n"></div>
            <div class="resize-handle resize-s" data-dir="s"></div>
            <div class="resize-handle resize-e" data-dir="e"></div>
            <div class="resize-handle resize-w" data-dir="w"></div>
            <div class="resize-handle resize-nw" data-dir="nw"></div>
            <div class="resize-handle resize-ne" data-dir="ne"></div>
            <div class="resize-handle resize-sw" data-dir="sw"></div>
            <div class="resize-handle resize-se" data-dir="se"></div>
        </div>
    </div>

    <!-- Barre des tâches complète de bureau (Style KDE / Windows) -->
    <div class="desktop-taskbar" id="desktop-taskbar" aria-label="Barre des tâches du bureau">
        <!-- Bouton Démarrer / Menu -->
        <button type="button" class="taskbar-start-btn" id="taskbar-start-btn" title="Menu CyberCigales">
            <span class="start-icon">㉿</span>
            <span class="start-label">CyberCigales</span>
        </button>

        <!-- Liste des applications en cours d'exécution -->
        <div class="taskbar-apps-container" id="taskbar-apps">
            <button type="button" class="taskbar-app-tab is-active" id="terminal-taskbar-item" title="<?= $isDinoTofu ? 'CigTerm [DinoTofu] — ' : 'CigTerm — ' ?><?= htmlspecialchars($userLogin) ?>@cyber (Terminal)">
                <img src="./assets/images/CigTerm.svg" alt="CigTerm" class="app-icon" width="16" height="16">
                <span class="app-label" id="terminal-taskbar-label"><?= $isDinoTofu ? 'CigTerm [DinoTofu]' : 'CigTerm — ' . htmlspecialchars($userLogin) . '@cyber: ~' ?></span>
                <span class="app-active-glow"></span>
            </button>

            <!-- Emplacement pour une future application -->
            <div class="taskbar-app-tab is-placeholder" title="Emplacement d'application disponible" aria-hidden="true">
                <span class="app-icon">📁</span>
                <span class="app-label">fichiers</span>
            </div>
        </div>


        <!-- Zone de notification / Horloge (Systray) -->
        <div class="taskbar-systray">
            <span class="tray-status" title="Système opérationnel"><span class="tray-dot"></span>En ligne</span>
            <span class="tray-clock" id="taskbar-clock">--:--:--</span>
        </div>
    </div>
</main>
