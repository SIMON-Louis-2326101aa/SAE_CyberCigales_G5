document.addEventListener("DOMContentLoaded", function () {
    const openLetterBtn = document.getElementById("open-letter-btn");
    const letterContent = document.getElementById("letterContent"); // C'est maintenant l'élément pivotant
    const solutionLetter = document.getElementById("solutionLetter"); // Le conteneur de la solution

    // ===============================================
    // 1. Gestion de l'ouverture/fermeture (Bouton)
    // ===============================================
    if (openLetterBtn && letterContent && solutionLetter) {
        openLetterBtn.addEventListener("click", function () {
            // Révèle le message codé (qui est l'élément pivotant)
            letterContent.classList.toggle("open");
            // Révèle la zone de solution
            solutionLetter.classList.toggle("open");

            // Met à jour le texte du bouton
            if (letterContent.classList.contains("open")) {
                openLetterBtn.textContent = "Fermer la lettre";
            } else {
                openLetterBtn.textContent = "Ouvrir la lettre";
                // Assure que la carte revient à sa position initiale quand on la ferme
                letterContent.classList.remove("turn");
            }
        });
    }

    // ===============================================
    // 2. Gestion de la rotation (Clic sur le contenu)
    // ===============================================
    if (letterContent) {
        letterContent.addEventListener("click", function (event) {
            // Empêche la rotation si la carte est masquée (non 'open')
            if (letterContent.classList.contains("open")) {
                // Applique la classe 'turn' pour la rotation 3D
                letterContent.classList.toggle("turn");
            }
            // Empêche la propagation si l'on clique sur un élément interactif à l'intérieur
            event.stopPropagation();
        });
    }
});


// ===== Marque-page latéral (Info Tab) & (Clue Tab) =====
document.addEventListener("DOMContentLoaded", function () {
    // Info Tab
    const infoTab = document.getElementById("info-tab");
    const tabHandle = document.getElementById("info-handle");

    if (infoTab && tabHandle) {
        tabHandle.addEventListener("click", function () {
            // Si l'onglet est désactivé, on ne fait rien
            if (tabHandle.classList.contains("disabled")) {
                return;
            }

            infoTab.classList.toggle("open");
        });
    }

    // Clue Tab
    const clueTab = document.getElementById("clue-tab");
    const clueHandle = document.getElementById("clue-handle");
    if (clueTab && clueHandle) {
        clueHandle.addEventListener("click", function () {
            clueTab.classList.toggle("open");
        });
    }
});

// ===== Révélation des indices avec délai (setTimeout) & Chronomètre (pas de changement) =====
document.addEventListener("DOMContentLoaded", function () {
    // Délai en millisecondes avant l'apparition de chaque indice (exemple : 2 minutes = 120000 ms)
    const delayClue1 = 600000; // 10 minutes
    const delayClue2 = 900000; // 15 minute
    const delayClue3 = 1800000; // 30 minutes

    const delayTabInfo = 300000; // 5 minutes

    const clue1 = document.getElementById("clue-text-1");
    const clue2 = document.getElementById("clue-text-2");
    const clue3 = document.getElementById("clue-text-3");

    const infoTab = document.getElementById("info-handle");

    // Fonction pour afficher l'indice
    function showClue(element) {
        if (element) {
            element.classList.add("show");
        }
    }

    // Minuteur tab info : Révéler tab d'info
    if (infoTab) {
        setTimeout(function() {
            infoTab.classList.remove("disabled");
            console.log("L'onglet d'info est activer !"); // Pour le débogage
        }, delayTabInfo);
    }

    // Minuteur 1 : Révéler l'indice 1 après 0 minute
    if (clue1) {
        setTimeout(function() {
            showClue(clue1);
            console.log("Indice 1 révélé !"); // Pour le débogage
        }, delayClue1);
    }

    // Minuteur 2 : Révéler l'indice 2 après 15 minute
    if (clue2) {
        setTimeout(function() {
            showClue(clue2);
            console.log("Indice 2 révélé !"); // Pour le débogage
        }, delayClue2);
    }

    // Minuteur 3 : Révéler l'indice 3 après 30 minutes
    if (clue3) {
        setTimeout(function() {
            showClue(clue3);
            console.log("Indice 3 révélé !"); // Pour le débogage
        }, delayClue3);
    }

    // Chronomètre de jeu (Game Timer)
    const timeDisplay = document.getElementById("time-display");

    if (timeDisplay && typeof GAME_START_TIME !== "undefined") {

        function updateTimer() {
            // Heure actuelle
            const now = Date.now();

            // Différence en secondes
            // eslint-disable-next-line no-undef
            const diff = Math.floor((now - GAME_START_TIME) / 1000);

            // Conversion en h / min / sec
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;

            // Affichage formaté
            timeDisplay.textContent =
                String(hours).padStart(2, "0") + ":" +
                String(minutes).padStart(2, "0") + ":" +
                String(seconds).padStart(2, "0");
        }

        // Mise à jour immédiate
        updateTimer();

        // Puis toutes les secondes
        setInterval(updateTimer, 1000);
    }

});

// ===== Carte photo retournable =====
document.addEventListener('DOMContentLoaded', () => {
    const card = document.getElementById('photoCard');

    if (!card) return;

    card.addEventListener('click', () => {
        card.classList.toggle('turn');
    });
});

// ===============================================
//                  PasswordGame
// ===============================================
document.addEventListener('DOMContentLoaded', () => {
    // Sélection des éléments clés du DOM pour PasswordGame
    const passwordInput = document.getElementById('passwordInput'); // Champ de saisie du mot de passe
    const rulesList = document.getElementById('passwordRules');   // Liste des règles
    const submitButton = document.querySelector('#passwordGameForm button[type="submit"]'); // Bouton de validation

    // Si l'un des éléments nécessaires n'est pas trouvé, la fonction s'arrête
    if (!passwordInput || !rulesList || !submitButton) {
        return;
    }

    // Définition des règles PasswordGame
    // A chaque règle respectée, une nouvelle règle apparaît, en respectant la nouvelle et les précédentes
    const rules = [
        // Règle 1: Longueur minimale de 8 caractères
        { text: "Règle 1: Votre mot de passe doit contenir au moins 8 caractères.", validate: (pwd) => pwd.length >= 8 },
        // Règle 2: Au moins une majuscule
        { text: "Règle 2: Votre mot de passe doit contenir au moins une majuscule.", validate: (pwd) => /[A-Z]/.test(pwd) },
        // Règle 3: Au moins un chiffre
        { text: "Règle 3: Votre mot de passe doit contenir au moins un chiffre.", validate: (pwd) => /[0-9]/.test(pwd) },
        // Règle 4: Au moins un caractère spécial
        { text: "Règle 4: Votre mot de passe doit contenir au moins un caractère spécial (ex: !, @, #, $).", validate: (pwd) => /[^A-Za-z0-9]/.test(pwd) },
        // Règle 5: La somme des chiffres doit être égale à 25
        { text: "Règle 5: La somme des chiffres de votre mot de passe doit être égale à 25.", validate: (pwd) => {
            const digits = pwd.match(/\d/g); // Extrait tous les chiffres de la chaîne
            if (!digits) return false; // S'il n'y a pas de chiffres, la règle n'est pas respectée
            // Calcule la somme des chiffres
            const sum = digits.reduce((acc, digit) => acc + parseInt(digit, 10), 0);
            return sum === 25; // Retourne vrai si la somme est égale à 25
        }}
    ];

    // Pour valider le mot de passe et afficher les règles
    const validatePassword = () => {
        const password = passwordInput.value; // Récupère la valeur actuelle du mot de passe
        rulesList.innerHTML = ''; // Efface toutes les règles précédemment affichées
        let allRulesMet = true;   // Pour savoir si toutes les règles sont respectées

        // Parcourt chaque règle pour la valider et l'afficher
        for (let i = 0; i < rules.length; i++) {
            const rule = rules[i]; // Règle actuelle
            const listItem = document.createElement('li'); // Crée un élément de liste pour la règle
            listItem.textContent = rule.text; // Définit le texte de l'élément de liste

            // Vérifie si la règle actuelle est validée
            if (rule.validate(password)) {
                listItem.style.color = 'lightgreen'; // Si validée, couleur verte
                rulesList.appendChild(listItem); // Ajoute à la liste des règles
            } else {
                listItem.style.color = 'red'; // Si non validée, couleur rouge
                rulesList.appendChild(listItem); // Ajoute à la liste des règles
                allRulesMet = false; // Une règle n'est pas respectée
                // Arrête l'affichage des règles suivantes car la condition précédente n'est pas remplie
                break;
            }
        }
        
        // Active ou désactive le bouton de validation en fonction de la validation de toutes les règles
        submitButton.disabled = !allRulesMet;
    };

    // Permet d'"écouter" en direct le mot de passe écrit et appeler validatePassword
    passwordInput.addEventListener('input', validatePassword);

    // Affichage première règle
    validatePassword();
});
