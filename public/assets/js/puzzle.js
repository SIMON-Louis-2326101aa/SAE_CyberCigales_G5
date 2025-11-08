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

// ===== Révélation des indices avec délai (setTimeout) =====
document.addEventListener("DOMContentLoaded", function () {
    // Délai en millisecondes avant l'apparition de chaque indice (exemple : 2 minutes = 120000 ms)
    const delayClue1 = 600000; // 10 minutes
    const delayClue2 = 900000; // 15 minute
    const delayClue3 = 1800000; // 30 minutes

    const clue1 = document.getElementById("clue-text-1");
    const clue2 = document.getElementById("clue-text-2");
    const clue3 = document.getElementById("clue-text-3");

    // Fonction pour afficher l'indice
    function showClue(element) {
        if (element) {
            element.classList.add("show");
        }
    }

    // Minuteur 1 : Révéler l'indice 2 après 1 minute
    if (clue1) {
        setTimeout(function() {
            showClue(clue1);
            console.log("Indice 1 révélé !"); // Pour le débogage
        }, delayClue1);
    }

    // Minuteur 2 : Révéler l'indice 2 après 1 minute
    if (clue2) {
        setTimeout(function() {
            showClue(clue2);
            console.log("Indice 2 révélé !"); // Pour le débogage
        }, delayClue2);
    }

    // Minuteur 3 : Révéler l'indice 3 après 3 minutes
    // (Note : le minuteur démarre en même temps que la page, pas après l'indice 2)
    if (clue3) {
        setTimeout(function() {
            showClue(clue3);
            console.log("Indice 3 révélé !"); // Pour le débogage
        }, delayClue3);
    }

    // Si vous voulez un vrai chrono visible, vous pouvez utiliser setInterval et mettre à jour un élément HTML
});
