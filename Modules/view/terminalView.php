<?php

/**
 * Vue Terminal - Émulateur de terminal Kitty & Prompt Kali Linux (Easter Egg)
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
?>

<main id="terminal-page" class="terminal-main-container" data-user-login="<?= htmlspecialchars($userLogin) ?>">
    <script>
        document.body.dataset.userLogin = <?= json_encode($userLogin) ?>;
    </script>
    <div class="terminal-window" id="terminal-window">
        <!-- Barre de titre style KDE / Windows (Contrôles à droite, sans style Apple) -->
        <div class="terminal-titlebar" id="terminal-titlebar">
            <div class="titlebar-left">
                <span class="terminal-app-icon">⌨</span>
                <span class="terminal-title-text">kitty — <?= htmlspecialchars($userLogin) ?>@cyber: ~</span>
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
            <div class="terminal-banner">
                <pre class="ascii-art">
   ____      _               ____ _             _           
  / ___|   _| |__   ___ _ __/ ___(_) __ _  __ _| | ___  ___ 
 | |  | | | | '_ \ / _ \ '__| |   | |/ _` |/ _` | |/ _ \/ __|
 | |__| |_| | |_) |  __/ |  | |___| | (_| | (_| | |  __/\__ \
  \____\__, |_.__/ \___|_|   \____|_|\__, |\__,_|_|\___||___/
       |___/                         |___/                    
                </pre>
                <p class="banner-welcome">CyberCigales Terminal [Émulateur interactif v1.0]</p>
                <p class="banner-hint">
                    Système initialisé. Tapez <span class="cmd-highlight">help</span> pour l'aide ou testez la commande <span class="cmd-highlight">echo "Hello World"</span>.
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
    </div>

    <!-- Bouton de la barre des tâches quand le terminal est réduit (Style KDE / Windows) -->
    <button type="button" id="terminal-taskbar-item" class="terminal-taskbar-item" title="Restaurer le terminal Kitty" aria-label="Restaurer le terminal Kitty">
        <span class="taskbar-icon">⌨</span>
        <span class="taskbar-text">kitty — <?= htmlspecialchars($userLogin) ?>@cyber: ~</span>
        <span class="taskbar-status-dot"></span>
    </button>
</main>

