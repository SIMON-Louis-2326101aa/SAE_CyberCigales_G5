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
//                 PasswordGame
// ===============================================
document.addEventListener('DOMContentLoaded', () => {
    // On récupère les éléments HTML avec lesquels on va interagir
    const passwordInput = document.getElementById('passwordInput'); // Le champ où l'utilisateur tape son mot de passe
    const rulesList = document.getElementById('passwordRules'); // La liste (<ul>) où les règles s'affichent
    const submitButton = document.querySelector('#passwordGameForm button[type="submit"]'); // Le bouton pour valider

    // Si un des éléments n'existe pas, on arrête le script pour éviter des erreurs
    if (!passwordInput || !rulesList || !submitButton) {
        return;
    }

    // --- Données pour les règles dynamiques ---
    const sponsors = ['Bjorg', 'Bugatti', 'Kiri'];
    const days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
    const currentDay = days[(new Date().getDay() + 6) % 7]; // Doit faire + 6 puis modulo 7 car javascript a été créer par des américains qui pensent qu'ils sont le centre du monde et que la semaine commence le dimanche
    
    // Variable pour stocker la valeur du produit romain pour l'affichage
    let romanProductValue = 1;

    // Chaque règle est un objet avec un texte et une fonction de validation
    // La fonction de validation prend le mot de passe (pwd) et retourne `true` si la règle est respectée, sinon `false`
    const rules = [
        // Règle 1: Longueur minimale
        { text: "Règle 1: Votre mot de passe doit contenir au moins 8 caractères.", validate: (pwd) => pwd.length >= 8 },

        // Règle 2: Présence d'une majuscule
        { text: "Règle 2: Votre mot de passe doit contenir au moins une majuscule.", validate: (pwd) => /[A-Z]/.test(pwd) },

        // Règle 3: Présence d'un chiffre
        { text: "Règle 3: Votre mot de passe doit contenir au moins un chiffre.", validate: (pwd) => /[0-9]/.test(pwd) },

        // Règle 4: Présence d'un caractère spécial
        // /[^A-Za-z0-9]/ cherche un caractère qui n'est pas une lettre majuscule, minuscule ou un chiffre (à cause du ^)
        { text: "Règle 4: Votre mot de passe doit contenir au moins un caractère spécial (ex: !, @, #, $).", validate: (pwd) => /[^A-Za-z0-9]/.test(pwd) },

        // Règle 5: La somme des chiffres doit faire 25
        { text: "Règle 5: La somme des chiffres de votre mot de passe doit être égale à 25.", validate: (pwd) => {
                const digits = pwd.match(/\d/g);
                if (!digits) return false;
                const sum = digits.reduce((acc, digit) => acc + parseInt(digit, 10), 0);
                return sum === 25;
            }},

        // Règle 6: Doit contenir "biloute"
        { text: "Règle 6: Votre mot de passe doit contenir 'biloute'.", validate: (pwd) => /biloute/i.test(pwd) },

        // Règle 7: Doit contenir le nom d'un sponsor
        {
            text: 'Règle 7: Votre mot de passe doit inclure le nom d\'un de nos sponsors. <div class="sponsor-logos"><img src="./assets/images/Logo_bjorg.png" alt="Logo Bjorg" title="Bjorg"><img src="./assets/images/Logo_Bugatti.png" alt="Logo Bugatti" title="Bugatti"><img src="./assets/images/Logo_KIRI.png" alt="Logo KIRI" title="KIRI"></div>',
            validate: (pwd) => sponsors.some(sponsor => new RegExp(sponsor, 'i').test(pwd))
        },

        // Règle 8: Doit contenir le jour actuel
        {
            text: `Règle 8: Votre mot de passe doit contenir le jour actuel (${currentDay}).`,
            validate: (pwd) => new RegExp(currentDay, 'i').test(pwd)
        },

        // Règle 9: Le produit des chiffres romains doit être 100
        {
            text: "Règle 9: La somme des valeurs de tous les chiffres romains (I, V, X, L, C, D, M,) doit être égal à 1729." +
                " Attention: Pas de combinaison, VI != 7, mais VI = 5 + 1",
            validate: (pwd) => {
                const romanValues = {'I': 1, 'V': 5, 'X': 10, 'L': 50, 'C': 100, 'D': 500, 'M': 1000};
                let sumRoman = 0;
                for (const char of pwd.toUpperCase()) {
                    if (char in romanValues) {
                        sumRoman += romanValues[char];
                    }
                }
                return sumRoman === 1729;
            }
        }
    ];

    // Cette fonction est appelée à chaque fois que l'utilisateur modifie le mot de passe
    const validatePassword = () => {
        const password = passwordInput.value;
        rulesList.innerHTML = ''; // On vide la liste des règles pour la reconstruire
        let allRulesMet = true; // On suppose que tout est bon au début

        // On parcourt les règles une par une
        for (let i = 0; i < rules.length; i++) {
            const rule = rules[i];
            const listItem = document.createElement('li'); // Crée un élément de liste pour la règle
            // innerHTML à la place de textContent pour avoir des images
            listItem.innerHTML = rule.text;

            // Si la règle est respectée
            if (rule.validate(password)) {
                listItem.style.color = 'green'; // Affichage en vert
                rulesList.appendChild(listItem); // Ajout à la liste
            } else { // Si la règle n'est pas respectée
                listItem.style.color = 'red'; // Affichage en rouge
                rulesList.appendChild(listItem); // Ajout à la liste
                allRulesMet = false; // Toutes les règles ne sont pas respectées
                break; // On arrête de vérifier les autres règles, car il faut les valider dans l'ordre
            }
        }

        // Le bouton est désactivé (`disabled = true`)
        submitButton.disabled = !allRulesMet;
    };

    // Permet d'"écouter" en direct le mot de passe écrit et appeler validatePassword
    passwordInput.addEventListener('input', validatePassword);

    // Affichage de la première règle
    validatePassword();
});