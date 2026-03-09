<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$team = $_SESSION['team'] ?? 'alice';

if ($team === 'alice') {
    // ── Cible réelle ──
    $targetName      = 'Bob Valmont';
    $targetHandle    = 'bob.valmont';
    $targetBio       = "Passionné d'énigmes 💻\nLe passé cache souvent les clés du futur.";
    $targetLetter    = 'B';
    $targetFollowers = 142;
    $myLetter        = 'A';
    $myHandle        = 'alice.valmont';

    // ── Faux comptes leurres ──
    $decoyAccounts = json_encode([
            ['handle' => 'bob_martin',    'name' => 'Bob Martin',    'letter' => 'B',
                    'followers' => 89,  'bio' => "Passionné de sport 🏃 Lyon"],
            ['handle' => 'bob.leblanc',   'name' => 'Bob Leblanc',   'letter' => 'B',
                    'followers' => 214, 'bio' => "Développeur web 🖥️ Paris"],
            ['handle' => 'bobby.photos',  'name' => 'Bobby Richard', 'letter' => 'B',
                    'followers' => 57,  'bio' => "Photographe amateur 📷"],
            ['handle' => 'bob_aventures', 'name' => 'Bob Durand',    'letter' => 'B',
                    'followers' => 331, 'bio' => "Voyageur passionné ✈️"],
    ]);

    $posts = json_encode([
            [
                    'id'       => 'b1',
                    'emoji'    => '🔐',
                    'caption'  => "Chaque cadenas a sa clé… Le passé ne disparaît jamais vraiment 🗝️",
                    'likes'    => 248,
                    'location' => 'Paris, France',
                    'time'     => 'IL Y A 3 JOURS',
                    'comments' => [
                            ['user' => 'marie_p',   'text' => 'Trop cryptique 😂'],
                            ['user' => 'lucas_dev', 'text' => 'Intéressant...'],
                    ],
            ],
            [
                    'id'       => 'b2',
                    'emoji'    => '💻',
                    'caption'  => "Mon setup pour le week-end. Belle session de code en perspective 🖥️",
                    'likes'    => 183,
                    'location' => 'Télétravail',
                    'time'     => 'IL Y A 5 JOURS',
                    'comments' => [
                            ['user' => 'alice_v',  'text' => 'Beau setup !'],
                            ['user' => 'thomas_k', 'text' => 'Quelle config !'],
                    ],
            ],
            [
                    'id'       => 'b3',
                    'emoji'    => '🌆',
                    'caption'  => "Coucher de soleil sur Paris. On voit tout depuis ici… même 
                    les secrets bien gardés 🌅",
                    'likes'    => 312,
                    'location' => 'Tour Eiffel, Paris',
                    'time'     => 'IL Y A 1 SEMAINE',
                    'comments' => [
                            ['user' => 'sophie_m', 'text' => 'Magnifique 😍'],
                            ['user' => 'jules_b',  'text' => 'Quelle vue !'],
                    ],
            ],
    ]);
} else {
    // ── Cible réelle ──
    $targetName      = 'Alice Valmont';
    $targetHandle    = 'alice.valmont';
    $targetBio       = "Photographie et secrets de famille 📸\nL'union fait la force.";
    $targetLetter    = 'A';
    $targetFollowers = 198;
    $myLetter        = 'B';
    $myHandle        = 'bob.valmont';

    // ── Faux comptes leurres ──
    $decoyAccounts = json_encode([
            ['handle' => 'alice_martin',  'name' => 'Alice Martin',  'letter' =>
                    'A', 'followers' => 103, 'bio' => "Étudiante en droit ⚖️ Paris"],
            ['handle' => 'alice.photo',   'name' => 'Alice Girard',  'letter' =>
                    'A', 'followers' => 476, 'bio' => "Photographe professionnelle 📷 Lyon"],
            ['handle' => 'alicedupont__', 'name' => 'Alice Dupont',  'letter' =>
                    'A', 'followers' => 62,  'bio' => "Passionnée de voyages ✈️"],
            ['handle' => 'alice_cuisine', 'name' => 'Alice Bernard', 'letter' =>
                    'A', 'followers' => 289, 'bio' => "Cheffe cuisinière 🍳 Bordeaux"],
    ]);

    $posts = json_encode([
            [
                    'id'       => 'a1',
                    'emoji'    => '📸',
                    'caption'  => "La lumière révèle tout. Surtout les choses qu'on croit cachées ✨",
                    'likes'    => 317,
                    'location' => 'Studio photo, Lyon',
                    'time'     => 'IL Y A 2 JOURS',
                    'comments' => [
                            ['user' => 'pierre_l', 'text' => 'Superbe cliché !'],
                            ['user' => 'emma_r',   'text' => 'Trop beau 💕'],
                    ],
            ],
            [
                    'id'       => 'a2',
                    'emoji'    => '🌸',
                    'caption'  => "Le printemps arrive enfin 🌸 Les fleurs comme les secrets 
                    ont besoin de temps pour éclore.",
                    'likes'    => 204,
                    'location' => "Parc de la Tête d'Or, Lyon",
                    'time'     => 'IL Y A 4 JOURS',
                    'comments' => [
                            ['user' => 'bob_v',   'text' => 'Très joli !'],
                            ['user' => 'carla_f', 'text' => 'Tu sors souvent dans ce parc ?'],
                    ],
            ],
            [
                    'id'       => 'a3',
                    'emoji'    => '🏛️',
                    'caption'  => "Architecture et mystère. Les vieux murs ont toujours une histoire à raconter 🏛️",
                    'likes'    => 156,
                    'location' => 'Vieux-Lyon, France',
                    'time'     => 'IL Y A 2 SEMAINES',
                    'comments' => [
                            ['user' => 'martin_d', 'text' => 'La lumière est parfaite !'],
                            ['user' => 'lea_c',    'text' => "J'adore cette ruelle !"],
                    ],
            ],
    ]);
}

// Historique DM + état bot
if (!isset($_SESSION['ig_messages'])) {
    $_SESSION['ig_messages'] = [];
}
$savedMessages = $_SESSION['ig_messages'];
$botReplied    = $_SESSION['ig_bot_replied'] ?? false;
$botReplyText  = $_SESSION['ig_bot_reply_text'] ?? '';
?>

<div class="ig-game-window">


    <header class="header ig-game-header">
        <nav class="header__content">
            <span class="ig-logo-text">Instagram</span>

            <div class="ig-search-wrap">
                <svg class="ig-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                        type="text"
                        id="igSearchInput"
                        class="ig-search-input"
                        placeholder="Rechercher <?= htmlspecialchars(explode(' ', $targetName)[0]) ?>..."
                        autocomplete="off"
                        oninput="igOnSearch(this.value)"
                        onfocus="igOnSearch(this.value)"
                />
                <div class="ig-search-dropdown hidden" id="igSearchDropdown"></div>
            </div>

            <div class="ig-header-right">
                <button class="ig-dm-header-btn hidden" id="igDmHeaderBtn" onclick="igOpenDm()" title="Messages">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <?php if ($botReplied) : ?>
                        <span class="ig-dm-notif"></span>
                    <?php endif; ?>
                </button>
                <div class="ig-avatar-mini"><?= htmlspecialchars($myLetter) ?></div>
            </div>
        </nav>
    </header>

    <!--menu-->
    <main class="ig-game-main">

        <!-- État accueil avec le feed-->
        <div id="ig-home-state" class="ig-feed-layout">

            <!-- Feed -->
            <div class="ig-feed-col">

                <!-- Stories -->
                <div class="ig-stories-bar">
                    <div class="ig-story"><div class="ig-story-avatar ig-story-active">M</div><span>marie_p</span></div>
                    <div class="ig-story"><div class="ig-story-avatar ig-story-active">L</div>
                        <span>lucas_dev</span></div>
                    <div class="ig-story"><div class="ig-story-avatar ig-story-active">S</div>
                        <span>sophie_m</span></div>
                    <div class="ig-story"><div class="ig-story-avatar">T</div><span>thomas_k</span></div>
                    <div class="ig-story"><div class="ig-story-avatar">J</div><span>jules_b</span></div>
                    <div class="ig-story"><div class="ig-story-avatar">E</div><span>emma_r</span></div>
                </div>

                <!-- Post 1 -->
                <div class="ig-feed-post">
                    <div class="ig-feed-post-header">
                        <div class="ig-feed-avatar">M</div>
                        <div class="ig-feed-post-info">
                            <span class="ig-feed-username">marie_p</span>
                            <span class="ig-feed-location">Paris, France</span>
                        </div>
                        <span class="ig-feed-more">···</span>
                    </div>
                    <div class="ig-feed-img">🌅</div>
                    <div class="ig-feed-post-footer">
                        <div class="ig-feed-actions">
                            <button class="ig-action-btn">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78
                                    7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>
                            <button class="ig-action-btn">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1
                                    2-2h14a2 2 0 0 1 2 2z"/></svg></button>
                            <button class="ig-action-btn" style="margin-left:auto">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2
                                     2 0 0 1 2 2z"/></svg></button>
                        </div>
                        <div class="ig-feed-likes"><strong>312 J'aime</strong></div>
                        <div class="ig-feed-caption"><strong>marie_p</strong> Coucher de soleil incroyable
                            ce soir 😍 #paris #sunset</div>
                        <div class="ig-feed-time">IL Y A 2 HEURES</div>
                    </div>
                </div>

                <!-- Post 2 -->
                <div class="ig-feed-post">
                    <div class="ig-feed-post-header">
                        <div class="ig-feed-avatar" style="background:linear-gradient(45deg,#11998e,#38ef7d)">L</div>
                        <div class="ig-feed-post-info">
                            <span class="ig-feed-username">lucas_dev</span>
                            <span class="ig-feed-location">Télétravail</span>
                        </div>
                        <span class="ig-feed-more">···</span>
                    </div>
                    <div class="ig-feed-img" style="font-size:60px">💻</div>
                    <div class="ig-feed-post-footer">
                        <div class="ig-feed-actions">
                            <button class="ig-action-btn"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                               stroke="currentColor" stroke-width="1.5">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5
                                     0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg></button>
                            <button class="ig-action-btn"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                               stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1
                                    2-2h14a2 2 0 0 1 2 2z"/></svg></button>
                            <button class="ig-action-btn" style="margin-left:auto">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2
                                     2 0 0 1 2 2z"/></svg></button>
                        </div>
                        <div class="ig-feed-likes"><strong>183 J'aime</strong></div>
                        <div class="ig-feed-caption"><strong>lucas_dev</strong> Nouvelle stack en production
                            🚀 #coding #dev</div>
                        <div class="ig-feed-time">IL Y A 5 HEURES</div>
                    </div>
                </div>

                <!-- Post 3 -->
                <div class="ig-feed-post">
                    <div class="ig-feed-post-header">
                        <div class="ig-feed-avatar" style="background:linear-gradient(45deg,#f7971e,#ffd200)">S</div>
                        <div class="ig-feed-post-info">
                            <span class="ig-feed-username">sophie_m</span>
                            <span class="ig-feed-location">Lyon, France</span>
                        </div>
                        <span class="ig-feed-more">···</span>
                    </div>
                    <div class="ig-feed-img" style="font-size:60px">🌸</div>
                    <div class="ig-feed-post-footer">
                        <div class="ig-feed-actions">
                            <button class="ig-action-btn">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.5">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78
                                     7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>
                            <button class="ig-action-btn">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2
                                     2 0 0 1 2 2z"/></svg></button>
                            <button class="ig-action-btn" style="margin-left:auto">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg></button>
                        </div>
                        <div class="ig-feed-likes"><strong>248 J'aime</strong></div>
                        <div class="ig-feed-caption"><strong>sophie_m</strong> Le printemps est là enfin
                            🌸 #nature #spring</div>
                        <div class="ig-feed-time">IL Y A 1 JOUR</div>
                    </div>
                </div>

            </div>

            <div class="ig-sidebar">
                <div class="ig-sidebar-me">
                    <div class="ig-sidebar-avatar"><?= htmlspecialchars($myLetter) ?></div>
                    <div class="ig-sidebar-me-info">
                        <span class="ig-sidebar-handle"><?= htmlspecialchars($myHandle) ?></span>
                        <span class="ig-sidebar-name"><?= htmlspecialchars($myHandle) ?></span>
                    </div>
                </div>

                <!-- Suggestions -->
                <div class="ig-sidebar-section-title">
                    <span>Suggestions pour vous</span>
                    <a class="ig-sidebar-see-all">Tout voir</a>
                </div>
                <div class="ig-sidebar-suggestions">
                    <div class="ig-sidebar-suggest">
                        <div class="ig-suggest-avatar" style="background:linear-gradient(45deg,#667eea,#764ba2)">T</div>
                        <div class="ig-suggest-info"><span>thomas_k</span><small>Suivi par marie_p</small></div>
                        <button class="ig-suggest-follow">Suivre</button>
                    </div>
                    <div class="ig-sidebar-suggest">
                        <div class="ig-suggest-avatar" style="background:linear-gradient(45deg,#f093fb,#f5576c)">E</div>
                        <div class="ig-suggest-info"><span>emma_r</span><small>Suivi par lucas_dev</small></div>
                        <button class="ig-suggest-follow">Suivre</button>
                    </div>
                    <div class="ig-sidebar-suggest">
                        <div class="ig-suggest-avatar" style="background:linear-gradient(45deg,#4facfe,#00f2fe)">J</div>
                        <div class="ig-suggest-info"><span>jules_b</span><small>Suivi par sophie_m</small></div>
                        <button class="ig-suggest-follow">Suivre</button>
                    </div>
                    <div class="ig-sidebar-suggest">
                        <div class="ig-suggest-avatar" style="background:linear-gradient(45deg,#43e97b,#38f9d7)">P</div>
                        <div class="ig-suggest-info"><span>pierre_l</span><small>Suivi par emma_r</small></div>
                        <button class="ig-suggest-follow">Suivre</button>
                    </div>
                    <div class="ig-sidebar-suggest">
                        <div class="ig-suggest-avatar" style="background:linear-gradient(45deg,#fa709a,#fee140)">C</div>
                        <div class="ig-suggest-info"><span>carla_f</span><small>Suivi par thomas_k</small></div>
                        <button class="ig-suggest-follow">Suivre</button>
                    </div>
                </div>

                <div class="ig-sidebar-footer">
                    À propos · Aide · Presse · API · Emplois · Confidentialité · Conditions<br>
                    Langue · © 2025 INSTAGRAM FROM META
                </div>
            </div>

        </div>

        <!-- État profil réel -->
        <div id="ig-profile-state" class="hidden">
            <div class="ig-profile-header">
                <div class="ig-profile-avatar-ring">
                    <div class="ig-profile-avatar-letter"><?= htmlspecialchars($targetLetter) ?></div>
                </div>
                <div class="ig-profile-info">
                    <h2 class="ig-profile-handle"><?= htmlspecialchars($targetHandle) ?></h2>
                    <ul class="ig-profile-stats">
                        <li><strong>3</strong> publications</li>
                        <li><strong id="igFollowersCount"><?= $targetFollowers ?></strong> abonnés</li>
                        <li><strong>28</strong> abonnements</li>
                    </ul>
                    <div class="ig-profile-bio">
                        <strong><?= htmlspecialchars($targetName) ?></strong><br>
                        <?= nl2br(htmlspecialchars($targetBio)) ?>
                    </div>
                    <div class="ig-profile-actions">
                        <button class="ig-btn-follow" id="igFollowBtn" onclick="igToggleFollow()">Suivre</button>
                        <button class="ig-btn-message" onclick="igOpenDm()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            Message
                        </button>
                    </div>
                </div>
            </div>
            <div class="ig-profile-tabs">
                <span class="active">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    PUBLICATIONS
                </span>
            </div>
            <div class="ig-profile-grid" id="igProfileGrid"></div>
        </div>

        <!-- État profil leurre -->
        <div id="ig-decoy-state" class="hidden">
            <div class="ig-profile-header">
                <div class="ig-profile-avatar-ring ig-decoy-ring">
                    <div class="ig-profile-avatar-letter" id="igDecoyLetter">?</div>
                </div>
                <div class="ig-profile-info">
                    <h2 class="ig-profile-handle" id="igDecoyHandle">—</h2>
                    <ul class="ig-profile-stats">
                        <li><strong>0</strong> publications</li>
                        <li><strong id="igDecoyFollowers">0</strong> abonnés</li>
                        <li><strong>12</strong> abonnements</li>
                    </ul>
                    <div class="ig-profile-bio">
                        <strong id="igDecoyName">—</strong><br>
                        <span id="igDecoyBio"></span>
                    </div>
                    <div class="ig-profile-actions">
                        <button class="ig-btn-follow">Suivre</button>
                        <button class="ig-btn-message"
                                onclick="igOpenDecoyDm(
                                document.getElementById('igDecoyHandle').textContent,
                                document.getElementById('igDecoyLetter').textContent,
                                document.getElementById('igDecoyName').textContent
                            )">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            Message
                        </button>
                    </div>
                </div>
            </div>
            <div class="ig-profile-tabs">
                <span class="active">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    PUBLICATIONS
                </span>
            </div>
            <div class="ig-profile-grid">
                <div class="ig-grid-item ig-grid-locked"><span>🔒</span></div>
                <div class="ig-grid-item ig-grid-locked"><span>:)</span></div>
                <div class="ig-grid-item ig-grid-locked"><span><3</span></div>
            </div>
        </div>

    </main>

    <!-- DM bon compte-->
    <div class="ig-dm-overlay hidden" id="igDmOverlay">
        <div class="ig-dm-panel">
            <div class="ig-dm-header">
                <button class="ig-dm-back" onclick="igCloseDm()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="ig-dm-header-avatar"><?= htmlspecialchars($targetLetter) ?></div>
                <div class="ig-dm-header-info">
                    <span class="ig-dm-header-handle"><?= htmlspecialchars($targetHandle) ?></span>
                    <span class="ig-dm-header-status">Actif récemment</span>
                </div>
            </div>

            <div class="ig-dm-messages">
                <div class="ig-dm-info-bubble">
                    Connecté avec <strong><?= htmlspecialchars($targetHandle) ?></strong>.<br>
                    Envoie-lui un message pour obtenir un indice 💬
                </div>
                <?php foreach ($savedMessages as $msg) : ?>
                    <?php if ($msg['from'] === 'me') : ?>
                        <div class="ig-dm-bubble ig-dm-bubble--me">
                            <?= htmlspecialchars($msg['text']) ?>
                        </div>
                    <?php else : ?>
                        <div class="ig-dm-bubble-wrap">
                            <div class="ig-dm-bubble-avatar"><?= htmlspecialchars($targetLetter) ?></div>
                            <div class="ig-dm-bubble ig-dm-bubble--them">
                                <?= nl2br(htmlspecialchars($msg['text'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if (!$botReplied) : ?>
                <div class="ig-dm-input-wrap">
                    <form action="index.php?controller=Puzzle&action=sendDmMessage" method="POST" class="ig-dm-form">
                        <input type="text" name="message" class="ig-dm-input"
                               placeholder="Message…" autocomplete="off" required/>
                        <button type="submit" class="ig-dm-send-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($botReplied) : ?>
                <div class="ig-dm-validate-wrap">
                    <p class="ig-dm-validate-hint">📍 Tu as reçu la localisation ! Valide l'épreuve :</p>
                    <form action="index.php?controller=Puzzle&action=validateSocialMedia" method="POST"
                          class="ig-dm-validate-form">
                        <input type="hidden" name="answer" value="<?= htmlspecialchars($botReplyText) ?>">
                        <button type="submit" class="ig-dm-validate-btn">✓ Valider l'épreuve</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- DM des leurres -->
    <div class="ig-dm-overlay hidden" id="igDecoyDmOverlay">
        <div class="ig-dm-panel">
            <div class="ig-dm-header">
                <button class="ig-dm-back" onclick="igCloseDecoyDm()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="ig-dm-header-avatar" id="igDecoyDmAvatar">?</div>
                <div class="ig-dm-header-info">
                    <span class="ig-dm-header-handle" id="igDecoyDmHandle">—</span>
                    <span class="ig-dm-header-status">Actif récemment</span>
                </div>
            </div>

            <div class="ig-dm-messages" id="igDecoyDmMessages">
                <div class="ig-dm-info-bubble">
                    Connecté avec <strong id="igDecoyDmHandleInfo">ce compte</strong>.<br>
                    Envoie-lui un message 💬
                </div>
            </div>

            <div class="ig-dm-input-wrap" id="igDecoyDmInputWrap">
                <div class="ig-dm-form">
                    <input type="text" id="igDecoyDmInput" class="ig-dm-input"
                           placeholder="Message…" autocomplete="off"
                           onkeydown="if(event.key==='Enter'){igSendDecoyMessage();event.preventDefault();}"/>
                    <button type="button" class="ig-dm-send-btn" onclick="igSendDecoyMessage()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </button>
                </div>
            </div>


        </div>
    </div>

    <!-- post-->
    <div class="ig-modal-overlay hidden" id="igModal" onclick="igCloseModalOutside(event)">
        <button class="ig-modal-close" onclick="igCloseModal()">✕</button>
        <div class="ig-modal">
            <div class="ig-modal-img" id="igModalImg">📷</div>
            <div class="ig-modal-right">
                <div class="ig-modal-header">
                    <div class="ig-modal-avatar"><?= htmlspecialchars($targetLetter) ?></div>
                    <div>
                        <div class="ig-modal-username"><?= htmlspecialchars($targetHandle) ?></div>
                        <div class="ig-modal-location" id="igModalLocation">—</div>
                    </div>
                    <span style="margin-left:auto;font-size:18px;cursor:pointer;">···</span>
                </div>
                <div class="ig-modal-body" id="igModalBody"></div>
                <div class="ig-modal-footer">
                    <div class="ig-modal-actions">
                        <button class="ig-action-btn" id="igModalLikeBtn" onclick="igToggleModalLike()">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0
                                 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                        <button class="ig-action-btn" onclick="igCloseModal(); igOpenDm();">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.5">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="ig-modal-likes" id="igModalLikes">— J'aime</div>
                    <div class="ig-modal-add-comment">
                        <input type="text" placeholder="Ajouter un commentaire…">
                        <button type="button">Publier</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const IG_TARGET = {
        handle:        <?= json_encode($targetHandle) ?>,
        name:          <?= json_encode($targetName) ?>,
        letter:        <?= json_encode($targetLetter) ?>,
        followers:     <?= (int)$targetFollowers ?>,
        posts:         <?= $posts ?>,
        botReplied:    <?= $botReplied ? 'true' : 'false' ?>,
        decoyAccounts: <?= $decoyAccounts ?>,
    };
</script>
<script src="public/assets/js/puzzle.js"></script>