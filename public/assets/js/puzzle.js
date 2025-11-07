document.addEventListener("DOMContentLoaded", function () {
    const openLetterBtn = document.getElementById("openLetterBtn");
    const letterContent = document.getElementById("letterContent");

    if (openLetterBtn && letterContent) {
        openLetterBtn.addEventListener("click", function () {
            letterContent.classList.toggle("open");

            // Change le texte du bouton selon l'état
            if (letterContent.classList.contains("open")) {
                openLetterBtn.textContent = "Fermer la lettre";
            } else {
                openLetterBtn.textContent = "Ouvrir la lettre";
            }
        });
    }
});
