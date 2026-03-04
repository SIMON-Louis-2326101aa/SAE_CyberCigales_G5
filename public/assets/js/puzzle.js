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
    function showClue(element)
    {
        if (element) {
            element.classList.add("show");
        }
    }

    // Minuteur tab info : Révéler tab d'info
    if (infoTab) {
        setTimeout(function () {
            infoTab.classList.remove("disabled");
            console.log("L'onglet d'info est activer !"); // Pour le débogage
        }, delayTabInfo);
    }

    // Minuteur 1 : Révéler l'indice 1 après 0 minute
    if (clue1) {
        setTimeout(function () {
            showClue(clue1);
            console.log("Indice 1 révélé !"); // Pour le débogage
        }, delayClue1);
    }

    // Minuteur 2 : Révéler l'indice 2 après 15 minute
    if (clue2) {
        setTimeout(function () {
            showClue(clue2);
            console.log("Indice 2 révélé !"); // Pour le débogage
        }, delayClue2);
    }

    // Minuteur 3 : Révéler l'indice 3 après 30 minutes
    if (clue3) {
        setTimeout(function () {
            showClue(clue3);
            console.log("Indice 3 révélé !"); // Pour le débogage
        }, delayClue3);
    }

    // Chronomètre de jeu (Game Timer)
    const timeDisplay = document.getElementById("time-display");
    console.log(window.BASE_TIME, window.GAME_STATUS, window.LAST_START_TIME);


    if (timeDisplay && window.BASE_TIME !== undefined) {

        function updateTimer()
        {
            let elapsed = window.BASE_TIME;

            if (window.GAME_STATUS === "in_progress" && window.LAST_START_TIME) {
                elapsed += Math.floor((Date.now() - window.LAST_START_TIME) / 1000);
            }

            const h = Math.floor(elapsed / 3600);
            const m = Math.floor((elapsed % 3600) / 60);
            const s = elapsed % 60;

            timeDisplay.textContent =
                String(h).padStart(2, "0") + ":" +
                String(m).padStart(2, "0") + ":" +
                String(s).padStart(2, "0");
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    }

});

// ===== Carte photo retournable =====
document.addEventListener('DOMContentLoaded', () => {
    const card = document.getElementById('photoCard');

    if (!card) {
        return;
    }

    card.addEventListener('click', () => {
        card.classList.toggle('turn');
    });
});

// Vanilla, no framework — juste UX
const $ = s => document.querySelector(s);

const story = $("#story");
if (story) {
    const full = story.textContent;
    story.textContent = "";
    let i = 0, speed = 20;
    (function tick()
    {
        if (i <= full.length) {
            story.textContent = full.slice(0, i++);
            requestAnimationFrame(() => setTimeout(tick, speed));
        }
    })();
}

const butterfly = document.getElementById("ui-butterfly");
const intro = document.getElementById("intro-note");
if (butterfly) {
    butterfly.addEventListener("click", (e) => {
        e.preventDefault();
        if (intro) {
            intro.hidden = false;
        }
        window.location.href = "?m=papillon&a=epreuve";
    });
    butterfly.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault(); butterfly.click(); }
    });
}

// accessibilité
const h1 = document.querySelector("h1.heading");
if (h1) {
    h1.setAttribute("tabindex","-1"); h1.focus({preventScroll:false}); }

// reduce motion
if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    document.querySelectorAll(".wing-l,.wing-r,.float").forEach(el => el.style.animation = "none");
}

const hint = document.getElementById("hint");
if (hint) {
    const full = hint.textContent;
    hint.textContent = "";
    let i = 0, speed = 12;
    (function tick()
    {
        if (i <= full.length) {
            hint.textContent = full.slice(0, i++);
            requestAnimationFrame(() => setTimeout(tick, speed));
        }
    })();
}


// ===============================================
//                 PasswordGame
// ===============================================
document.addEventListener('DOMContentLoaded', () => {
    // On récupère les éléments HTML avec lesquels on va interagir
    const passwordInput = document.getElementById('passwordInput'); // Le champ où l'utilisateur tape son mot de passe
    const rulesList = document.getElementById('passwordRules'); // La liste (<ul>) où les règles s'affichent
    const submitButton = document.querySelector('#password-game-form button[type="submit"]'); // Le bouton pour valider

    // Si un des éléments n'existe pas, on arrête le script pour éviter des erreurs
    if (!passwordInput || !rulesList || !submitButton) {
        return;
    }

    // --- Données pour les règles dynamiques ---
    const sponsors = ['Bjorg', 'Bugatti', 'Kiri'];
    const days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
    const currentDay = days[(new Date().getDay() + 6) % 7]; // Doit faire + 6 puis modulo 7 car javascript a été créer par des américains qui pensent qu'ils sont le centre du monde et que la semaine commence le dimanche
    // --- Fonction utilitaire pour la Règle 9 (Correction de l'affichage) ---
    // Calcule la somme des valeurs des chiffres romains dans une chaîne de caractères.
    const getRomanSum = (pwd) => {
        const romanValues = {'I': 1, 'V': 5, 'X': 10, 'L': 50, 'C': 100, 'D': 500, 'M': 1000};
        let sum = 0;
        for (const char of pwd.toUpperCase()) {
            if (char in romanValues) {
                sum += romanValues[char];
            }
        }
        return sum;
    };

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
            text: 'Règle 7: Votre mot de passe doit inclure le nom d\'un de nos sponsors. <div class="sponsor-logos">' +
                '<img src="./assets/images/Logo_Bjorg.png" alt="Logo Bjorg" title="Bjorg">' +
                '<img src="./assets/images/Logo_Bugatti.png" alt="Logo Bugatti" title="Bugatti">' +
                '<img src="./assets/images/Logo_KIRI.png" alt="Logo KIRI" title="KIRI"></div>',
            validate: (pwd) => sponsors.some(sponsor => new RegExp(sponsor, 'i').test(pwd))
        },

        // Règle 8: Doit contenir le jour actuel
        {
            text: `Règle 8: Votre mot de passe doit contenir le jour actuel (${currentDay}).`,
            validate: (pwd) => new RegExp(currentDay, 'i').test(pwd)
        },

        // Règle 9: La somme des chiffres romains doit être 1729
        {
            // Le texte appelle la fonction getRomanSum pour un affichage toujours à jour.
            text: () => `Règle 9: La somme des valeurs de tous les chiffres romains (I,V,X,L,C,D,M) doit être égale à 1729. <br><small>Attention: Pas de combinaison, VI = 5 + 1</small> Somme actuelle: ${getRomanSum(passwordInput.value)}`,
            // La validation appelle aussi la même fonction pour une vérification toujours à jour.
            validate: (pwd) => getRomanSum(pwd) === 1729
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
            // Si rule.text est une fonction, on l'exécute pour obtenir le texte. Sinon, on utilise la innerHTML (à la place de textContent pour avoir des images).
            listItem.innerHTML = typeof rule.text === 'function' ? rule.text() : rule.text;

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

    // Ajout de l'écouteur sur le bouton valider pour aller dans une autre page
    const passwordGameForm = document.getElementById('passwordGameForm');
    if (passwordGameForm) {
        passwordGameForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Empêche le rechargement de la page
            // Vérifie si toutes les règles sont validées avant de rediriger
            if (!submitButton.disabled) {
                window.location.href = 'index.php?controller=Redirection&action=openSummaryClue';
            }
        });
    }

    // Affichage de la première règle
    validatePassword();
});

// ==========================================
//               ButterflyWay
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
    const root = document.querySelector("[data-bw-root]");
    if (!root) return;

    const maxSteps = 11;
    const path = ["L","R","L","L","R","R","L","R","L","L","B"];

    const team = (root.getAttribute("data-team") || "alice").toLowerCase();

    const storyHintsAlice = [
        "Un log ancien subsiste. Peu visible, mais intact.",
        "Une activité bruyante attire l’attention en surface.",
        "Des métadonnées oubliées tracent un chemin flou.",
        "Un signal faible persiste hors du flux principal.",
        "Un accès trop exposé clignote inutilement.",
        "Le silence apparent laisse transparaitre une trace plus discrète.",
        "Une archive n’a jamais été correctement effacée.",
        "Un trafic évident semble trop parfait.",
        "Une signature ancienne a été laissée volontairement.",
        "Les données se raréfient. Mais le sens est encore là.",
        "Le papillon ralentit. Il attend que tu realises ce que d’autres ont ignoré.",
    ];

    const storyHintsBob = [
        "Un point d’entrée discret semble moins exposé.",
        "Un accès public attire trop facilement.",
        "Une route secondaire contourne les contrôles visibles.",
        "Un bruit parasite cache un chemin plus sûr.",
        "Une interface trop lumineuse signale un risque.",
        "Un silence artificiel est rarement rassurant.",
        "Un accès interne n’a jamais été audité.",
        "Un flux évident ressemble à un leurre.",
        "Une validation ancienne n’a jamais été révoquée.",
        "À ce stade, la solution simple est la plus dangereuse.",
        "Le papillon change de logique. La sécurité exige parfois de reculer.",
    ];

    const lostMessages = [
        "Une alerte s’est déclenchée. Le signal a disparu.",
        "Trop direct. Le papillon s’est volatilisé.",
        "Une action brusque a effacé la trace.",
        "Avast a détécté une menace et t'a mis en quarantaine.",
        "Tu as attiré l’attention. Le système t'a redirigé.",
        "Erreur humaine détectée, tu a été déconnécté du système",
    ];

    const hints = (team === "bob") ? storyHintsBob : storyHintsAlice;

    const elHint = document.getElementById("bw-hint");
    const elFeedback = document.getElementById("bw-feedback");
    const elStep = document.getElementById("bw-step");
    const elMax = document.getElementById("bw-max");
    const elScore = document.getElementById("bw-score");
    const codeZone = document.getElementById("bw-code-zone");

    const key = `bw_state_${team}`; // state séparé par team (optionnel)
    const defaultState = { step: 0, score: 0, blocked: false, showCode: false };

    function loadState() {
        try {
            return JSON.parse(sessionStorage.getItem(key)) || { ...defaultState };
        } catch {
            return { ...defaultState };
        }
    }

    function saveState(st) {
        sessionStorage.setItem(key, JSON.stringify(st));
    }

    function randomLost() {
        return lostMessages[Math.floor(Math.random() * lostMessages.length)];
    }

    function render(st) {
        if (elMax) elMax.textContent = String(maxSteps);
        if (elStep) elStep.textContent = String(st.step);
        if (elScore) elScore.textContent = String(st.score);

        if (elHint) {
            elHint.textContent = (st.step < maxSteps)
                ? (hints[st.step] || "")
                : "Il semble que tu aie trouvé ce que tu cherchais.";
        }

        if (codeZone) {
            codeZone.style.display = st.showCode ? "" : "none";
        }
    }

    function move(dir) {
        const st = loadState();

        if (st.step >= maxSteps) return;

        if (st.blocked) {
            if (elFeedback) elFeedback.textContent = randomLost() + " (Reprends la trace.)";
            return;
        }

        dir = String(dir || "").toUpperCase();
        if (!["L","R","B"].includes(dir)) dir = "R";

        const expected = path[st.step] || "R";

        if (dir === expected) {
            st.score += 1;
            st.step += 1;

            if (elFeedback) elFeedback.textContent = "Le signal s'amplifie.";

            if (st.step >= maxSteps) {
                st.showCode = true;
                if (elFeedback) elFeedback.textContent = "Il ne reste plus qu’à valider l’accès.";
            }
        } else {
            st.score = -1;
            st.blocked = true;
            if (elFeedback) elFeedback.textContent = randomLost() + " (Signal bloqué.)";
        }

        saveState(st);
        render(st);
    }

    function turn() {
        // Si l’action attendue à l’étape actuelle est B, alors c’est un “bon move”
        const st = loadState();
        const expected = path[st.step] || "R";

        if (expected === "B") {
            move("B");
            return;
        }

        // Sinon retour au début
        st.step = 0;
        st.score = 0;
        st.blocked = false;
        st.showCode = false;

        if (elFeedback) elFeedback.textContent = "Tu te retournes… et tu reprends la piste depuis le début.";
        saveState(st);
        render(st);
    }

    document.querySelectorAll("[data-bw]").forEach(btn => {
        btn.addEventListener("click", () => {
            const d = btn.getAttribute("data-bw");
            if (d === "B") turn();
            else move(d);
        });
    });

    // init
    render(loadState());
});