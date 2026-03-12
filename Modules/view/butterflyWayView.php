<?php

/** @var array $data */

$team = $_SESSION['team'] ?? 'alice';
?>
<main class="app" data-bw-root data-team="<?= htmlspecialchars($team, ENT_QUOTES, 'UTF-8') ?>">
    <section class="page">
        <div class="center">
            <h1 class="heading">Le chemin du petit papillon</h1>

            <p class="lead">
                Un papillon traverse les couloirs du système.<br>
                On dirait qu'il possède des données importantes.<br>
                Il ne laisse presque aucune trace.<br>
                Chaque mouvement est une information.<br>
                Chaque erreur, une alerte.<br><br>
                En cybersécurité, ce qui disparaît trop vite est souvent ce qu’il fallait protéger.<br><br>
                Observe. Interprète. Ne force jamais.
            </p>

            <!-- HINT + FEEDBACK pilotés par JS -->
            <p class="lead" id="bw-hint"></p>

            <p class="lead lead-feedback" id="bw-feedback"></p>

            <!-- ACTIONS pilotées par JS -->
            <div class="bw-actions">
                <div class="bw-left">
                    <button class="btn active btn-but" type="button" data-bw="L">
                        ← Analyser en profondeur
                    </button>
                </div>
                <div class="bw-center">
                    <button class="btn btn-dark active btn-but" type="button" data-bw="B">
                        Revenir à la racine
                    </button>
                </div>
                <div class="bw-right">
                    <button class="btn active btn-but" type="button" data-bw="R">
                        Suivre la voie évidente →
                    </button>
                </div>
            </div>

            <!-- FORM CODE (caché tant que pas fini, affiché par JS) -->
            <div id="bw-code-zone" class="bw-code-zone">
                <p class="lead">
                    <em>
                        Le papillon se pose sur un terminal verrouillé.<br>
                        L’écran affiche : <strong>INPUT REQUIRED</strong>.<br>
                        Il attend que tu identifies une chose : <br>
                        Un terme simple, souvent utilisé pour désigner l’accès total<br>
                        ou plutôt le rôle qui supervise tout.<br>
                        Ou alors, Un mot court, connu de ceux qui touchent au cœur des systèmes,<br>
                        l’identifiant de l’accès le plus absolu.<br>
                        Une chose est sûre : il n’y a de place que pour <strong>4 à 5 caractères</strong>.<br>
                    </em>
                </p>

                <form method="post"
                      action="index.php?controller=Puzzle&action=validateButterflyCode"
                      class="card bw-code-form">
                    <label for="code">Code</label>
                    <input
                            class="input fly-label"
                            id="code"
                            name="code"
                            autocomplete="off"
                            placeholder="Écris le mot…"
                    />
                    <button class="btn active btn-but" type="submit">Valider</button>
                </form>
            </div>

            <p class="lead bw-status">
                Lieux explorés : <span id="bw-step">0</span> / <span id="bw-max">11</span> —
                Stabilité du signal :
                <strong id="bw-score" class="bw-score">0</strong>
            </p>
        </div>
    </section>
</main>