document.addEventListener("DOMContentLoaded", function () {
    const openLetterBtn = document.getElementById("open-letter-btn");
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

// ===== Marque-page latéral (Info Tab) =====
document.addEventListener("DOMContentLoaded", function () {
    const infoTab = document.getElementById("info-tab");
    const tabHandle = document.getElementById("info-handle");

    if (infoTab && tabHandle) {
        tabHandle.addEventListener("click", function () {
            infoTab.classList.toggle("open");
        });
    }
});

// ===== Marque-page latéral (Clue Tab) ===== // 💡 NOUVEAU BLOC
document.addEventListener("DOMContentLoaded", function () {
    const clueTab = document.getElementById("clue-tab");
    const clueHandle = document.getElementById("clue-handle"); // 💡 Nouvel ID

    if (clueTab && clueHandle) {
        clueHandle.addEventListener("click", function () {
            clueTab.classList.toggle("open");
        });
    }
});
