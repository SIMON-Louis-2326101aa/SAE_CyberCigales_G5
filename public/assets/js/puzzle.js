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

    const inventoryTab = document.getElementById("inventory-tab");
    const inventoryHandle = document.getElementById("inventory-handle");
    if (inventoryTab && inventoryHandle) {
        inventoryHandle.addEventListener("click", function () {
            inventoryTab.classList.toggle("open");
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    /* global ENIGME_START */

    const clues = [
        { delay: 300000, text: "clue-text-1", time: "clue-time-1" },
        { delay: 600000, text: "clue-text-2", time: "clue-time-2" },
        { delay: 900000, text: "clue-text-3", time: "clue-time-3" }
    ];

    clues.forEach(clue => startClueTimer(clue.delay, clue.text, clue.time));

    // ===== Chrono pour l'onglet info =====
    function startInfoTimer(delay)
    {
        const infoTab = document.getElementById("info-handle");
        const infoChrono = document.getElementById("info-chrono");

        if (!infoTab || !infoChrono) return;

        const timer = setInterval(function(){

            const now = Date.now();
            let remaining = Math.floor((delay - (now - ENIGME_START)) / 1000);

            if (remaining <= 0)
            {
                clearInterval(timer);
                infoChrono.textContent = "00:00";
                infoTab.classList.remove("disabled");
                return;
            }

            let minutes = Math.floor(remaining / 60);
            let seconds = remaining % 60;

            minutes = String(minutes).padStart(2,"0");
            seconds = String(seconds).padStart(2,"0");

            infoChrono.textContent = minutes + ":" + seconds;

        },1000);
    }
    startInfoTimer(60000);


    // ===== Indices =====
    function startClueTimer(delay, textId, timeId)
    {
        const clueText = document.getElementById(textId);
        const clueTime = document.getElementById(timeId);

        if (!clueText || !clueTime) return;

        const timer = setInterval(function(){

            const now = Date.now();
            let remaining = Math.floor((delay - (now - ENIGME_START)) / 1000);

            if (remaining <= 0)
            {
                clearInterval(timer);
                clueText.classList.add("show");
                return;
            }

            let minutes = Math.floor(remaining / 60);
            let seconds = remaining % 60;

            minutes = String(minutes).padStart(2,"0");
            seconds = String(seconds).padStart(2,"0");

            clueTime.textContent = minutes + ":" + seconds;

        },1000);
    }

    // Chronomètre de jeu (Game Timer)
    const timeDisplay = document.getElementById("time-display");


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
// La rotation est gérée uniquement dans checkWin() après résolution du puzzle

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

document.addEventListener("DOMContentLoaded", function () {

    const photoPuzzle = document.getElementById("photoPuzzle");
    const photoCard = document.getElementById("photoCard");

    if (!photoPuzzle || !photoCard) return;


    const size = 4;
    const total = size * size;
    const imageUrl = "./assets/images/photoFamilleFlou.png";

    let pieces = [];
    let order = [...Array(total).keys()];
    let shuffled = [...order].sort(() => Math.random() - 0.5);

    const alreadySolved = localStorage.getItem("enigme2_photo_win");

    if (alreadySolved === "true") {

        // Affiche directement l'image complète
        photoPuzzle.innerHTML = "";
        photoPuzzle.style.width = "400px";
        photoPuzzle.style.height = "400px";
        photoPuzzle.style.backgroundImage = "url('./assets/images/photoFamilleFlou.png')";
        photoPuzzle.style.backgroundSize = "cover";
        photoPuzzle.style.backgroundPosition = "center";


        const card = document.getElementById("photoCard");

        card.addEventListener("click", () => {
            card.classList.toggle("turn");
        });

        return;
    }


    photoPuzzle.style.display = "grid";
    photoPuzzle.style.gridTemplateColumns = `repeat(${size}, 1fr)`;
    photoPuzzle.style.width = "400px";
    photoPuzzle.style.height = "400px";
    photoPuzzle.style.gap = "0";

    shuffled.forEach((value) => {

        let piece = document.createElement("div");
        piece.classList.add("piece");
        piece.draggable = true;
        piece.dataset.correct = value;
        piece.style.border = "none"; // supprime les bordures qui créent les espaces blancs

        piece.style.backgroundImage = `url(${imageUrl})`;
        piece.style.backgroundSize = "400px 400px";

        let x = (value % size) * -100;
        let y = Math.floor(value / size) * -100;
        piece.style.backgroundPosition = `${x}px ${y}px`;

        piece.addEventListener("dragstart", dragStart);
        piece.addEventListener("dragover", dragOver);
        piece.addEventListener("drop", drop);

        photoPuzzle.appendChild(piece);
        pieces.push(piece);
    });

    let dragged = null;

    function dragStart() {
        dragged = this;
    }

    function dragOver(e) {
        e.preventDefault();
    }

    function drop(e) {
        e.preventDefault();

        if (dragged === this) return;

        let tempBg = this.style.backgroundPosition;
        this.style.backgroundPosition = dragged.style.backgroundPosition;
        dragged.style.backgroundPosition = tempBg;

        let tempCorrect = this.dataset.correct;
        this.dataset.correct = dragged.dataset.correct;
        dragged.dataset.correct = tempCorrect;

        checkWin();
    }

    function checkWin() {

        let win = true;

        pieces.forEach((piece, index) => {
            if (parseInt(piece.dataset.correct) !== index) {
                win = false;
            }
        });

        if (win) {

            // Sauvegarde l'état
            localStorage.setItem("enigme2_photo_win", "true");

            // Bloque les pièces
            pieces.forEach(piece => {
                piece.draggable = false;
                piece.style.cursor = "default";
            });

            // Active la rotation sur la carte complète
            const card = document.getElementById("photoCard");

            card.addEventListener("click", () => {
                card.classList.toggle("turn");
            });

        }
    }

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
            if (st.showCode) {
                codeZone.style.display = "block";
                codeZone.classList.add("is-visible");
            } else {
                codeZone.style.display = "none";
                codeZone.classList.remove("is-visible");
            }
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
        const st = loadState();
        const expected = path[st.step] || "R";

        // Si on est bloqué (erreur commise) ou qu'on a déjà fini (11/11)
        // Cliquer au milieu réinitialise tout
        if (st.blocked || st.step >= maxSteps) {
            st.step = 0;
            st.score = 0;
            st.blocked = false;
            st.showCode = false;

            if (elFeedback) elFeedback.textContent = "Tu te retournes… et tu reprends la piste depuis le début.";
            saveState(st);
            render(st);
            return;
        }

        // Si on est à l'étape 10 sans être bloqué et que B est attendu, on finit
        if (st.step === 10 && expected === "B") {
            move("B");
            return;
        }

        // Par défaut, le bouton central réinitialise (comportement normal du labyrinthe)
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
    const sponsors = ['Samsung', 'Mitsubishi', 'Lamborghini'];
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
        { text: "Règle 1: Votre mot de passe doit contenir au moins 12 caractères.", validate: (pwd) => pwd.length >= 12 },

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

        // Règle 6: Doit contenir "biloute" et "cigales"
        { text: "Règle 6: Votre mot de passe doit contenir les mots 'biloute' et 'cigales'.", validate: (pwd) => /(?=.*biloute)(?=.*cigales)/i.test(pwd) },

        // Règle 7: Doit contenir le nom d'un sponsor
        {
            text: 'Règle 7: Votre mot de passe doit inclure le nom d\'un de nos sponsors. <div class="sponsor-logos">' +
                '<img src="./assets/images/Logo_Samsung.png" alt="Logo Samsung" title="Samsung">' +
                '<img src="./assets/images/Logo_Mitsubishi.png" alt="Logo Mitsubishi" title="Mitsubishi">' +
                '<img src="./assets/images/Logo_Lamborghini.png" alt="Logo Lamborghini" title="Lamborghini"></div>',
            validate: (pwd) => sponsors.some(sponsor => new RegExp(sponsor, 'i').test(pwd))
        },

        // Règle 8: Doit contenir le jour actuel
        {
            text: `Règle 8: Votre mot de passe doit contenir le jour actuel (${currentDay}).`,
            validate: (pwd) => new RegExp(currentDay, 'i').test(pwd)
        },

        // Règle 9: La somme des chiffres romains doit être 3729
        {
            // Le texte appelle la fonction getRomanSum pour un affichage toujours à jour.
            text: () => `Règle 9: La somme des valeurs de tous les chiffres romains (I,V,X,L,C,D,M) doit être égale à 3729. <br><small>Attention: Pas de combinaison, IV = 1 + 5 ≠ 4</small> Somme actuelle: ${getRomanSum(passwordInput.value)}`,
            // La validation appelle aussi la même fonction pour une vérification toujours à jour.
            validate: (pwd) => getRomanSum(pwd) === 3729
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

            // On récupère le texte de la règle (fonction ou chaîne)
            let ruleText = typeof rule.text === 'function' ? rule.text() : rule.text;

            // Si la règle est respectée
            if (rule.validate(password)) {
                listItem.style.color = '#00FF00'; // Affichage en vert clair
                listItem.innerHTML = "✅ " + ruleText; // Ajout de l'emoji succès pour plus de visibilité
                rulesList.appendChild(listItem); // Ajout à la liste
            } else { // Si la règle n'est pas respectée
                listItem.style.color = 'red'; // Affichage en rouge
                listItem.innerHTML = "❌ " + ruleText; // Ajout de l'emoji échec pour voir les règles pas encore validées
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
    const passwordGameForm = document.getElementById('password-game-form');
    const successBlock = document.getElementById('block-message-good-pwd');

    if (passwordGameForm) {
        passwordGameForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Empêche le rechargement de la page

            // Si toutes les règles sont validées, on affiche le bloc de succès au lieu de rediriger
            if (!submitButton.disabled) {
                if (successBlock) {
                    // On affiche le mot de passe final
                    const finalDisplay = document.getElementById('final-password-display');
                    if (finalDisplay) {
                        finalDisplay.textContent = passwordInput.value;
                    }

                    successBlock.style.display = 'block'; // Affiche l'explication et le bouton suivant
                    submitButton.style.display = 'none';  // Cache le bouton de validation original
                    passwordInput.disabled = true;        // Désactive l'input pour garder l'état
                }
            }
        });
    }

    // Affichage de la première règle
    validatePassword();
});


// ===============================================
//                 Phishing Mail
// ===============================================

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('phishing-container');
    if (!container) return;

    const emailItems = container.querySelectorAll('.email-item-logic');
    const displayArea = container.querySelector('#email-display-area');
    const pdfSimu = container.querySelector('#pdf-simulation');
    const validationSection = container.querySelector('#validation-section');

    if (!emailItems.length || !displayArea) return;

    // Contenus des emails
    const mailData = {
        1: {
            from: "service-client@inf0-impots.gouv.fr",
            subject: "Remboursement de trop-perçu",
            body: "<p>Cher(e) contribuable,</p><p>Après examen de votre dossier fiscal, un remboursement de 450,20€ est en votre faveur.</p><p>Veuillez confirmer vos coordonnées sur notre portail sécurisé : <br><a href='index.php?controller=Puzzle&action=openImpotsPhishing'>http://impots-gouv-remboursement-virement.net/ref45</a></p><p>Cordialement,<br>L'administration fiscale.</p>"
        },
        2: {
            from: "secure-check@faceb00k.security.com",
            subject: "Alerte de sécurité importante",
            body: "<p>Bonjour,</p><p>Une tentative de connexion suspecte a été détectée depuis Singapour. Si vous n'êtes pas à l'origine de cette action, sécurisez votre compte immédiatement.</p><div class='mail-button-container'><a href='index.php?controller=Puzzle&action=openFacebookPhishing' class='btn-nav btn-mail-action'>SÉCURISER MON COMPTE</a></div><p>L'équipe de sécurité.</p>"
        },
        3: {
            from: "archives.departementales@hauts-de-seine.fr",
            subject: "Votre demande d'acte n°7845",
            body: `<p>Bonjour,</p><p>Faisant suite à votre demande, veuillez trouver ci-joint l'acte de naissance demandé.</p>
                   <div class="attachment-logic" id="trigger-pdf">
                       <div class="attachment-icon-simu"></div>
                       <div class="attachment-info">
                           <strong>acte_de_naissance_7845.pdf</strong>
                           <span class="attachment-action-text">Cliquer pour visualiser</span>
                       </div>
                   </div>
                   <p>Cordialement,<br>Le service des archives.</p>`
        },
        4: {
            from: "contact@genealogie-direct-infos.com",
            subject: "Nouvelle découverte dans votre arbre !",
            body: `<p>Bonjour,</p><p>Notre algorithme a détecté un nouvel acte concernant la famille <strong>VALMONT</strong> qui pourrait vous intéresser.</p>
                   <div class="attachment-logic" id="trigger-fake-pdf">
                       <div class="attachment-icon-simu"></div>
                       <div class="attachment-info">
                           <strong>acte_valmont_inedit.pdf</strong>
                           <span class="attachment-action-text">Cliquer pour visualiser</span>
                       </div>
                   </div>
                   <p>Pour visualiser ce document inédit, veuillez régulariser votre abonnement annuel (19,99€) en cliquant sur le <a href='index.php?controller=Puzzle&action=openGenealogiePhishing'>lien sécurisé</a>.</p>
                   <p>L'équipe Généalogie Direct.</p>`
        },
        5: {
            from: "info@lapostee-suivi.fr",
            subject: "Votre colis n°8L9452 est bloqué",
            body: "<p>Bonjour,</p><p>Votre colis n'a pas pu être livré car il manque un affranchissement de 1,99€.</p><p>Pour programmer une nouvelle livraison, veuillez régulariser les frais de port via le lien ci-dessous :</p><p><a href='index.php?controller=Puzzle&action=openColisPhishing'>https://lapostee.fr/suivi/paiement-frais-douane</a></p><p>Attention, sans action de votre part sous 48h, le colis sera renvoyé à l'expéditeur.</p>"
        },
        6: {
            from: "nepasrepondre@videocloud-share.com",
            subject: "Vous avez reçu une vidéo !",
            body: "<p>Bonjour,</p><p>Un de vos contacts vous a partagé une vidéo privée via notre plateforme sécurisée.</p><p>Pour la visionner, cliquez sur le bouton ci-dessous :</p><div class='mail-button-container'><a href='index.php?controller=Puzzle&action=openVideoPhishing' class='btn-nav btn-mail-action'>VISIONNER LA VIDÉO</a></div>"
        }
    };

    // Récupération des paramètres d'initialisation (utile après un rechargement de page)
    const team = (container.getAttribute('data-team') || 'alice').toLowerCase();
    const initialMailId = container.getAttribute('data-open-mail');
    const initialOpenPdf = container.getAttribute('data-open-pdf') === '1';

    // Détermination du contenu selon l'équipe
    const targetName = (team === 'alice') ? 'Clara VALMONT' : 'Diane VALMONT';
    const motherName = (team === 'alice') ? 'Diane VALMONT' : 'Clara VALMONT';
    const gpsCoord = (team === 'alice') ? 'D' : '9';


    // Affiche le contenu de l'acte de naissance (simule l'ouverture d'un PDF)
    function showPdf() {
        if (!pdfSimu) return;
        pdfSimu.innerHTML = `
            <div class="pdf-header-border">
                <h2 class="pdf-title">EXTRAIT D'ACTE DE NAISSANCE</h2>
                <p class="pdf-subtitle">Commune de Boulogne-Billancourt</p>
            </div>
            <div class="pdf-body-content">
                <p>Le <strong>18 mars 1978</strong>, est née :</p>
                <h3 class="pdf-person-name">${targetName}</h3>
                <p>Fille de Pierre VALMONT et de Suzanne LECLERC. Soeur de ${motherName}.</p>
                <div class="pdf-handwritten">
                    <span class="handwritten-label">Note manuscrite :</span>
                    <strong>${gpsCoord}</strong>
                </div>
            </div>
        `;
        pdfSimu.classList.add('show');
        if (validationSection) validationSection.classList.remove('hidden');
    }

    // Affiche un faux document (Phishing)
    function showFakePdf() {
        if (!pdfSimu) return;
        pdfSimu.innerHTML = `
            <div class="pdf-header-border" id="pdf-phishing-header-border">
                <h2 class="pdf-title">DOCUMENT ARCHIVÉ - APERÇU</h2>
                <p class="pdf-subtitle">Service de généalogie privée</p>
            </div>
            <div class="pdf-body-content">
                <p>Extrait partiel concernant :</p>
                <h3 class="pdf-person-name">${motherName}</h3>
                <p>Née le 15 août 1975 à Paris.</p>
                <p><em>Le reste du document est masqué. Pour lever le filigrane et accéder aux mentions marginales, veuillez valider votre paiement.</em></p>
                <div class="pdf-subscription">
                    ABONNEMENT REQUIS
                </div>
            </div>
        `;
        pdfSimu.classList.add('show');
        if (validationSection) validationSection.classList.add('hidden');
    }

    // Gère l'affichage d'un email spécifique
    function selectMail(id) {
        const item = Array.from(emailItems).find(el => el.getAttribute('data-id') === id);
        if (!item) return;

        // Mise à jour visuelle de la sélection dans la liste
        emailItems.forEach(el => el.classList.remove('active'));
        item.classList.add('active');

        const data = mailData[id];

        // Rendu du contenu du mail
        displayArea.innerHTML = `
            <div class="mail-detail-meta">
                <strong>De :</strong> ${data.from}<br>
                <strong>Objet :</strong> ${data.subject}
            </div>
            <div class="mail-message-body">
                ${data.body}
            </div>
        `;

        // On enlève l'affichage de pdf quand on clique sur un autre mail
        if (pdfSimu) {
            pdfSimu.innerHTML = '';
            pdfSimu.classList.remove('show');
        }
        if (validationSection) {
            validationSection.classList.add('hidden');
        }

        // Gestion du clic sur la pièce jointe (uniquement pour le mail n°3)
        if (id === "3") {
            const trigger = document.getElementById('trigger-pdf');
            if (trigger) {
                trigger.onclick = () => {
                    showPdf();
                };
            }
        }

        // Gestion du clic sur la pièce jointe factice (mail n°4)
        if (id === "4") {
            const triggerFake = document.getElementById('trigger-fake-pdf');
            if (triggerFake) {
                triggerFake.onclick = () => {
                    showFakePdf();
                };
            }
        }
    }

    // Assignation des événements de clic sur les mails de la sidebar
    emailItems.forEach(item => {
        item.addEventListener('click', () => {
            selectMail(item.getAttribute('data-id'));
        });
    });

    // Restauration automatique de l'état (si session PHP active après erreur)
    if (initialMailId) {
        selectMail(initialMailId);
        if (initialOpenPdf) {
            showPdf();
        }
    }
});

// Gestion de la vidéo de phishing (Déblocage et lecture)
document.addEventListener('DOMContentLoaded', () => {
    const unlockBtn = document.getElementById('unlock-video-btn');
    const trapVideo = document.getElementById('trap-video');
    const unlockOverlay = document.getElementById('unlock-overlay');
    const epilepsyWarning = document.getElementById('epilepsy-warning');

    if (unlockBtn && trapVideo) {
        unlockBtn.addEventListener('click', () => {
            // Cache l'overlay initial
            if (unlockOverlay) unlockOverlay.style.display = 'none';

            // Affiche l'avertissement d'épilepsie
            if (epilepsyWarning) epilepsyWarning.classList.remove('hidden');

            // Attend 2 secondes avant de lancer la vidéo
            setTimeout(() => {
                if (epilepsyWarning) epilepsyWarning.classList.add('hidden');
                trapVideo.classList.remove('video-hidden');
                trapVideo.play();
            }, 2000);
        });
    }
});

// Fonction pour remplir automatiquement le formulaire des impôts avec les données de l'équipe
function autoFillImpots() {
    const container = document.querySelector('.impots-container');
    const team = container ? container.getAttribute('data-team') : 'alice';

    const nom = document.getElementById('nom');
    const birth = document.getElementById('birth');
    const address = document.getElementById('address');
    const tel = document.getElementById('tel');
    const card = document.getElementById('card');

    const fillBtn = document.getElementById('fill-btn');
    const submitBtn = document.getElementById('submit-btn');

    // Données de test selon l'équipe choisie
    if (team === 'alice') {
        if (nom) nom.value = "Clara Valmont";
        if (birth) birth.value = "1978-03-18";
        if (address) address.value = "1 Rue du Papillon, Boulogne-Billancourt";
        if (tel) tel.value = "06 12 88 44 22";
        if (card) card.value = "4532 0123 4567 8901";
    } else {
        if (nom) nom.value = "Diane Valmont";
        if (birth) birth.value = "1978-03-18";
        if (address) address.value = "5 Rue de la mémoire, Boulogne-Billancourt";
        if (tel) tel.value = "07 45 66 77 88";
        if (card) card.value = "5105 9876 5432 1098";
    }

    // On cache le bouton de remplissage et on montre le bouton de validation
    if (fillBtn) fillBtn.classList.add('hidden');
    if (submitBtn) submitBtn.classList.remove('hidden');
}

// Fonction pour afficher l'avertissement de phishing sur la page des impôts
function showWarningPhishingImpots() {
    const form = document.getElementById('phishing-form');
    const scammerMsg = document.getElementById('scammer-msg');
    const warningMsg = document.getElementById('warning-msg');
    const returnBtn = document.getElementById('return-btn');
    const submitBtn = document.getElementById('submit-btn');
    const intro = document.getElementById('impots-intro');

    // On cache le formulaire, le bouton valider et l'introduction
    if (form) form.style.display = 'none';
    if (submitBtn) submitBtn.style.display = 'none';
    if (intro) intro.style.display = 'none';

    // On affiche le message méchant des arnaqueurs
    if (scammerMsg) {
        scammerMsg.style.display = 'block';
        scammerMsg.scrollIntoView({ behavior: 'smooth' });
    }

    // Après 7 secondes, on remplace par le message explicatif et le bouton retour
    setTimeout(() => {
        if (scammerMsg) scammerMsg.style.display = 'none';
        if (warningMsg) warningMsg.style.display = 'block';
        if (returnBtn) returnBtn.style.display = 'block';

        if (warningMsg) warningMsg.scrollIntoView({ behavior: 'smooth' });
    }, 7000);
}

// Fonction pour remplir automatiquement le formulaire Facebook
function autoFillFacebook() {
    const container = document.querySelector('.facebook-container');
    const team = container ? container.getAttribute('data-team') : 'alice';

    const email = document.getElementById('fb-email');
    const pass = document.getElementById('fb-pass');

    const fillBtn = document.getElementById('fb-fill-btn');
    const loginBtn = document.getElementById('fb-login-btn');

    // Remplissage avec les identifiants probables des personnages
    if (team === 'alice') {
        if (email) email.value = "clara.valmont@email.fr";
        if (pass) pass.value = "SecretPapillon95";
    } else {
        if (email) email.value = "diane.valmont@email.fr";
        if (pass) pass.value = "MemoireVive92";
    }

    if (fillBtn) fillBtn.classList.add('hidden');
    if (loginBtn) loginBtn.classList.remove('hidden');
}

// Fonction pour afficher l'avertissement de phishing Facebook
function showWarningPhishingFacebook() {
    const loginCard = document.getElementById('fb-login-card');
    const warningMsg = document.getElementById('fb-warning-msg');
    const returnBtn = document.getElementById('fb-return-btn');

    if (loginCard) loginCard.style.display = 'none';
    if (warningMsg) warningMsg.style.display = 'block';
    if (returnBtn) returnBtn.style.display = 'block';
}

// Fonction pour remplir automatiquement le formulaire Généalogie
function autoFillGenealogie() {
    const container = document.querySelector('.genealogie-container');
    const team = container ? container.getAttribute('data-team') : 'alice';

    const nom = document.getElementById('gen-nom');
    const email = document.getElementById('gen-email');
    const card = document.getElementById('gen-card');

    const fillBtn = document.getElementById('gen-fill-btn');
    const submitBtn = document.getElementById('gen-submit-btn');

    // Données
    if (team === 'alice') {
        if (nom) nom.value = "Clara Valmont";
        if (email) email.value = "clara.valmont@email.fr";
        if (card) card.value = "4532 0123 4567 8901";
    } else {
        if (nom) nom.value = "Diane Valmont";
        if (email) email.value = "diane.valmont@email.fr";
        if (card) card.value = "5105 9876 5432 1098";
    }

    if (fillBtn) fillBtn.classList.add('hidden');
    if (submitBtn) submitBtn.classList.remove('hidden');
}

// Fonction pour afficher l'avertissement de phishing Généalogie
function showWarningPhishingGenealogie() {
    const cardBox = document.getElementById('gen-card-box');
    const warningMsg = document.getElementById('gen-warning-msg');
    const returnBtn = document.getElementById('gen-return-btn');

    if (cardBox) cardBox.style.display = 'none';
    if (warningMsg) warningMsg.style.display = 'block';
    if (returnBtn) returnBtn.style.display = 'block';
}

// Fonction pour remplir automatiquement le formulaire Colis (La Poste)
function autoFillColis() {
    const container = document.querySelector('.colis-container');
    const team = container ? container.getAttribute('data-team') : 'alice';

    const email = document.getElementById('colis-email');
    const pass = document.getElementById('colis-pass');

    const fillBtn = document.getElementById('colis-fill-btn');
    const loginBtn = document.getElementById('colis-login-btn');

    // Identifiants cohérents avec les autres pages
    if (team === 'alice') {
        if (email) email.value = "clara.valmont@email.fr";
        if (pass) pass.value = "SecretPapillon95";
    } else {
        if (email) email.value = "diane.valmont@email.fr";
        if (pass) pass.value = "MemoireVive92";
    }

    if (fillBtn) fillBtn.classList.add('hidden');
    if (loginBtn) loginBtn.classList.remove('hidden');
}

// Fonction pour afficher l'avertissement de phishing Colis
function showWarningPhishingColis() {
    const loginCard = document.getElementById('colis-login-card');
    const warningMsg = document.getElementById('colis-warning-msg');
    const returnBtn = document.getElementById('colis-return-btn');

    if (loginCard) loginCard.style.display = 'none';
    if (warningMsg) warningMsg.style.display = 'block';
    if (returnBtn) returnBtn.style.display = 'block';
}
window.autoFillImpots = autoFillImpots;
window.showWarningPhishingImpots = showWarningPhishingImpots;

window.autoFillFacebook = autoFillFacebook;
window.showWarningPhishingFacebook = showWarningPhishingFacebook;

window.autoFillGenealogie = autoFillGenealogie;
window.showWarningPhishingGenealogie = showWarningPhishingGenealogie;

window.autoFillColis = autoFillColis;
window.showWarningPhishingColis = showWarningPhishingColis;