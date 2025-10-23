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
