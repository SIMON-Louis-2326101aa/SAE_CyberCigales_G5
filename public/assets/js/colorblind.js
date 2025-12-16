// --- Accessibilité pour les daltoniens ---
document.addEventListener('DOMContentLoaded', () => {
    const themeSelector = document.getElementById('theme-selector');
    const body = document.body;

    // Appliquer le thème sauvegardé au chargement de la page
    const savedTheme = localStorage.getItem('colorblind-theme') || 'default'; // Défaut si pas de thème
    body.setAttribute('data-theme', savedTheme);
    themeSelector.value = savedTheme;

    // Gérer le changement de sélection dans le menu déroulant
    themeSelector.addEventListener('change', () => {
        const selectedTheme = themeSelector.value;
        body.setAttribute('data-theme', selectedTheme);
        localStorage.setItem('colorblind-theme', selectedTheme);
    });
});
