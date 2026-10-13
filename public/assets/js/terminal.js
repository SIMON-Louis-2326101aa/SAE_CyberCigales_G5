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
    const asciiElem = document.getElementById("terminal-ascii-art");

    if (!terminalWindow || !terminalInput) {
        return;
    }

    // Heure de chargement pour calcul de l'Uptime (fastfetch / neofetch)
    const pageStartTime = Date.now();

    // ASCII Art de base CyberCigales (mémorisé en mémoire vive pour basculement sans rechargement ni duplication)
    const CYBER_ASCII_DEFAULT =
        "   ____      _                ____ _             _           \n" +
        "  / ___|   _| |__   ___ _ __./ ___(_) __ _  __ _| | ___  ___ \n" +
        " | |  | | | | '_ \\ / _ \\ '__| |   | |/ _` |/ _` | |/ _ \\/ __|\n" +
        " | |__| |_| | |_) |  __/ |  | |___| | (_| | (_| | |  __/\\__ \\\n" +
        "  \\____\\__, |_.__/ \\___|_|   \\____|_|\\__, |\\__,_|_|\\___||___/\n" +
        "       |___/                         |___/                    ";

    const baseCyberAscii = (!asciiElem?.classList.contains("ascii-dinotofu") && asciiElem?.textContent)
        ? asciiElem.textContent.replace(/^\n+|\n+$/g, "")
        : CYBER_ASCII_DEFAULT;

    // ASCII Art DinoTofu (mémorisé en mémoire vive pour exécution instantanée sans lecture disque)
    const DINOTOFU_ASCII = `                                                                          %%%                                                                         
                                                                        @#.=*@                                                                        
                                                                       %-:*%==%%                                                                      
                         @@@%%%                                    @%#*:=#:=##=*%%@                                                                   
                        %=-==:=@%                                  @*--#+::-=*%==*%%                                 %%%%@@@                          
                       @#=#--#-*@                             %@%*=-*#*=#####%**##=-+#@@                         %%*-:::+:-*@                         
                       %*+%%**=%@                         @@#+::::-:=#@*=+%##*#@%*=-----=*%@#                   @+:-*%%%*=#%@%                        
                        @##**+*%@                    %@%+--=:::-+#@@%%@@%=-+*%@@%%@@%*=----+-+#@%              %+-*#:.:=*%@++%                        
                         @%@@@#+#@              @@%+-:::::-*#%@%%%%%#*+*%@**@%#***#%%%%@@##=------+#@@         %-=#*=-==*#%#=#@                       
                            %@%#=*%@            @*=::-+*%@@%%%%#*++++++++#%%#*+++++++**%%%%%@%#+=---+%        %%=+%##**#%%%%=#@                       
                             @@%%#+#@           @*==@@%%%%#*++--=++++++++++++++++++++++++++*%%%%%@*++%        @#+=*%#%%#**%=#@%                       
                              @@%##+*@      @@@%%*==%%%+====-::::-====================-::::::--*%@++=%         @@#+:#@@@#=+#@@                        
                                @%%#=#@  @@+---#@*==%%#===-:::::::::=================-:::::::::=%%+==%         %=:+*##**#%@@                          
                                 @%%#---:--=#%#%@+==@%#------------------===========::::-*+=:::=%%+==%        @%*-+#@@@@%@                            
                                 @%#+*==+#@@%   %+==%%#-----------------------------::::-::::::=%%++=%      %@-:#@@%@%                                
                                 @=*#*#+%*=-@   @+==%##---=+=++----:---------------:::::++:::::=%%+++%      @@#@%#%%                                  
                               %*-=*@@@%%-..-@ %#=++@%#-=+===+++=------#@@=------.::::-+*#+*:::=%%++=+@    @%=+#@@@                                   
                             %#-=*%@%**##-::.-@=---%@%=++++++++++*=--#=:-=*%---::::::-**####::::#%@+-==%@%@%%*#%@                                     
                            @#++#@  @#++**+*#-+=-=%%%**+++*+++*#:-+%+#+--**%--%%%:::+:+*=*##::-*+#%@#-=*-*%**#%@                                      
                             @@%%@   @#*%-:----+@@%%***+**+***%+-+#%@%%%%%%@%=:-+%::*:+*+*##:-**#=*%%@#=-----#@@                                      
                                      @%%+***%@@%%#*******+**%@%*++=====++=+#%#*#%:-*+****##*+**#*==#%%@@**#+#@                                       
                            ##         %@++=%@@%#*****+***#%+====+========++++#%%##++%****###**+##*+**%@@@+++#@         #*%                           
                           %++%        %%++=#%@#*********##=#===+*===++++==+****#%#=*@*****##**####**##@%%=++#@        #*=#                           
                           %+=* #*     %%++=%%%##****#+-:#+=+===+++*###++==***####%%%@%%#****##***#####@%%=++#@     ####=+#                           
                           %**#*+*     %%++=#%@###***#+..-#*******=:....=****=--::+*%#=+%#**#****######@%%=+*#@     %++#+*%                           
                           #%####%     %%+++#%@####**#+.:+*:.........:......---===*##@%%#**#***########@%%+**#@     ###%#%%                           
                        %**  %#% #+*   @%**+#%@%#####%+..#@-........#@=:.:.-=======*##%#++%**##########@%%+**#@   #+##%##  #*#                        
                        %*+=#%*%+++#   @@###%%%%#####%+..+#--:..:-:.#@-:::.-========+*%%*#%###########%@%@*##%@   #*++%*#%++*%                        
                         %#**%*%**%%    @****%%%%####%+.:..:=%@@@%-.:::..:.===========*%%@%####%%####%%@%#+**%@    %**###+**%%                        
                          %%%%*%%% #%   @#**+@%%%####%+.:....::-::.....:::.========+++*#%@*-#%%=#%%%#%%%%****@@  ## %%%###%%                          
                             %%% ##=*   %%**+%%%%%###%+::::..........:.:::.======++=++*##*#%@@%+++%%%%%%%***#@   #=*% ##%                             
                        %*++##%#*#++#    @##**@%%%%##%+::::...:::...:-:.:::===+####+++*###*****++#%%%%%%#**#%%   %+*#%%#%#*++#                        
                         @***+%*####%    @%##*%%%%%%%@+::::::::::::::::::::=+*#===+%*++*+##***=*#%%%%@%%*###@    %%##%*%*+**%%                        
                          %%###@#%%%%#*# %@#%#+%%@%%%%@*+==::--::::::::-:::+@+=++**#%+*******#@@%%%%%%%+*##@@  #*#%%%#%%##%%                          
                             ##%#*  #**@  @%###*@%%%##%%%@@%@@@%#++===-::-++@*++*###@##@@@@@%%%%%#%%%%**#%%@  @#**% %#%##                             
                          *%%##%%%*%###@  @@%#%#+%%%####%@%%@%%@@@%%%%@@@@@@@%%####%@%%%%%%%%###*##%%*+##%@@% %%##%%#@%##%%%                          
                            @#*###%#%%%  %#%@%%##=%%@******####%%%%%%%%%%%%%@@#@%%@@@%###***+*#**#%%**#%%@@##% %%%%#####*%%                           
                              %@%%@%#@%%@##%%@%%%%+%%%#***#+++++++++++*****#**###*****+**+*+***#%#%=#%%%@@%%%%@ %#%@@%%@%                             
            @=-------:=-::::::::::::::::::-@%@@%%%%+*%%@%%%#####*++++++++++++++++*++####%##%%%%%%%=%%%%@@@@=:::::::::::::-::---=:--------%            
            @*++#@@%%@@@%%%%%%%@%%%@%%%%%+=@@@@@@%%%%+%%%@%%#%%%%%%%%####***#*%#%##%##%%%%%%%@%%**%%%@@@@@@+=#%%@@@@%%@%%%@@%%@@%%%@%%*++%            
            @*+%@%%@@%@@%%@%@@@@@@%###%%@+=%@@%@@@@%%%*+%%%@%%%%%%%##%%###%%###%%%#%%%%%%%@@%%*+%%%%@@@@%@@+=#%%%%%%%%#######%%%%%%%%%@++%            
           @##+#%%%%*::::::::::::-=%%%%%%%*==************************************************************+=+%%%%%%=:+%%%%%%%%%%%%%%%%%%++#%           
         %%===%@%%%%@%*-::::::::::::-#%%%%%@%@@######%###########################%%####################%##%@%%%#::::::*%%%%%%%%%%%%%%%%%*+=*%         
        %*++#@@%%%%%%@@*:::-%@@%=:::::+%%%%@+:-#%%%%%%%%%%%%%%#%#####%%%%%%%%%%%%%%#%%%%%%%%%%%%%%%%%%%%%%%%%%-::+%#=::%@%%%%%%%%%%%%%%%@%+++#%       
      %*=+#%%%%%%%%%%%@*:::-%@%@@#:::::-@%%+-::=#@%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%#-#%%%%%%%%%%%%%%%%%%%%#:::*@@@%=%%%%%%%%%%%%%%%%%%%@%*++%%     
    %#=+*@@%%%%%%%%%%%*+:::-%%%%@@+::::-@%%@@*%@@%%%%%%%%%%%%@%%%%%%%%%%%%@@@%%%%%%%%-::#@%%%%%%%%%@%@%%%%%%%#:::*@%%@@@%%%%%%%%%%%@%%%%%%%%@#++*@    
  %%=+*%@*+=#%%%%%%%%@#::::-%%%%%%*::::-@%%%+---=@%%*--=#%-:::+%%%%%%%%%*-::-*%%%%%=::::---=%%%%%#-:::+%%%%%*-:::---*@@%#++@%%%%+-=%@%%%%%=++%@*+=#@  
 @*=+%@#+*-++=%%%%%%%%%::::-%%%%%%*::::-@%%=:::=%@@%=:::::=-::::*%%%%#-:-=-::::+%@==-:::===%@%*-:-=-::::=%@*=-:::-==%%=:::*@%#-:::#@%%%%*=+:***@%*++%@
 @%**+@@#*##*#@%%%%%%%%::::-%%%%%%*::::-@%%@-::=%%%@%:::+@@%*:::-@%@-::-%@@#::::=@@@=:::%@@@@=::-%@@#-:::-%@@#:::*@@@@%:::*@%@#:::#@%%%@%*###*%@#+*#@@
  %@#***@%*#@@%%%%%%%@%-----%@%%%%*:---=@%%@-::=%%%%%:::+@%@%:::-@%@-::-%%%@@-::=@%@=::-%@%%@=::-%@%@@-::-@%@#:::*@%%%@:::*@%%#::-#@%%%%%@%*%@%+**@@  
    %@#*+%@@%%%%%%%%@+-=---=%@%%%@*----=@%%@=::=%%%%%:::+@%%%:--=%%@=-=-%%%%@---=@%@=:--%@%%@=::-%%%%@---=%%@#::-*@%%%@::-*@%@#:--#@%%%%%%@@@+**%@    
      @@#**#%%%%@%%%%@@#-=-=%@%%%@*--=-=@%%@=-=+%%%%@---*@%%%---=@%@=---@@%%@---+@%@==--%@%%@=--=%%%%@---=@%@#---*@%%@@--=*@%@#-==#@%%%%%%%+###@@     
        @%**+%%%%%%%%%@#-===%@%%@*-====#@%%@===+%%%%%-==*@%@%-==+@@@=====%@%%===+@%@=-==%@%%@+-==-#@%%-==+%@@#-==*@%%@@-==*@%@#-==#@%%%%@*+*#@@       
         %@#***%%%%%%@@#-==+%%%#-====-#@%%@@===+%%%@%-==*@%@%-==+%%@@*===--=--*%@@%@+===-=++@@*===--===+%@@%@#-==*@%%@@=-==-=-====#@%%%#+*#@@         
           @@**%%%%%@%-==*+=========#@@@%@*======#@+=====+@@@#===+%@@@@%+--+%@@@%%%@@%+-=*@@@@@@%+-=+%@@@@%%@#-==*@%%%@@%+===#@#===*@%@**%@           
            @#*%%%%@=============%%@@@%%%@@@@@@@@@@@@@@@@@@%@@@@@@@%%%@@@@@@@@%%%%%%@@@@@@@%%%%%@@@@@@@%%%%%@*===+%@%%%%@@@@@@@@@@@@@%@#*%            
            @#*%%%%@@@@@@@@@@@@@@@@%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*=%%%%%%%%%%%%%%%%%%%%%%@@%%%%%%@%==+#@@@@%%%%%@%%%%%%%%@%%%@#*%            
            @#*+*@%%%%%%%%%%%%%%%%%%%%%%#****+++++=======-----#%%-----=%#-=*-*%#-----#%%=---======+++++**%@+#@@@@%%%%%%%%%%%%%%%%%%%%%+*#@            
            @#***++++++++++++++++++++%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%@+=%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%@@@@%%@+++++++++++++++++++++**#@            
             @@@@@@@@@@@@@@@@@@@@@@%#*+***************##*******%@@@@@@@@@@@@%%@@@@@@@@@#********************#**++#%@@@@@@@@@@@@@@@@@@@@@@%            
                                    @%%%%@%@%%@@@@@@@@@@@@@@@@@@##%##################+@@@@@@@@@@@%%@@@@@@@@@%%%%%@%                                   
                                                               %**%#+#####-:#####+*##*@@                                                              
                                                               %**####=..:...:.:+####*%@                                                              
                                                               %**###:::......:=+*###*%@                                                              
                                                               %**###=#::-:=+++++*###*%@                                                              
                                                               %**%##--+-%:=+++++*###*%@                                                              
                                                               %%###%#+----=+++*#%%###@@                                                              
                                                                 @%#*#%#%#+*#%%%%*#%@%                                                                
                                                                   @@%*#%###%%#*#@@                                                                   
                                                                     %@%#*%%**%@%                                                                     
                                                                        @@##@@@                                                                       
                                                                          %@                                                                          `;

    // Si la page démarre directement en mode DinoTofu (?easter=dinotofu)
    const isDinoTofuInitial = terminalPage?.dataset.easter === "dinotofu" || asciiElem?.classList.contains("ascii-dinotofu");
    if (isDinoTofuInitial && asciiElem) {
        asciiElem.textContent = DINOTOFU_ASCII;
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
  <strong>echo</strong> [texte]            : Affiche une ligne de texte (supporte les guillemets, variables $USER, $HOME, $PATH, $0, $$...)
  <strong>clear</strong>                   : Efface l'écran du terminal
  <strong>whoami</strong>                  : Affiche l'utilisateur actuel
  <strong>pwd</strong>                     : Affiche le répertoire de travail
  <strong>fastfetch</strong> / <strong>neofetch</strong>    : Affiche les informations système et matérielles du client
  <strong>help</strong>                    : Affiche ce message d'aide

<p>Note : L'environnement est sous Linux avec le shell BASH (commandes sensibles à la casse).</p>`;
                entry.appendChild(outputElem);
                break;

            case "dinotofu": {
                const asciiElem = document.getElementById("terminal-ascii-art");
                const isAlreadyDino = asciiElem && asciiElem.classList.contains("ascii-dinotofu");

                if (isAlreadyDino) {
                    outputElem.className += " cmd-info";
                    outputElem.innerHTML = `
<div style="color: #38ef7d; font-weight: bold; margin-bottom: 0.35rem;">[Easter Egg DinoTofu]</div>
<div>L'art ASCII de DinoTofu est déjà affiché dans le terminal, cela ne sert à rien de retaper "dinotofu".</div>
<div><a href="https://github.com/SIMON-Louis-2326101aa/ProjetDinotofu" target="_blank" rel="noopener noreferrer" class="terminal-link">https://github.com/SIMON-Louis-2326101aa/ProjetDinotofu</a></div>`;
                    entry.appendChild(outputElem);
                } else {
                    // 1. Efface l'historique du terminal
                    terminalOutput.innerHTML = "";

                    // 2. Remplace l'ASCII art par DinoTofu depuis la mémoire
                    if (asciiElem) {
                        asciiElem.textContent = DINOTOFU_ASCII;
                        asciiElem.classList.add("ascii-dinotofu");
                    }

                    // 3. Met à jour la description sous l'ASCII
                    const bannerHint = document.getElementById("terminal-banner-hint");
                    if (bannerHint) {
                        bannerHint.innerHTML = `[Easter Egg : DinoTofu Edition] Système initialisé. Tapez <span class="cmd-highlight">help</span> pour l'aide ou testez la commande <span class="cmd-highlight">echo "Hello World"</span>.`;
                    }

                    // 4. Met à jour le titre de la fenêtre et la barre des tâches
                    const titleText = document.getElementById("terminal-title-text");
                    if (titleText) {
                        titleText.textContent = `CigTerm [DinoTofu] — ${userLogin}@cyber: ~`;
                    }
                    const taskbarLabel = document.getElementById("terminal-taskbar-label");
                    if (taskbarLabel) {
                        taskbarLabel.textContent = `CigTerm [DinoTofu]`;
                    }
                    if (taskbarItem) {
                        taskbarItem.title = `CigTerm [DinoTofu] — ${userLogin}@cyber (Terminal)`;
                    }

                    // 5. Synchronise l'URL du navigateur
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, "", "index.php?controller=Redirection&action=openTerminal&easter=dinotofu");
                    }

                    // 6. Affiche le message de confirmation avec le lien cliquable
                    outputElem.className += " cmd-info";
                    outputElem.innerHTML = `
<div style="color: #38ef7d; font-weight: bold; margin-bottom: 0.35rem;">[Easter Egg DinoTofu Activé]</div>
<div>Jeu DinoTofu créé par notre collègue de l'équipe CyberCigales !</div>
<div><a href="https://github.com/SIMON-Louis-2326101aa/ProjetDinotofu" target="_blank" rel="noopener noreferrer" class="terminal-link">https://github.com/SIMON-Louis-2326101aa/ProjetDinotofu</a></div>
<div style="color: #7b889b; font-size: 0.85rem; margin-top: 0.35rem;">Astuce : Pour revenir au shell et à l'ASCII standard, tapez "bash", "zsh" ou "sh".</div>`;
                    entry.appendChild(outputElem);
                }
                break;
            }

            case "bash":
            case "zsh":
            case "sh":
            case "shell": {
                const isDinoActive = asciiElem && asciiElem.classList.contains("ascii-dinotofu");

                if (isDinoActive) {
                    // 1. Efface l'historique du terminal
                    terminalOutput.innerHTML = "";

                    // 2. Restaure l'ASCII art d'origine (CyberCigales) depuis la mémoire
                    if (asciiElem) {
                        asciiElem.textContent = baseCyberAscii;
                        asciiElem.classList.remove("ascii-dinotofu");
                    }

                    // 3. Restaure le message sous l'ASCII
                    const bannerHint = document.getElementById("terminal-banner-hint");
                    if (bannerHint) {
                        bannerHint.innerHTML = `Système initialisé. Tapez <span class="cmd-highlight">help</span> pour l'aide ou testez la commande <span class="cmd-highlight">echo "Hello World"</span>.`;
                    }

                    // 4. Restaure le titre de la fenêtre et la barre des tâches
                    const titleText = document.getElementById("terminal-title-text");
                    if (titleText) {
                        titleText.textContent = `CigTerm — ${userLogin}@cyber: ~`;
                    }
                    const taskbarLabel = document.getElementById("terminal-taskbar-label");
                    if (taskbarLabel) {
                        taskbarLabel.textContent = `CigTerm — ${userLogin}@cyber: ~`;
                    }
                    if (taskbarItem) {
                        taskbarItem.title = `CigTerm — ${userLogin}@cyber (Terminal)`;
                    }

                    // 5. Synchronise l'URL du navigateur (retire le paramètre easter)
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, "", "index.php?controller=Redirection&action=openTerminal");
                    }

                    outputElem.className += " cmd-info";
                    outputElem.textContent = `Retour au shell ${cmd} standard (CigTerm v1.0). L'ASCII d'origine CyberCigales a été restauré.`;
                    entry.appendChild(outputElem);
                } else {
                    outputElem.className += " cmd-info";
                    outputElem.textContent = `Vous êtes déjà connecté au shell ${cmd} (CigTerm v1.0).`;
                    entry.appendChild(outputElem);
                }
                break;
            }

            case "whoami":
                outputElem.textContent = userLogin;
                entry.appendChild(outputElem);
                break;

            case "pwd":
                outputElem.textContent = `/home/${userLogin}`;
                entry.appendChild(outputElem);
                break;

            case "fastfetch":
            case "neofetch":
                executeFastfetch(entry, outputElem, args);
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

    /**
     * Détection du système d'exploitation client (OS)
     */
    function detectOS(override) {
        if (override) {
            const low = override.toLowerCase();
            if (low === "apple" || low === "mac" || low === "macos" || low === "ios") {
                return { isApple: true, type: "apple", name: "Apple Darwin (macOS / iOS)" };
            }
            if (low === "linux") {
                return { isApple: false, type: "linux", name: "GNU/Linux (x86_64)" };
            }
            if (low === "windows") {
                return { isApple: false, type: "windows", name: "Windows 11 (x86_64)" };
            }
            if (low === "android") {
                return { isApple: false, type: "android", name: "Android 14 (Linux Kernel)" };
            }
        }

        const ua = navigator.userAgent || "";
        const platform = (navigator.userAgentData && navigator.userAgentData.platform) || navigator.platform || "";

        // 1. Détection des produits Apple (macOS, iOS, iPadOS)
        if (/Macintosh|Mac OS X|iPhone|iPad|iPod/i.test(ua) || /Mac|iPhone|iPad|iPod/i.test(platform)) {
            return {
                isApple: true,
                type: "apple",
                name: "Apple Darwin (macOS / iOS)"
            };
        }

        // 2. Détection Android
        if (/Android/i.test(ua) || platform.toLowerCase() === "android") {
            const vMatch = ua.match(/Android\s+([0-9.]+)/i);
            const version = vMatch ? ` ${vMatch[1]}` : "";
            return {
                isApple: false,
                type: "android",
                name: `Android${version} (Linux Kernel)`
            };
        }

        // 3. Détection Windows
        if (/Windows|Win64|Win32/i.test(ua) || /Win/i.test(platform)) {
            let winVer = "10 / 11";
            if (/Windows NT 10\.0/i.test(ua)) {
                winVer = "10 / 11";
            } else if (/Windows NT 6\.3/i.test(ua)) {
                winVer = "8.1";
            } else if (/Windows NT 6\.2/i.test(ua)) {
                winVer = "8";
            } else if (/Windows NT 6\.1/i.test(ua)) {
                winVer = "7";
            } else if (/Windows NT 6\.0/i.test(ua)) {
                winVer = "Vista";
            } else if (/Windows NT 5\.1/i.test(ua)) {
                winVer = "XP";
            }
            const arch = /WOW64|Win64|x64/i.test(ua) ? "x86_64" : "x86";
            return {
                isApple: false,
                type: "windows",
                name: `Windows ${winVer} (${arch})`
            };
        }

        // 4. Détection Linux
        if (/Linux|X11/i.test(ua) || /Linux/i.test(platform)) {
            let distro = "GNU/Linux";
            if (/Ubuntu/i.test(ua)) distro = "Ubuntu Linux";
            else if (/Fedora/i.test(ua)) distro = "Fedora Linux";
            else if (/Debian/i.test(ua)) distro = "Debian Linux";
            else if (/Arch/i.test(ua)) distro = "Arch Linux";
            else if (/CrOS/i.test(ua)) distro = "ChromeOS";

            const isArm = /aarch64|armv/i.test(ua);
            const arch = isArm ? "aarch64" : "x86_64";
            return {
                isApple: false,
                type: "linux",
                name: `${distro} (${arch})`
            };
        }

        // 5. Fallback générique
        return {
            isApple: false,
            type: "generic",
            name: platform || "Système Inconnu"
        };
    }

    /**
     * Détection du navigateur et modèle d'appareil
     */
    function detectBrowser() {
        const ua = navigator.userAgent || "";
        let name = "Navigateur Web";
        let version = "";
        let engine = "Blink";

        if (/Firefox\/([0-9.]+)/i.test(ua)) {
            name = "Mozilla Firefox";
            version = ua.match(/Firefox\/([0-9.]+)/i)[1];
            engine = "Gecko";
        } else if (/Edg\/([0-9.]+)/i.test(ua)) {
            name = "Microsoft Edge";
            version = ua.match(/Edg\/([0-9.]+)/i)[1];
        } else if (/OPR\/([0-9.]+)/i.test(ua)) {
            name = "Opera";
            version = ua.match(/OPR\/([0-9.]+)/i)[1];
        } else if (/Vivaldi\/([0-9.]+)/i.test(ua)) {
            name = "Vivaldi";
            version = ua.match(/Vivaldi\/([0-9.]+)/i)[1];
        } else if (/Chrome\/([0-9.]+)/i.test(ua)) {
            name = "Google Chrome / Chromium";
            version = ua.match(/Chrome\/([0-9.]+)/i)[1];
        } else if (/Safari\/([0-9.]+)/i.test(ua)) {
            name = "Apple Safari";
            const v = ua.match(/Version\/([0-9.]+)/i);
            version = v ? v[1] : "";
            engine = "WebKit";
        }

        let deviceModel = "";
        if (/Android/i.test(ua)) {
            const m = ua.match(/Android[^;]+;\s*([^;)]+)/i);
            if (m && m[1] && !/K|Build/i.test(m[1])) {
                deviceModel = m[1].trim();
            }
        }

        return {
            fullName: version ? `${name} ${version}` : name,
            engine,
            deviceModel
        };
    }

    /**
     * Retourne l'ASCII Art officiel du système sélectionné
     */
    function getOSLogo(type) {
        if (type === "linux") {
            return [
                "    .--.   ",
                "   |<span class=\"tux-accent\">o_o</span> |  ",
                "   |<span class=\"tux-accent\">:_/</span> |  ",
                "  //   \\ \\ ",
                " (|     | )",
                "/'\\_   _/`\\",
                "\\<span class=\"tux-accent\">___)=(___</span>/"
            ].join("\n");
        }
        if (type === "windows") {
            return [
                "  ███████████   ███████████",
                "  ███████████   ███████████",
                "  ███████████   ███████████",
                "  ███████████   ███████████",
                "",
                "  ███████████   ███████████",
                "  ███████████   ███████████",
                "  ███████████   ███████████",
                "  ███████████   ███████████"
            ].join("\n");
        }
        if (type === "android") {
            return [
                "         -o          o-       ",
                "          +hydNNNNdyh+        ",
                "        +mMMMMMMMMMMMMm+      ",
                "      `dMMm:NMMMMMMN:mMMd`    ",
                "      hMMMMMMMMMMMMMMMMMMh    ",
                "  ..  yyyyyyyyyyyyyyyyyyyy  ..",
                ".mMMm`MMMMMMMMMMMMMMMMMMMM`mMMm.",
                ":MMMM-MMMMMMMMMMMMMMMMMMMM-MMMM:",
                ":MMMM-MMMMMMMMMMMMMMMMMMMM-MMMM:",
                ":MMMM-MMMMMMMMMMMMMMMMMMMM-MMMM:",
                "-MMMM-MMMMMMMMMMMMMMMMMMMM-MMMM-",
                " +yy+ MMMMMMMMMMMMMMMMMMMM +yy+ ",
                "      mMMMMMMMMMMMMMMMMMMm    "
            ].join("\n");
        }
        return [
            "      .------------.     ",
            "     /              \\    ",
            "    |   ┌────────┐   |   ",
            "    |   │ >_     │   |   ",
            "    |   │ CIGTERM│   |   ",
            "    |   └────────┘   |   ",
            "     \\              /    ",
            "      '------------'     "
        ].join("\n");
    }

    /**
     * Exécute fastfetch / neofetch avec récupération des métriques matérielles et de l'environnement client
     */
    function executeFastfetch(entry, outputElem, args) {
        const cleanArgs = (args || "").trim();
        if (cleanArgs === "--help" || cleanArgs === "-h") {
            outputElem.className += " cmd-info";
            outputElem.innerHTML = `
<strong>Usage :</strong> fastfetch [os_preview]
Affiche les spécifications système, logicielles et matérielles détectées côté client.

<strong>Arguments optionnels pour prévisualisation :</strong>
  <strong>linux</strong>   : Prévisualiser le logo Linux (Tux)
  <strong>windows</strong> : Prévisualiser le logo Microsoft Windows
  <strong>android</strong> : Prévisualiser le logo Android (Bugdroid)`;
            entry.appendChild(outputElem);
            return;
        }

        const osInfo = detectOS(cleanArgs);

        // Si l'utilisateur est sur un appareil Apple (macOS ou iOS), afficher le message en attente
        if (osInfo.isApple) {
            outputElem.className += " cmd-info";
            outputElem.innerHTML = `
<div style="color: #f9e2af; font-weight: bold; margin-bottom: 0.35rem;">[fastfetch / neofetch] Détection Apple</div>
<div>Système détecté : <strong style="color: #ffffff;">${escapeHtml(osInfo.name)}</strong></div>
<div style="margin-top: 0.35rem; color: #a6adc8;">La prise en charge des métriques et des logos pour l'écosystème Apple (macOS &amp; iOS) est temporairement suspendue et sera intégrée ultérieurement.</div>
<div style="color: #7b889b; font-size: 0.85rem; margin-top: 0.35rem;">Astuce : Testez la commande depuis une machine Linux, Windows ou un appareil Android pour visualiser le logo et les statistiques complètes.</div>`;
            entry.appendChild(outputElem);
            return;
        }

        const browserInfo = detectBrowser();
        const screenW = window.screen.width;
        const screenH = window.screen.height;
        const dpr = window.devicePixelRatio || 1;
        const colorDepth = window.screen.colorDepth || 24;

        // Uptime de la session terminal
        const uptimeSec = Math.floor((Date.now() - pageStartTime) / 1000);
        const uptimeHrs = Math.floor(uptimeSec / 3600);
        const uptimeMins = Math.floor((uptimeSec % 3600) / 60);
        const uptimeRemainingSec = uptimeSec % 60;
        const uptimeParts = [];
        if (uptimeHrs > 0) uptimeParts.push(`${uptimeHrs} hour${uptimeHrs > 1 ? "s" : ""}`);
        if (uptimeMins > 0 || uptimeHrs > 0) uptimeParts.push(`${uptimeMins} min${uptimeMins > 1 ? "s" : ""}`);
        uptimeParts.push(`${uptimeRemainingSec} sec${uptimeRemainingSec > 1 ? "s" : ""}`);
        const uptimeStr = uptimeParts.join(", ");

        // Kernel
        let kernelStr = "CigTerm Virtual Kernel 6.1 (JS)";
        if (osInfo.type === "linux") {
            kernelStr = "Linux (Navigateur WebAssembly/JS)";
        } else if (osInfo.type === "windows") {
            kernelStr = "Windows NT (Navigateur Isolé)";
        } else if (osInfo.type === "android") {
            kernelStr = "Linux Kernel (Android Runtime)";
        }

        // Host
        const hostStr = browserInfo.deviceModel
            ? `${browserInfo.deviceModel} — ${browserInfo.fullName}`
            : `${browserInfo.fullName} (Moteur ${browserInfo.engine})`;

        // CPU & Mémoire
        const cpuCores = navigator.hardwareConcurrency ? `${navigator.hardwareConcurrency} (cœurs/threads logiques)` : "Non divulgué";
        const memGiB = navigator.deviceMemory ? `~${navigator.deviceMemory} GiB` : "Non divulgué (sécurité navigateur)";

        // Locale & Timezone
        const locale = navigator.language || "fr-FR";
        let tz = "";
        try {
            tz = Intl.DateTimeFormat().resolvedOptions().timeZone || "";
        } catch (_) {}
        const localeStr = tz ? `${locale} (${tz})` : locale;

        // Titre et séparateur
        const titleUserHost = `${userLogin}@cybercigales`;
        const separator = "-".repeat(titleUserHost.length);

        // Logo ASCII
        const logoData = getOSLogo(osInfo.type);

        // Palette ANSI
        const standardColors = ["#1e1e2e", "#f38ba8", "#a6e3a1", "#f9e2af", "#89b4fa", "#cba6f7", "#89dceb", "#cdd6f4"];
        const brightColors = ["#585b70", "#eba0ac", "#94e2d5", "#fab387", "#74c7ec", "#f5c2e7", "#b4befe", "#ffffff"];
        const renderPaletteRow = (colors) => colors.map(c => `<span style="color:${c};">███</span>`).join(" ");

        outputElem.className += " cmd-fastfetch";
        outputElem.innerHTML = `
<div class="fastfetch-wrapper">
    <div class="fastfetch-logo fastfetch-logo-${osInfo.type}">
        <pre>${logoData}</pre>
    </div>
    <div class="fastfetch-content">
        <div class="fastfetch-title">
            <span class="fastfetch-user">${escapeHtml(userLogin)}</span><span class="fastfetch-at">@</span><span class="fastfetch-host">cybercigales</span>
        </div>
        <div class="fastfetch-separator">${separator}</div>
        <div class="fastfetch-row"><span class="fastfetch-key">OS:</span><span class="fastfetch-val">${escapeHtml(osInfo.name)}</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Host:</span><span class="fastfetch-val">${escapeHtml(hostStr)}</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Kernel:</span><span class="fastfetch-val">${escapeHtml(kernelStr)}</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Uptime:</span><span class="fastfetch-val">${escapeHtml(uptimeStr)}</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Packages:</span><span class="fastfetch-val">9 (CigTerm Builtins)</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Shell:</span><span class="fastfetch-val">bash 5.3.20-1 (CigTerm)</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Display:</span><span class="fastfetch-val">${screenW}x${screenH} @ ${dpr}x (${colorDepth}-bit)</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Terminal:</span><span class="fastfetch-val">CigTerm (xterm-kitty / DOM)</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">CPU:</span><span class="fastfetch-val">${escapeHtml(cpuCores)}</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Memory:</span><span class="fastfetch-val">${escapeHtml(memGiB)}</span></div>
        <div class="fastfetch-row"><span class="fastfetch-key">Locale:</span><span class="fastfetch-val">${escapeHtml(localeStr)}</span></div>
        <div class="fastfetch-palette">
            <div class="palette-row">${renderPaletteRow(standardColors)}</div>
            <div class="palette-row">${renderPaletteRow(brightColors)}</div>
        </div>
    </div>
</div>`;
        entry.appendChild(outputElem);
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
