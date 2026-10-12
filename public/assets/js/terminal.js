/**
 * Terminal Emulator - JavaScript
 * Escape The Code - CyberCigales
 */

document.addEventListener("DOMContentLoaded", () => {
    const terminalPage = document.getElementById("terminal-page");
    const terminalWindow = document.getElementById("terminal-window");
    const terminalBody = document.getElementById("terminal-body");
    const terminalOutput = document.getElementById("terminal-output");
    const terminalInput = document.getElementById("terminal-input");
    const userLogin = document.body.dataset.userLogin || "cigale";

    const btnMin = document.getElementById("terminal-btn-min");
    const btnMax = document.getElementById("terminal-btn-max");
    const btnClose = document.getElementById("terminal-btn-close");
    const taskbarItem = document.getElementById("terminal-taskbar-item");

    if (!terminalWindow || !terminalInput) {
        return;
    }

    // Mémorise si le terminal était maximisé avant la réduction
    let wasMaximized = false;

    // Variables d'environnement et paramètres spéciaux simulés (Bash)
    const envVariables = {
        USER: userLogin,
        HOME: `/home/${userLogin}`,
        SHELL: "/bin/bash",
        PATH: "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/cyber/tools",
        TERM: "xterm-kitty",
        HOSTNAME: "cybercigales",
        "0": "bash",
        "$": "1337",
        "!": "",
        "?": "0",
        "#": "0",
        "*": "",
        "@": "",
        "-": "himBHs",
        "_": "echo"
    };

    // Historique des commandes
    const commandHistory = [];
    let historyIndex = -1;

    // Focus automatique sur l'input au clic dans le terminal
    terminalBody.addEventListener("click", () => {
        terminalInput.focus();
    });

    // 1. Bouton Réduire (Minimize) : Animation vers le bas puis affichage de l'icône dans la barre des tâches
    if (btnMin) {
        btnMin.addEventListener("click", (e) => {
            e.stopPropagation();
            wasMaximized = terminalWindow.classList.contains("is-maximized");

            terminalWindow.classList.add("is-minimizing");

            setTimeout(() => {
                terminalWindow.classList.remove("is-minimizing");
                terminalWindow.classList.add("is-minimized");

                if (terminalPage) {
                    terminalPage.classList.remove("is-maximized");
                    terminalPage.classList.add("is-minimized");
                }

                if (taskbarItem) {
                    taskbarItem.classList.add("is-visible");
                }
            }, 260);
        });
    }

    // Clic sur l'icône de la barre des tâches pour restaurer le terminal
    if (taskbarItem) {
        taskbarItem.addEventListener("click", (e) => {
            e.stopPropagation();
            taskbarItem.classList.remove("is-visible");
            terminalWindow.classList.remove("is-minimized");

            if (terminalPage) {
                terminalPage.classList.remove("is-minimized");
            }

            // Rétablit l'état précédent (maximisé ou normal)

            if (wasMaximized) {
                terminalWindow.classList.add("is-maximized");
                if (terminalPage) {
                    terminalPage.classList.add("is-maximized");
                }
                if (btnMax) {
                    btnMax.innerHTML = "<span>&#x25F3;</span>";
                }
            } else {
                terminalWindow.classList.remove("is-maximized");
                if (terminalPage) {
                    terminalPage.classList.remove("is-maximized");
                }
                if (btnMax) {
                    btnMax.innerHTML = "<span>&#x25A2;</span>";
                }
            }

            // Animation de remontée depuis le bas
            terminalWindow.classList.add("is-restoring");
            setTimeout(() => {
                terminalWindow.classList.remove("is-restoring");
                terminalInput.focus();
            }, 280);
        });
    }

    // 2. Bouton Agrandir (Maximize) : Bascule en plein écran sans défilement intempestif
    if (btnMax) {
        btnMax.addEventListener("click", (e) => {
            e.stopPropagation();
            const isNowMax = terminalWindow.classList.toggle("is-maximized");
            if (terminalPage) {
                terminalPage.classList.toggle("is-maximized", isNowMax);
            }
            btnMax.innerHTML = isNowMax
                ? "<span>&#x25F3;</span>"
                : "<span>&#x25A2;</span>";

            // Aligne simplement la page tout en haut pour que le header et la barre de titre soient parfaits
            window.scrollTo({ top: 0, behavior: "smooth" });
            terminalInput.focus();
        });
    }

    // 3. Bouton Fermer (Close)
    if (btnClose) {
        btnClose.addEventListener("click", (e) => {
            e.stopPropagation();
            if (confirm("Voulez-vous fermer le terminal et retourner à l'accueil ?")) {
                window.location.href = "index.php?controller=Redirection&action=openHomepage";
            }
        });
    }

    // Gestion du clavier dans le terminal
    terminalInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            const rawCommand = terminalInput.value;
            handleCommand(rawCommand);
            terminalInput.value = "";
            historyIndex = commandHistory.length;
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            if (commandHistory.length > 0 && historyIndex > 0) {
                historyIndex--;
                terminalInput.value = commandHistory[historyIndex];
            } else if (historyIndex === -1 && commandHistory.length > 0) {
                historyIndex = commandHistory.length - 1;
                terminalInput.value = commandHistory[historyIndex];
            }
        } else if (e.key === "ArrowDown") {
            e.preventDefault();
            if (historyIndex < commandHistory.length - 1 && historyIndex >= 0) {
                historyIndex++;
                terminalInput.value = commandHistory[historyIndex];
            } else {
                historyIndex = commandHistory.length;
                terminalInput.value = "";
            }
        }
    });

    /**
     * Traite et exécute une ligne de commande entrée par l'utilisateur
     */
    function handleCommand(rawInput) {
        const trimmed = rawInput.trim();

        // Enregistre dans l'historique si non-vide
        if (trimmed !== "") {
            commandHistory.push(rawInput);
        }

        // Création de l'élément visuel pour la commande exécutée
        const entry = document.createElement("div");
        entry.className = "terminal-entry";

        // Reproduit le prompt Kali Linux avec la commande tapée
        entry.innerHTML = `
            <div class="kali-prompt-line1">
                <span class="p-bracket">┌──(</span><span class="p-user">${escapeHtml(userLogin)}</span><span class="p-symbol">㉿</span><span class="p-host">cyber</span><span class="p-bracket">)-[</span><span class="p-path">~</span><span class="p-bracket">]</span>
            </div>
            <div class="kali-prompt-line2">
                <span class="p-arrow">└─$</span>
                <span>${escapeHtml(rawInput)}</span>
            </div>
        `;

        if (trimmed === "") {
            terminalOutput.appendChild(entry);
            scrollToBottom();
            return;
        }

        // Découpage STRICT (Sensible à la casse - Style Linux Bash)
        const firstSpace = trimmed.indexOf(" ");
        let cmd = "";
        let args = "";

        if (firstSpace === -1) {
            cmd = trimmed; // PAS de .toLowerCase() !
            args = "";
        } else {
            cmd = trimmed.substring(0, firstSpace); // PAS de .toLowerCase() !
            args = trimmed.substring(firstSpace + 1).trim();
        }

        // Exécution de la commande
        const outputElem = document.createElement("div");
        outputElem.className = "cmd-output";

        switch (cmd) {
            case "echo":
                outputElem.textContent = executeEcho(args);
                entry.appendChild(outputElem);
                break;

            case "clear":
                terminalOutput.innerHTML = "";
                scrollToBottom();
                return;

            case "help":
                outputElem.className += " cmd-info";
                outputElem.innerHTML = `
<strong>Commandes disponibles :</strong>
  <strong>echo</strong> &lt;texte&gt;   : Affiche une ligne de texte (supporte les guillemets et variables $USER, $0, $$, $?...)
  <strong>clear</strong>          : Efface l'écran du terminal
  <strong>help</strong>           : Affiche ce message d'aide

<em>Note : L'environnement est sous Linux/Bash (commandes sensibles à la casse).</em>`;
                entry.appendChild(outputElem);
                break;

            default:
                outputElem.className += " cmd-error";
                outputElem.textContent = `bash: ${cmd}: commande introuvable. Tapez "help" pour afficher les commandes.`;
                entry.appendChild(outputElem);
                break;
        }

        terminalOutput.appendChild(entry);
        scrollToBottom();
    }

    /**
     * Traitement de la commande echo avec gestion complète des variables Bash
     */
    function executeEcho(args) {
        if (!args || args === "") {
            return "";
        }

        let text = args;

        // Guillemets simples ('...') : Aucun remplacement de variable dans Bash
        if (text.startsWith("'") && text.endsWith("'") && text.length >= 2) {
            return text.substring(1, text.length - 1);
        }

        // Guillemets doubles ("...") : Retrait des guillemets puis expansion des variables
        if (text.startsWith('"') && text.endsWith('"') && text.length >= 2) {
            text = text.substring(1, text.length - 1);
        }

        // Expansion des variables Bash :
        // Supporte $VAR, ${VAR}, mais aussi les paramètres spéciaux $0, $$, $!, $?, $#, $*, $@, $-, $1...
        const varRegex = /\$\{([A-Za-z_][A-Za-z0-9_]*|[0-9]+|[\?\$\!\#\*\@\-\_])\}|\$([A-Za-z_][A-Za-z0-9_]*|[0-9]+|[\?\$\!\#\*\@\-\_])/g;

        text = text.replace(varRegex, (match, p1, p2) => {
            const varName = p1 || p2;
            // Si la variable est définie, on renvoie sa valeur
            if (envVariables.hasOwnProperty(varName)) {
                return envVariables[varName];
            }
            // Si c'est une variable inconnue ou non-définie (ex: $user, $foo, $1), Bash renvoie du vide ""
            return "";
        });

        return text;
    }

    function scrollToBottom() {
        terminalBody.scrollTop = terminalBody.scrollHeight;
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
