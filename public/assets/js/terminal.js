/**
 * Terminal Emulator - JavaScript
 * Escape The Code - CyberCigales
 */

document.addEventListener("DOMContentLoaded", () => {
    const terminalPage = document.getElementById("terminal-page");
    const desktopWorkspace = document.getElementById("desktop-workspace");
    const terminalWindow = document.getElementById("terminal-window");
    const terminalBody = document.getElementById("terminal-body");
    const terminalOutput = document.getElementById("terminal-output");
    const terminalInput = document.getElementById("terminal-input");
    const userLogin = document.body.dataset.userLogin || "cigale";

    const btnMin = document.getElementById("terminal-btn-min");
    const btnMax = document.getElementById("terminal-btn-max");
    const btnClose = document.getElementById("terminal-btn-close");
    const titlebar = document.getElementById("terminal-titlebar");

    const taskbarItem = document.getElementById("terminal-taskbar-item");
    const taskbarStartBtn = document.getElementById("taskbar-start-btn");
    const taskbarClock = document.getElementById("taskbar-clock");

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
        PATH: "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/opt/cyber/bin",
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

    // ========================================================
    // 1. Horloge de la barre des tâches (Temps réel)
    // ========================================================
    function updateClock() {
        if (!taskbarClock) return;
        const now = new Date();
        const h = String(now.getHours()).padStart(2, "0");
        const m = String(now.getMinutes()).padStart(2, "0");
        const s = String(now.getSeconds()).padStart(2, "0");
        taskbarClock.textContent = `${h}:${m}:${s}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Focus automatique sur l'input au clic dans le terminal
    terminalBody.addEventListener("click", () => {
        terminalInput.focus();
    });

    // ========================================================
    // 2. Déplacement de la fenêtre par la barre de titre (Drag & Drop)
    // ========================================================
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let initialLeft = 0;
    let initialTop = 0;
    let minLeft = -Infinity;
    let maxLeft = Infinity;
    let minTop = -Infinity;
    let maxTop = Infinity;

    function startDrag(clientX, clientY) {
        // Ne pas déplacer si la fenêtre est maximisée
        if (terminalWindow.classList.contains("is-maximized")) {
            return;
        }

        isDragging = true;
        terminalWindow.classList.add("is-dragging");

        dragStartX = clientX;
        dragStartY = clientY;

        initialLeft = parseFloat(terminalWindow.style.left) || 0;
        initialTop = parseFloat(terminalWindow.style.top) || 0;

        if (desktopWorkspace) {
            const wsRect = desktopWorkspace.getBoundingClientRect();
            const winRect = terminalWindow.getBoundingClientRect();

            // Bloquer vers le haut pour que la barre de titre ne passe pas sous le header
            minTop = initialTop - (winRect.top - wsRect.top);
            // Bloquer vers le bas pour garder la barre de titre au-dessus de la barre des tâches
            maxTop = initialTop + (wsRect.bottom - winRect.top - 40);

            // Bloquer sur les côtés tout en permettant d'utiliser toute la largeur de l'écran
            minLeft = initialLeft - (winRect.right - wsRect.left) + 120;
            maxLeft = initialLeft + (wsRect.right - winRect.left) - 120;
        }

        document.addEventListener("mousemove", onMouseMove);
        document.addEventListener("mouseup", onMouseUp);
        document.addEventListener("touchmove", onTouchMove, { passive: false });
        document.addEventListener("touchend", onTouchEnd);
    }

    function moveDrag(clientX, clientY) {
        if (!isDragging) return;

        const deltaX = clientX - dragStartX;
        const deltaY = clientY - dragStartY;

        let newLeft = initialLeft + deltaX;
        let newTop = initialTop + deltaY;

        if (Number.isFinite(minLeft) && Number.isFinite(maxLeft)) {
            newLeft = Math.max(minLeft, Math.min(newLeft, maxLeft));
        }
        if (Number.isFinite(minTop) && Number.isFinite(maxTop)) {
            newTop = Math.max(minTop, Math.min(newTop, maxTop));
        }

        terminalWindow.style.left = `${newLeft}px`;
        terminalWindow.style.top = `${newTop}px`;
    }

    function stopDrag() {
        if (isDragging) {
            isDragging = false;
            terminalWindow.classList.remove("is-dragging");
            document.removeEventListener("mousemove", onMouseMove);
            document.removeEventListener("mouseup", onMouseUp);
            document.removeEventListener("touchmove", onTouchMove);
            document.removeEventListener("touchend", onTouchEnd);
        }
    }

    function onMouseMove(e) {
        moveDrag(e.clientX, e.clientY);
    }

    function onMouseUp() {
        stopDrag();
    }

    function onTouchMove(e) {
        if (isDragging && e.touches.length > 0) {
            e.preventDefault();
            moveDrag(e.touches[0].clientX, e.touches[0].clientY);
        }
    }

    function onTouchEnd() {
        stopDrag();
    }

    if (titlebar) {
        titlebar.addEventListener("mousedown", (e) => {
            // Ignorer si on clique sur un bouton de contrôle (min, max, close)
            if (e.target.closest(".ctrl-btn")) return;
            startDrag(e.clientX, e.clientY);
            e.preventDefault();
        });

        titlebar.addEventListener("touchstart", (e) => {
            if (e.target.closest(".ctrl-btn")) return;
            if (e.touches.length > 0) {
                startDrag(e.touches[0].clientX, e.touches[0].clientY);
            }
        }, { passive: true });

        // Double-clic sur la barre pour maximiser / restaurer (comme sur KDE / Windows)
        titlebar.addEventListener("dblclick", (e) => {
            if (e.target.closest(".ctrl-btn")) return;
            if (btnMax) btnMax.click();
        });
    }

    // ========================================================
    // 3. Gestion Réduire / Restaurer / Maximiser / Barre des tâches
    // ========================================================
    function minimizeTerminal() {
        wasMaximized = terminalWindow.classList.contains("is-maximized");
        terminalWindow.classList.add("is-minimizing");

        setTimeout(() => {
            terminalWindow.classList.remove("is-minimizing");
            terminalWindow.classList.add("is-minimized");

            if (taskbarItem) {
                taskbarItem.classList.remove("is-active");
            }
        }, 260);
    }

    function restoreTerminal() {
        terminalWindow.classList.remove("is-minimized");

        if (taskbarItem) {
            taskbarItem.classList.add("is-active");
        }

        // Rétablit l'état précédent (maximisé ou normal avec sa position déplacée)
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

        terminalWindow.classList.add("is-restoring");
        setTimeout(() => {
            terminalWindow.classList.remove("is-restoring");
            terminalInput.focus();
        }, 280);
    }

    // Bouton Réduire (Minimize)
    if (btnMin) {
        btnMin.addEventListener("click", (e) => {
            e.stopPropagation();
            minimizeTerminal();
        });
    }

    // Clic sur l'application dans la barre des tâches (Style Windows / KDE)
    if (taskbarItem) {
        taskbarItem.addEventListener("click", (e) => {
            e.stopPropagation();
            if (terminalWindow.classList.contains("is-minimized")) {
                restoreTerminal();
            } else {
                minimizeTerminal();
            }
        });
    }

    // Clic sur le bouton Démarrer / Menu
    if (taskbarStartBtn) {
        taskbarStartBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            if (terminalWindow.classList.contains("is-minimized")) {
                restoreTerminal();
            } else {
                terminalInput.focus();
            }
        });
    }

    // Bouton Agrandir / Restaurer (Maximize)
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

            window.scrollTo({ top: 0, behavior: "smooth" });
            terminalInput.focus();
        });
    }

    // Bouton Fermer (Close)
    if (btnClose) {
        btnClose.addEventListener("click", (e) => {
            e.stopPropagation();
            if (confirm("Voulez-vous fermer le terminal et retourner à l'accueil ?")) {
                window.location.href = "index.php?controller=Redirection&action=openHomepage";
            }
        });
    }

    // ========================================================
    // 4. Gestion de la saisie utilisateur & Bash
    // ========================================================
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
            if (envVariables.hasOwnProperty(varName)) {
                return envVariables[varName];
            }
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
