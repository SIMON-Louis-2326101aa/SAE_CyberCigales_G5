(function () {
    'use strict';

    function el(id)   { return document.getElementById(id); }
    function show(id) { el(id).classList.remove('hidden'); }
    function hide(id) { el(id).classList.add('hidden'); }

    let isFollowing  = false;
    let modalLiked   = false;
    let currentPost  = null;
    let currentDecoy = null; // compte leurre actuellement affiché

    const TARGET = (typeof IG_TARGET !== 'undefined') ? IG_TARGET : {};

    // Si le bot a déjà répondu (session active), rouvrir le bon profil + le DM directement
    if (TARGET.botReplied) {
        _showRealProfile();
        igOpenDm();
    }

    // ════════════════════════════════════════════════════════
    //  RECHERCHE — vrai compte + leurres mélangés
    // ════════════════════════════════════════════════════════
    window.igOnSearch = function (val) {
        const dropdown = el('igSearchDropdown');
        val = val.trim().toLowerCase();

        if (val.length < 2) { dropdown.classList.add('hidden'); return; }

        const allAccounts = [
            { handle: TARGET.handle, name: TARGET.name, letter: TARGET.letter, isReal: true },
            ...(TARGET.decoyAccounts || []).map(d => ({ ...d, isReal: false }))
        ];

        const matches = allAccounts.filter(a =>
            a.handle.toLowerCase().startsWith(val) ||
            a.name.toLowerCase().startsWith(val)
        );

        // Mélanger pour que le bon compte ne soit pas toujours en premier
        matches.sort(() => Math.random() - 0.5);

        if (matches.length === 0) {
            dropdown.innerHTML = `<div class="ig-search-empty">Aucun résultat pour « ${val} »</div>`;
        } else {
            dropdown.innerHTML = matches.map(a => `
                <div class="ig-search-item" onclick="igSelectAccount('${a.handle}', ${a.isReal})">
                    <div class="ig-search-avatar">${a.letter}</div>
                    <div>
                        <div class="ig-search-name">${a.handle}</div>
                        <div class="ig-search-sub">${a.name}</div>
                    </div>
                </div>`
            ).join('');
        }

        dropdown.classList.remove('hidden');
    };

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.ig-search-wrap')) {
            el('igSearchDropdown').classList.add('hidden');
        }
    });

    // ════════════════════════════════════════════════════════
    //  SÉLECTION D'UN COMPTE
    // ════════════════════════════════════════════════════════
    window.igSelectAccount = function (handle, isReal) {
        el('igSearchDropdown').classList.add('hidden');
        el('igSearchInput').value = '';

        if (isReal) {
            _showRealProfile();
        } else {
            const decoy = (TARGET.decoyAccounts || []).find(d => d.handle === handle);
            if (decoy) _showDecoyProfile(decoy);
        }
    };

    function _showRealProfile() {
        hide('ig-home-state');
        hide('ig-decoy-state');
        show('ig-profile-state');

        el('igProfileGrid').innerHTML = TARGET.posts
            .map(p => `
                <div class="ig-grid-item" onclick="igOpenModal('${p.id}')">
                    <span class="ig-grid-emoji">${p.emoji}</span>
                </div>`)
            .join('');
    }

    function _showDecoyProfile(decoy) {
        currentDecoy = decoy;
        hide('ig-home-state');
        hide('ig-profile-state');
        show('ig-decoy-state');

        el('igDecoyLetter').textContent    = decoy.letter;
        el('igDecoyHandle').textContent    = decoy.handle;
        el('igDecoyName').textContent      = decoy.name;
        el('igDecoyBio').textContent       = decoy.bio;
        el('igDecoyFollowers').textContent = decoy.followers;
    }

    window.igGoHome = function () {
        hide('ig-profile-state');
        hide('ig-decoy-state');
        show('ig-home-state');
        el('igSearchInput').value = '';
        el('igSearchDropdown').classList.add('hidden');
    };

    window.igBackToSearch = function () {
        hide('ig-decoy-state');
        show('ig-home-state');
        currentDecoy = null;
        el('igSearchInput').focus();
    };

    // ════════════════════════════════════════════════════════
    //  FOLLOW
    // ════════════════════════════════════════════════════════
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

    //DM du bon compte
    window.igOpenDm = function () {
        show('igDmOverlay');
        const msgs = el('igDmMessages');
        if (msgs) msgs.scrollTop = msgs.scrollHeight;
    };

    window.igCloseDm = function () {
        hide('igDmOverlay');
    };

    window.igSendDmMessage = function () {
        const input = el('igDmInput');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;

        const msgs = el('igDmMessages');

        // Bulle envoyée par le joueur
        const bubbleMe = document.createElement('div');
        bubbleMe.className = 'ig-dm-bubble ig-dm-bubble--me';
        bubbleMe.textContent = text;
        msgs.appendChild(bubbleMe);

        input.value = '';
        msgs.scrollTop = msgs.scrollHeight;

        // Envoi au serveur via fetch (la réponse reste côté PHP)
        const formData = new FormData();
        formData.append('message', text);

        fetch('index.php?controller=Puzzle&action=sendDmMessage', {
            method: 'POST',
            body: formData
        })
            .then(r => r.json())
            .then(data => {
                const wrap = document.createElement('div');
                wrap.className = 'ig-dm-bubble-wrap';

                const avatar = document.createElement('div');
                avatar.className = 'ig-dm-bubble-avatar';
                avatar.textContent = TARGET.letter || '?';

                const bubble = document.createElement('div');
                bubble.className = 'ig-dm-bubble ig-dm-bubble--them';
                bubble.textContent = data.reply;

                wrap.appendChild(avatar);
                wrap.appendChild(bubble);
                msgs.appendChild(wrap);
                msgs.scrollTop = msgs.scrollHeight;

                // Si le bot a répondu avec le bon mot-clé, afficher le bouton de validation
                if (data.botReplied) {
                    el('igDmInput').closest('.ig-dm-input-wrap').remove();
                    const validateWrap = document.createElement('div');
                    validateWrap.className = 'ig-dm-validate-wrap';
                    validateWrap.innerHTML = `
                    <p class="ig-dm-validate-hint">📍 Tu as reçu la localisation ! Valide l'épreuve :</p>
                    <form action="index.php?controller=Puzzle&action=validateSocialMedia" method="POST"
                          class="ig-dm-validate-form">
                        <button type="submit" class="ig-dm-validate-btn">✓ Valider l'épreuve</button>
                    </form>`;
                    el('igDmOverlay').querySelector('.ig-dm-panel').appendChild(validateWrap);
                }
            });
    };

    //DM leurres
    window.igOpenDecoyDm = function (handle, letter, name) {
        // Accepte les infos passées directement depuis le bouton HTML
        // ou depuis currentDecoy si disponible
        const h = handle || (currentDecoy && currentDecoy.handle) || '—';
        const l = letter || (currentDecoy && currentDecoy.letter) || '?';

        el('igDecoyDmAvatar').textContent      = l;
        el('igDecoyDmHandle').textContent      = h;
        el('igDecoyDmHandleInfo').textContent  = h;

        // Réinitialiser les bulles (hors info-bubble)
        const msgs = el('igDecoyDmMessages');
        msgs.querySelectorAll('.ig-dm-bubble, .ig-dm-bubble-wrap, .ig-phishing-reply').forEach(e => e.remove());

        show('igDecoyDmInputWrap');
        el('igDecoyDmInput').value = '';

        show('igDecoyDmOverlay');
        msgs.scrollTop = msgs.scrollHeight;
    };

    window.igCloseDecoyDm = function () {
        hide('igDecoyDmOverlay');
    };

    window.igSendDecoyMessage = function () {
        const input = el('igDecoyDmInput');
        const text  = input.value.trim();
        if (!text) return;

        const msgs = el('igDecoyDmMessages');

        // Bulle envoyée par le joueur
        const bubbleMe = document.createElement('div');
        bubbleMe.className = 'ig-dm-bubble ig-dm-bubble--me';
        bubbleMe.textContent = text;
        msgs.appendChild(bubbleMe);

        input.value = '';

        // Réponse automatique du bot leurre après un court délai (simuler la frappe)
        setTimeout(() => {
            const wrap = document.createElement('div');
            wrap.className = 'ig-dm-bubble-wrap';

            const avatar = document.createElement('div');
            avatar.className = 'ig-dm-bubble-avatar';
            avatar.textContent = el('igDecoyDmAvatar').textContent || '?';

            const bubble = document.createElement('div');
            bubble.className = 'ig-dm-bubble ig-dm-bubble--them ig-dm-bubble--phishing';
            bubble.innerHTML = `Salut ! Clique sur ce lien pour qu'on se retrouve 👉 <span class="ig-fake-link">bit.ly/r3nd3zv0us-secret</span> 😊`;

            wrap.appendChild(avatar);
            wrap.appendChild(bubble);
            msgs.appendChild(wrap);

            // Laisser la saisie active (le joueur peut continuer à écrire)
            // Pas d'avertissement — le piège doit rester crédible

            msgs.scrollTop = msgs.scrollHeight;
        }, 900);

        msgs.scrollTop = msgs.scrollHeight;
    };

    // ════════════════════════════════════════════════════════
    //  POST MODAL
    // ════════════════════════════════════════════════════════
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
        const btn   = el('igModalLikeBtn');
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

    function igGoHome() {
        // Ferme tous les overlays/panels ouverts
        document.getElementById('igDmOverlay')?.classList.add('hidden');
        document.getElementById('igDecoyDmOverlay')?.classList.add('hidden');
        document.getElementById('igModal')?.classList.add('hidden');
        // Masque la vue profil et affiche le feed
        const profileState = document.getElementById('ig-profile-state');
        if (profileState) profileState.classList.add('hidden');
        const homeState = document.getElementById('ig-home-state');
        if (homeState) homeState.classList.remove('hidden');
        // Vide la recherche
        const searchInput = document.getElementById('igSearchInput');
        if (searchInput) searchInput.value = '';
        const dropdown = document.getElementById('igSearchDropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }


})();