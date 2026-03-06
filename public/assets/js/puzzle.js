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

// ===============================================
//                 Phishing Mail
// ===============================================

document.addEventListener('DOMContentLoaded', () => {
    const emailItems = document.querySelectorAll('.email-item-logic');
    const displayArea = document.getElementById('email-display-area');
    const pdfSimu = document.getElementById('pdf-simulation');
    const validationSection = document.getElementById('validation-section');

    if (!emailItems.length || !displayArea) return;

    // Contenus des emails
    const mailData = {
        1: {
            from: "service-client@inf0-impots.gouv.fr",
            subject: "Remboursement de trop-perçu",
            body: "<p>Cher(e) contribuable,</p><p>Après examen de votre dossier fiscal, un remboursement de 450,20€ est en votre faveur.</p><p>Veuillez confirmer vos coordonnées sur notre portail sécurisé : <br><a href='index.php?controller=Puzzle&action=phishingLinkClick&from_id=1'>http://impots-gouv-remboursement-virement.net/ref45</a></p><p>Cordialement,<br>L'administration fiscale.</p>"
        },
        2: {
            from: "secure-check@faceb00k.security.com",
            subject: "Alerte de sécurité importante",
            body: "<p>Bonjour,</p><p>Une tentative de connexion suspecte a été détectée depuis Singapour. Si vous n'êtes pas à l'origine de cette action, sécurisez votre compte immédiatement.</p><div class='button class' id='block-button-facebook-phishing'><a href='index.php?controller=Puzzle&action=phishingLinkClick&from_id=2' class='btn-nav' id='button-facebook-phishing'>SÉCURISER MON COMPTE</a></div><p>L'équipe de sécurité.</p>"
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
                   <p>Pour visualiser ce document inédit, veuillez régulariser votre abonnement annuel (19,99€) en cliquant sur le <a href='index.php?controller=Puzzle&action=phishingLinkClick&from_id=4'>lien sécurisé</a>.</p>
                   <p>L'équipe Généalogie Direct.</p>`
        }
    };

    // Récupération des paramètres d'initialisation (utile après un rechargement de page)
    const container = document.getElementById('phishing-container');
    const team = container ? container.getAttribute('data-team') : 'alice';
    const initialMailId = container ? container.getAttribute('data-open-mail') : null;
    const initialOpenPdf = container ? container.getAttribute('data-open-pdf') === '1' : false;

    // Détermination du contenu selon l'équipe
    const targetName = (team === 'alice') ? 'Diane VALMONT' : 'Clara VALMONT';
    const motherName = (team === 'alice') ? 'Clara VALMONT' : 'Diane VALMONT';
    const gpsCoord = (team === 'alice') ? '43°14\'18.6\"N' : '5°26\'18.1\"E';


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
                    <span class="handwritten-label">Note manuscrite :</span><br>
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

(function () {
    'use strict';

    function el(id)   { return document.getElementById(id); }
    function show(id) { el(id).classList.remove('hidden'); }
    function hide(id) { el(id).classList.add('hidden'); }

    let isFollowing  = false;
    let modalLiked   = false;
    let currentPost  = null;
    let profileFound = false;

    const TARGET = (typeof IG_TARGET !== 'undefined') ? IG_TARGET : {};

    // Si le profil a déjà été trouvé (bot a déjà répondu = session active),
    // on affiche directement le bouton DM dans le header
    if (TARGET.botReplied) {
        const btn = el('igDmHeaderBtn');
        if (btn) btn.classList.remove('hidden');
    }

    window.igOnSearch = function (val) {
        const dropdown = el('igSearchDropdown');
        val = val.trim().toLowerCase();

        if (!val) { dropdown.classList.add('hidden'); return; }

        const match =
            TARGET.handle.toLowerCase().includes(val) ||
            TARGET.name.toLowerCase().includes(val)   ||
            TARGET.handle.replace('.', ' ').toLowerCase().includes(val);

        dropdown.innerHTML = match
            ? `<div class="ig-search-item" onclick="igShowProfile()">
                   <div class="ig-search-avatar">${TARGET.letter}</div>
                   <div>
                       <div class="ig-search-name">${TARGET.handle}</div>
                       <div class="ig-search-sub">${TARGET.name}</div>
                   </div>
               </div>`
            : `<div class="ig-search-empty">Aucun résultat pour « ${val} »</div>`;

        dropdown.classList.remove('hidden');
    };

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.ig-search-wrap')) {
            el('igSearchDropdown').classList.add('hidden');
        }
    });

    window.igShowProfile = function () {
        profileFound = true;
        el('igSearchDropdown').classList.add('hidden');
        el('igSearchInput').value = '';

        hide('ig-home-state');
        show('ig-profile-state');

        // Rendre le bouton DM visible dans le header
        const dmBtn = el('igDmHeaderBtn');
        if (dmBtn) dmBtn.classList.remove('hidden');

        // Construire la grille
        el('igProfileGrid').innerHTML = TARGET.posts
            .map(p => `
                <div class="ig-grid-item" onclick="igOpenModal('${p.id}')">
                    <span class="ig-grid-emoji">${p.emoji}</span>
                </div>`)
            .join('');
    };

    window.igToggleFollow = function () {
        isFollowing = !isFollowing;
        const btn   = el('igFollowBtn');
        const count = el('igFollowersCount');

        if (isFollowing) {
            btn.textContent = 'Suivi';
            btn.classList.add('following');
            count.textContent = TARGET.followers + 1;
        } else {
            btn.textContent = 'Suivre';
            btn.classList.remove('following');
            count.textContent = TARGET.followers;
        }
    };

    window.igOpenDm = function () {
        show('igDmOverlay');
        // Scroll vers le bas des messages
        const msgs = el('igDmMessages');
        if (msgs) msgs.scrollTop = msgs.scrollHeight;
    };

    window.igCloseDm = function () {
        hide('igDmOverlay');
    };

    window.igOpenModal = function (postId) {
        currentPost = TARGET.posts.find(p => p.id === postId);
        if (!currentPost) return;
        modalLiked = false;

        el('igModalImg').textContent      = currentPost.emoji;
        el('igModalLocation').textContent = currentPost.location;
        el('igModalLikes').textContent    = currentPost.likes + " J'aime";

        const likeBtn = el('igModalLikeBtn');
        likeBtn.classList.remove('liked');
        likeBtn.innerHTML = heartSVG(false);

        el('igModalBody').innerHTML =
            `<div class="ig-modal-comment">
                <div class="ig-modal-c-avatar">${TARGET.letter}</div>
                <div><strong>${TARGET.handle}</strong> ${currentPost.caption}</div>
            </div>` +
            currentPost.comments.map(c =>
                `<div class="ig-modal-comment">
                    <div class="ig-modal-c-avatar">${c.user[0].toUpperCase()}</div>
                    <div><strong>${c.user}</strong> ${c.text}</div>
                </div>`
            ).join('') +
            `<div class="ig-modal-time">${currentPost.time}</div>`;

        el('igModal').classList.remove('hidden');
    };

    window.igCloseModal = function () {
        el('igModal').classList.add('hidden');
        currentPost = null;
    };

    window.igCloseModalOutside = function (e) {
        if (e.target === el('igModal')) igCloseModal();
    };

    window.igToggleModalLike = function () {
        if (!currentPost) return;
        modalLiked = !modalLiked;
        const btn  = el('igModalLikeBtn');
        const likes = el('igModalLikes');
        btn.classList.toggle('liked', modalLiked);
        btn.innerHTML     = heartSVG(modalLiked);
        likes.textContent = (currentPost.likes + (modalLiked ? 1 : 0)) + " J'aime";
    };

    function heartSVG(filled) {
        const fill   = filled ? 'red' : 'none';
        const stroke = filled ? 'red' : 'currentColor';
        return `<svg width="22" height="22" viewBox="0 0 24 24"
                    fill="${fill}" stroke="${stroke}" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67
                             l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78
                             l1.06 1.06L12 21.23l7.78-7.78
                             1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>`;
    }

})();