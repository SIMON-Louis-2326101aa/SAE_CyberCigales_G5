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
    // 2. Déplacement & Redimensionnement de la fenêtre (KDE / Windows)
    // ========================================================
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let initialLeft = 0;
    let initialTop = 0;

    // Dimensions et positions personnalisées après redimensionnement
    let savedNormalWidth = null;
    let savedNormalHeight = null;
    let savedNormalLeft = null;
    let savedNormalTop = null;

    // Déplacement depuis un état maximisé
    let pendingMaximizedDrag = false;
    let maxDragStartX = 0;
    let maxDragStartY = 0;

    // Redimensionnement
    let isResizing = false;
    let resizeDir = "";
    let resizeStartX = 0;
    let resizeStartY = 0;
    let resizeStartW = 0;
    let resizeStartH = 0;
    let resizeStartLeft = 0;
    let resizeStartTop = 0;

    // --- Gestion du Drag ---
    function startDrag(clientX, clientY) {
        if (terminalWindow.classList.contains("is-maximized") || !desktopWorkspace) {
            return;
        }

        isDragging = true;
        terminalWindow.classList.add("is-dragging");

        dragStartX = clientX;
        dragStartY = clientY;

        const wsRect = desktopWorkspace.getBoundingClientRect();
        const winRect = terminalWindow.getBoundingClientRect();

        // Récupère la position exacte en pixels dans desktopWorkspace
        initialLeft = winRect.left - wsRect.left;
        initialTop = winRect.top - wsRect.top;

        // Fixe en pixels stricts pour éliminer tout décalage
        terminalWindow.style.left = `${initialLeft}px`;
        terminalWindow.style.top = `${initialTop}px`;
        terminalWindow.style.width = `${winRect.width}px`;
        terminalWindow.style.height = `${winRect.height}px`;

        savedNormalWidth = winRect.width;
        savedNormalHeight = winRect.height;
        savedNormalLeft = initialLeft;
        savedNormalTop = initialTop;

        document.addEventListener("mousemove", onMouseMove);
        document.addEventListener("mouseup", onMouseUp);
        document.addEventListener("touchmove", onTouchMove, { passive: false });
        document.addEventListener("touchend", onTouchEnd);
    }

    function moveDrag(clientX, clientY) {
        if (!isDragging || !desktopWorkspace) return;

        const deltaX = clientX - dragStartX;
        const deltaY = clientY - dragStartY;

        let newLeft = initialLeft + deltaX;
        let newTop = initialTop + deltaY;

        const wsRect = desktopWorkspace.getBoundingClientRect();
        const winRect = terminalWindow.getBoundingClientRect();

        // Ne passe pas sous le header et garde la barre de titre dans le bureau
        newTop = Math.max(0, Math.min(newTop, wsRect.height - 40));
        // Garde au moins 120px de la fenêtre visible horizontalement
        newLeft = Math.max(-winRect.width + 120, Math.min(newLeft, wsRect.width - 120));

        terminalWindow.style.left = `${newLeft}px`;
        terminalWindow.style.top = `${newTop}px`;

        savedNormalLeft = newLeft;
        savedNormalTop = newTop;
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

    // --- Restauration fluide lors du drag d'une fenêtre maximisée ---
    function unmaximizeAndStartDrag(clientX, clientY, startX, startY) {
        if (!desktopWorkspace) return;

        const wsRect = desktopWorkspace.getBoundingClientRect();
        const maxWinRect = terminalWindow.getBoundingClientRect();

        // Ratio horizontal où se trouvait le curseur sur la barre maximisée
        const ratioX = Math.max(0.1, Math.min(0.9, (startX - maxWinRect.left) / maxWinRect.width));

        // Désactive l'état maximisé
        terminalWindow.classList.remove("is-maximized");
        if (terminalPage) {
            terminalPage.classList.remove("is-maximized");
        }
        if (btnMax) {
            btnMax.innerHTML = "<span>&#x25A2;</span>";
        }

        // Récupère les dimensions normales sauvegardées
        const restoredW = savedNormalWidth || (wsRect.width * 0.75);
        const restoredH = savedNormalHeight || (wsRect.height * 0.85);

        terminalWindow.style.width = `${restoredW}px`;
        terminalWindow.style.height = `${restoredH}px`;

        // Position sous le curseur
        let newLeft = (clientX - wsRect.left) - (ratioX * restoredW);
        let newTop = (clientY - wsRect.top) - 18;

        newLeft = Math.max(0, Math.min(newLeft, wsRect.width - 120));
        newTop = Math.max(0, Math.min(newTop, wsRect.height - 40));

        terminalWindow.style.left = `${newLeft}px`;
        terminalWindow.style.top = `${newTop}px`;

        savedNormalLeft = newLeft;
        savedNormalTop = newTop;
        savedNormalWidth = restoredW;
        savedNormalHeight = restoredH;

        // Démarre le drag standard immédiatement
        startDrag(clientX, clientY);
    }

    function onMaximizedDragMove(e) {
        if (!pendingMaximizedDrag) return;
        const dist = Math.hypot(e.clientX - maxDragStartX, e.clientY - maxDragStartY);
        if (dist > 5) {
            pendingMaximizedDrag = false;
            document.removeEventListener("mousemove", onMaximizedDragMove);
            document.removeEventListener("mouseup", onMaximizedDragUp);
            unmaximizeAndStartDrag(e.clientX, e.clientY, maxDragStartX, maxDragStartY);
        }
    }

    function onMaximizedDragUp() {
        pendingMaximizedDrag = false;
        document.removeEventListener("mousemove", onMaximizedDragMove);
        document.removeEventListener("mouseup", onMaximizedDragUp);
    }

    // --- Écouteurs sur la barre de titre ---
    if (titlebar) {
        titlebar.addEventListener("mousedown", (e) => {
            if (e.target.closest(".ctrl-btn")) return;

            // Si la fenêtre est maximisée, prépare la restauration au drag
            if (terminalWindow.classList.contains("is-maximized")) {
                pendingMaximizedDrag = true;
                maxDragStartX = e.clientX;
                maxDragStartY = e.clientY;
                document.addEventListener("mousemove", onMaximizedDragMove);
                document.addEventListener("mouseup", onMaximizedDragUp);
                return;
            }

            startDrag(e.clientX, e.clientY);
            e.preventDefault();
        });

        titlebar.addEventListener("touchstart", (e) => {
            if (e.target.closest(".ctrl-btn")) return;
            if (e.touches.length > 0) {
                if (terminalWindow.classList.contains("is-maximized")) return;
                startDrag(e.touches[0].clientX, e.touches[0].clientY);
            }
        }, { passive: true });

        // Double-clic sur la barre pour maximiser / restaurer (comme sur KDE / Windows)
        titlebar.addEventListener("dblclick", (e) => {
            if (e.target.closest(".ctrl-btn")) return;
            toggleMaximize();
        });
    }

    // --- Gestion du Redimensionnement par les bords (Resize) ---
    const resizeHandles = document.querySelectorAll(".resize-handle");

    function initResize(e, dir) {
        if (terminalWindow.classList.contains("is-maximized") || terminalWindow.classList.contains("is-minimized") || !desktopWorkspace) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();

        isResizing = true;
        resizeDir = dir;
        resizeStartX = e.clientX || (e.touches && e.touches[0].clientX);
        resizeStartY = e.clientY || (e.touches && e.touches[0].clientY);

        const wsRect = desktopWorkspace.getBoundingClientRect();
        const rect = terminalWindow.getBoundingClientRect();

        resizeStartW = rect.width;
        resizeStartH = rect.height;
        resizeStartLeft = rect.left - wsRect.left;
        resizeStartTop = rect.top - wsRect.top;

        // Force les dimensions et coordonnées en pixels stricts
        terminalWindow.style.left = `${resizeStartLeft}px`;
        terminalWindow.style.top = `${resizeStartTop}px`;
        terminalWindow.style.width = `${resizeStartW}px`;
        terminalWindow.style.height = `${resizeStartH}px`;

        terminalWindow.classList.add("is-resizing");
        document.body.style.userSelect = "none";

        document.addEventListener("mousemove", onResizeMove);
        document.addEventListener("mouseup", onResizeEnd);
        document.addEventListener("touchmove", onResizeTouchMove, { passive: false });
        document.addEventListener("touchend", onResizeEnd);
    }

    function doResize(clientX, clientY) {
        if (!isResizing || !desktopWorkspace) return;

        const deltaX = clientX - resizeStartX;
        const deltaY = clientY - resizeStartY;

        const wsRect = desktopWorkspace.getBoundingClientRect();
        const minW = 460;
        const minH = 300;
        const maxW = wsRect.width;
        const maxH = wsRect.height;

        let newW = resizeStartW;
        let newH = resizeStartH;
        let newLeft = resizeStartLeft;
        let newTop = resizeStartTop;

        // Côté droit (e) : le bord gauche reste parfaitement cloué en place
        if (resizeDir.includes("e")) {
            newW = Math.min(maxW - resizeStartLeft, Math.max(minW, resizeStartW + deltaX));
        }

        // Côté bas (s) : le bord haut reste parfaitement cloué en place
        if (resizeDir.includes("s")) {
            newH = Math.min(maxH - resizeStartTop, Math.max(minH, resizeStartH + deltaY));
        }

        // Côté gauche (w) : le bord droit reste parfaitement cloué en place (resizeStartLeft + resizeStartW)
        if (resizeDir.includes("w")) {
            newW = resizeStartW - deltaX;
            if (newW < minW) newW = minW;
            newLeft = resizeStartLeft + (resizeStartW - newW);
            if (newLeft < 0) {
                newW += newLeft;
                newLeft = 0;
            }
        }

        // Côté haut (n) : le bord bas reste parfaitement cloué en place (resizeStartTop + resizeStartH)
        if (resizeDir.includes("n")) {
            newH = resizeStartH - deltaY;
            if (newH < minH) newH = minH;
            newTop = resizeStartTop + (resizeStartH - newH);
            if (newTop < 0) {
                newH += newTop;
                newTop = 0;
            }
        }

        terminalWindow.style.width = `${newW}px`;
        terminalWindow.style.height = `${newH}px`;
        terminalWindow.style.left = `${newLeft}px`;
        terminalWindow.style.top = `${newTop}px`;

        savedNormalWidth = newW;
        savedNormalHeight = newH;
        savedNormalLeft = newLeft;
        savedNormalTop = newTop;
    }

    function onResizeMove(e) {
        doResize(e.clientX, e.clientY);
    }

    function onResizeTouchMove(e) {
        if (isResizing && e.touches.length > 0) {
            e.preventDefault();
            doResize(e.touches[0].clientX, e.touches[0].clientY);
        }
    }

    function onResizeEnd() {
        if (!isResizing) return;
        isResizing = false;
        terminalWindow.classList.remove("is-resizing");
        document.body.style.userSelect = "";

        document.removeEventListener("mousemove", onResizeMove);
        document.removeEventListener("mouseup", onResizeEnd);
        document.removeEventListener("touchmove", onResizeTouchMove);
        document.removeEventListener("touchend", onResizeEnd);
    }

    resizeHandles.forEach((handle) => {
        const dir = handle.dataset.dir;
        handle.addEventListener("mousedown", (e) => initResize(e, dir));
        handle.addEventListener("touchstart", (e) => initResize(e, dir), { passive: false });
    });

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

        // Rétablit l'état précédent (maximisé ou normal avec sa position déplacée et ses dimensions)
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
            if (savedNormalWidth) {
                terminalWindow.style.width = `${savedNormalWidth}px`;
            }
            if (savedNormalHeight) {
                terminalWindow.style.height = `${savedNormalHeight}px`;
            }
            if (savedNormalLeft !== null && savedNormalLeft !== undefined) {
                terminalWindow.style.left = `${savedNormalLeft}px`;
            }
            if (savedNormalTop !== null && savedNormalTop !== undefined) {
                terminalWindow.style.top = `${savedNormalTop}px`;
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

    // Fonction centralisée Agrandir / Restaurer (Maximize)
    function toggleMaximize() {
        const isMax = terminalWindow.classList.contains("is-maximized");

        if (!isMax) {
            // Sauvegarder les dimensions et positions normales actuelles AVANT d'appliquer l'état maximisé
            const wsRect = desktopWorkspace ? desktopWorkspace.getBoundingClientRect() : { left: 0, top: 0 };
            const rect = terminalWindow.getBoundingClientRect();

            savedNormalWidth = rect.width;
            savedNormalHeight = rect.height;
            savedNormalLeft = rect.left - wsRect.left;
            savedNormalTop = rect.top - wsRect.top;

            terminalWindow.classList.add("is-maximized");
            if (terminalPage) {
                terminalPage.classList.add("is-maximized");
            }
            if (btnMax) {
                btnMax.innerHTML = "<span>&#x25F3;</span>";
            }
        } else {
            // Restauration de l'état normal
            terminalWindow.classList.remove("is-maximized");
            if (terminalPage) {
                terminalPage.classList.remove("is-maximized");
            }
            if (btnMax) {
                btnMax.innerHTML = "<span>&#x25A2;</span>";
            }

            // Réappliquer les dimensions et coordonnées sauvegardées
            if (savedNormalWidth) {
                terminalWindow.style.width = `${savedNormalWidth}px`;
            }
            if (savedNormalHeight) {
                terminalWindow.style.height = `${savedNormalHeight}px`;
            }
            if (savedNormalLeft !== null && savedNormalLeft !== undefined) {
                terminalWindow.style.left = `${savedNormalLeft}px`;
            }
            if (savedNormalTop !== null && savedNormalTop !== undefined) {
                terminalWindow.style.top = `${savedNormalTop}px`;
            }
        }

        window.scrollTo({ top: 0, behavior: "smooth" });
        terminalInput.focus();
    }

    // Bouton Agrandir / Restaurer
    if (btnMax) {
        btnMax.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleMaximize();
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
  <strong>echo</strong> [texte]   : Affiche une ligne de texte (supporte les guillemets, apostrophes et variables $USER, $HOME, $PATH, $0, $$...)
  <strong>clear</strong>          : Efface l'écran du terminal
  <strong>help</strong>           : Affiche ce message d'aide

<p>Note : L'environnement est sous Linux avec le shell BASH (commandes sensibles à la casse).</p>`;
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
