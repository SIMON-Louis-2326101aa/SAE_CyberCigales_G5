// Utilitaire: fait disparaître un élément avec une transition CSS
function hideWithFade(el, removeDelayMs = 600) {
    el.classList.add('is-hiding');

    setTimeout(() => {
        if (el && el.parentNode) {
            el.parentNode.removeChild(el);
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
        return;
    }

    flashes.forEach((flash) => {
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
                hideWithFade(flash, fadeDurationMs);
            });
        }

        // Fermeture au clic sur le bloc
        flash.addEventListener('click', (e) => {
            if (e.target.closest('.flash__close')) return; // ignorer si bouton
            hideWithFade(flash, fadeDurationMs);
        });

        // Auto-hide après X secondes
        if (autoHideAfterMs > 0) {
            setTimeout(() => {
                hideWithFade(flash, fadeDurationMs);
            }, autoHideAfterMs);
        }
    });
}

// --- Ferme tous les flashs visibles avec la touche ESC ---
function setupGlobalEscToClose(selector = '.flash', fadeDurationMs = 600) {
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        const allFlashes = document.querySelectorAll(selector);
        if (!allFlashes.length) return;
        allFlashes.forEach((el) => hideWithFade(el, fadeDurationMs));
    });
}

// --- API Front: permet d’ajouter un flash dynamiquement ---
window.Flash = {
    init: setupFlashMessages,
    closeAll: () => {
        document.querySelectorAll('.flash').forEach((el) => hideWithFade(el));
    },
    create: (message, type = 'info', autoHideAfterMs = 5000) => {
        const flash = document.createElement('div');
        flash.className = `flash flash--${type}`;
        flash.textContent = message;
        document.body.appendChild(flash);
        setupFlashMessages({ selector: '.flash', autoHideAfterMs });
    }
};

// --- Lancement automatique après chargement DOM ---
document.addEventListener('DOMContentLoaded', () => {
    setupFlashMessages({
        autoHideAfterMs: 10000, // durée du pop-up en ms
        fadeDurationMs: 600,
        addCloseButton: true,
    });
    setupGlobalEscToClose('.flash', 600);
});

// --- Changement de thème ---
document.addEventListener('DOMContentLoaded', () => {
    const themeButton = document.querySelector('#theme-changer');
    const body = document.body;

    // Chargement du theme depuis localStorage
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        body.classList.add(currentTheme);
    }

    themeButton.addEventListener('click', () => {
        body.classList.toggle('dark-theme');

        // Sauvegarde du theme dans localStorage
        if (body.classList.contains('dark-theme')) {
            localStorage.setItem('theme', 'dark-theme');
        } else {
            localStorage.removeItem('theme');
        }
    });
});

// --- Accessibilité pour les daltoniens & Menu Paramètres ---
document.addEventListener('DOMContentLoaded', () => {
    const settingsButton = document.getElementById('settings-button');
    const settingsMenu = document.getElementById('settings-menu');
    const themeButtonsContainer = document.getElementById('theme-selector-buttons');
    const body = document.body;

    if (!settingsButton || !settingsMenu || !themeButtonsContainer) {
        return; // Ne rien faire si les éléments ne sont pas trouvés
    }

    // --- Logique du menu déroulant ---
    settingsButton.addEventListener('click', (event) => {
        event.stopPropagation(); // Empêche le 'click' de se propager au document
        const isVisible = settingsMenu.classList.toggle('is-visible');
        settingsButton.setAttribute('aria-expanded', isVisible);
    });

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', (event) => {
        if (!settingsMenu.contains(event.target) && !settingsButton.contains(event.target) && settingsMenu.classList.contains('is-visible')) {
            settingsMenu.classList.remove('is-visible');
            settingsButton.setAttribute('aria-expanded', 'false');
        }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && settingsMenu.classList.contains('is-visible')) {
            settingsMenu.classList.remove('is-visible');
            settingsButton.setAttribute('aria-expanded', 'false');
        }
    });

    // --- Logique de changement de thème ---
    const applyTheme = (theme) => {
        body.setAttribute('data-theme', theme);
        localStorage.setItem('colorblind-theme', theme);

        // Mettre à jour le bouton actif
        const themeButtons = themeButtonsContainer.querySelectorAll('.theme-button');
        themeButtons.forEach(btn => {
            if (btn.dataset.theme === theme) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    };

    // Appliquer le thème sauvegardé au chargement de la page
    const savedTheme = localStorage.getItem('colorblind-theme') || 'default';
    applyTheme(savedTheme);

    // Gérer le clic sur les boutons de thème
    themeButtonsContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('theme-button')) {
            const selectedTheme = event.target.dataset.theme;
            applyTheme(selectedTheme);
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const burgerButton = document.getElementById('burger-button');
    const navRightSide = document.getElementById('nav-right-side');

    if (!burgerButton || !navRightSide) return;

    burgerButton.addEventListener('click', () => {
        const isOpen = navRightSide.classList.toggle('active');
        burgerButton.classList.toggle('active');

        burgerButton.setAttribute('aria-expanded', isOpen.toString());
    });
});
