// --- Dev Logger ---
function logConsole(message, type = 'info') {
    const icons = {
        ok: '✅',
        error: '❌',
        file: '📄',
        song: '🔊',
        info: 'ℹ️'
    };
    const prefix = icons[type] || '📄';
    console.log(`<-- ${prefix} [DEV LOG Script] ${message} -->`);
}

// Utilitaire: fait disparaître un élément avec une transition CSS
function hideWithFade(el, removeDelayMs = 600) {
    if (!el) {
        logConsole('Aucun élément à masquer.', 'error');
        return;
    }
    if (el.classList.contains('is-hiding')) {
        logConsole('L’élément est déjà en train de disparaître.', 'info');
        return;
    }

    el.classList.add('is-hiding');
    logConsole(`Début de disparition du flash (${removeDelayMs} ms)`, 'info');

    setTimeout(() => {
        if (el && el.parentNode) {
            el.parentNode.removeChild(el);
            logConsole('Flash supprimé du DOM', 'ok');
        } else {
            logConsole('Échec suppression du flash (déjà retiré ?)', 'error');
        }
    }, removeDelayMs);
}

// --- Initialise tous les messages flash sur la page ---
function setupFlashMessages(options = {}) {
    const {
        selector = '.flash',
        autoHideAfterMs = 5000, // durée du pop-up
        fadeDurationMs = 600,
        addCloseButton = true,
    } = options;

    const flashes = Array.from(document.querySelectorAll(selector));
    if (!flashes.length) {
        logConsole('Aucun message flash trouvé.', 'info');
        return;
    }

    logConsole(`Initialisation de ${flashes.length} flash(s) détecté(s)`, 'ok');

    flashes.forEach((flash, index) => {
        logConsole(`Flash #${index + 1} → "${flash.textContent.trim()}"`, 'file');

        // Accessibilité
        flash.setAttribute('role', 'alert');
        flash.setAttribute('aria-live', 'polite');

        // Ajout bouton de fermeture
        if (addCloseButton && !flash.querySelector('.flash__close')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'flash__close';
            btn.setAttribute('aria-label', 'Fermer le message');
            btn.textContent = '×';
            flash.appendChild(btn);

            btn.addEventListener('click', () => {
                logConsole(`Bouton fermé manuellement pour flash #${index + 1}`, 'info');
                hideWithFade(flash, fadeDurationMs);
            });
        }

        // Fermeture au clic sur le bloc
        flash.addEventListener('click', (e) => {
            if (e.target.closest('.flash__close')) return; // ignorer si bouton
            logConsole(`Clic direct sur flash #${index + 1}`, 'info');
            hideWithFade(flash, fadeDurationMs);
        });

        // Auto-hide après X secondes
        if (autoHideAfterMs > 0) {
            logConsole(
                `Timer auto-hide programmé (${autoHideAfterMs / 1000}s) pour flash #${index + 1}`,
                'info'
            );
            setTimeout(() => {
                hideWithFade(flash, fadeDurationMs);
                logConsole(`Auto-hide exécuté pour flash #${index + 1}`, 'ok');
            }, autoHideAfterMs);
        } else {
            logConsole(`Auto-hide désactivé pour flash #${index + 1}`, 'info');
        }
    });
}

// --- Ferme tous les flashs visibles avec la touche ESC ---
function setupGlobalEscToClose(selector = '.flash', fadeDurationMs = 600) {
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        const allFlashes = document.querySelectorAll(selector);
        if (!allFlashes.length) return;
        logConsole(`Touche Échap pressée → fermeture de ${allFlashes.length} flash(s)`, 'info');
        allFlashes.forEach((el) => hideWithFade(el, fadeDurationMs));
    });
}

// --- API Front: permet d’ajouter un flash dynamiquement ---
window.Flash = {
    init: setupFlashMessages,
    closeAll: () => {
        document.querySelectorAll('.flash').forEach((el) => hideWithFade(el));
        logConsole('Tous les flashs fermés via Flash.closeAll()', 'info');
    },
    create: (message, type = 'info', autoHideAfterMs = 5000) => {
        const flash = document.createElement('div');
        flash.className = `flash flash--${type}`;
        flash.textContent = message;
        document.body.appendChild(flash);
        logConsole(`Nouveau flash créé dynamiquement: "${message}"`, 'file');
        setupFlashMessages({ selector: '.flash', autoHideAfterMs });
    }
};

// --- Lancement automatique après chargement DOM ---
document.addEventListener('DOMContentLoaded', () => {
    logConsole('Initialisation du système de flashs...', 'info');
    setupFlashMessages({
        autoHideAfterMs: 10000, // durée du pop-up en ms
        fadeDurationMs: 600,
        addCloseButton: true,
    });
    setupGlobalEscToClose('.flash', 600);
    logConsole('Flash system prêt à l’utilisation.', 'ok');
});