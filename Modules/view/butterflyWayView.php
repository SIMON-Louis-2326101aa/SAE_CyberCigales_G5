<?php
/** @var array $data */

$started = !empty($data['started']);
$step    = (int)($data['step'] ?? 0);
$max     = (int)($data['maxSteps'] ?? 11);
$score   = (int)($data['score'] ?? 0);
$hint    = (string)($data['hint'] ?? '');
$fb      = $data['feedback'] ?? null;

$done = ($max > 0 && $step >= $max);
$showCode = !empty($data['show_code']);
?>
<main class="app">
    <section class="page">
        <div class="center">
            <h1 class="heading">Le chemin du petit papillon</h1>

            <?php if (!$started) : ?>
                <p class="lead">
                    Un papillon traverse les couloirs du système.<br>
                    On dirait qu'il possède des données importantes.<br>
                    Il ne laisse presque aucune trace.<br>
                    Chaque mouvement est une information.<br>
                    Chaque erreur, une alerte.<br>
                    <br>
                    En cybersécurité, ce qui disparaît trop vite est souvent ce qu’il fallait protéger.<br>
                    <br>
                    Observe. Interprète. Ne force jamais.
                </p>

                <form method="post" action="index.php?controller=ButterflyWay&action=start">
                    <button class="btn" type="submit">Commencer</button>
                </form>

            <?php else : ?>
                <?php if ($hint !== '') : ?>
                    <p class="lead" id="hint"><?= htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <?php if (!empty($fb)) : ?>
                    <p class="lead lead-feedback">
                        <?= htmlspecialchars((string)$fb, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <?php if (!$done && !$showCode) :?>
                    <div class="bw-actions">
                        <div class="bw-left">
                            <a class="btn active btn-but"
                               href="index.php?controller=ButterflyWay&action=left">← Analyser en profondeur</a>
                        </div>
                        <div class="bw-center">
                            <a class="btn btn-dark active btn-but"
                               href="index.php?controller=ButterflyWay&action=turn">Revenir à la racine</a>
                        </div>
                        <div class="bw-right">
                            <a class="btn active btn-but"
                               href="index.php?controller=ButterflyWay&action=right">Suivre la voie évidente →</a>
                        </div>
                    </div>
                <?php else : ?>
                    <p class="lead">
                        <em>
                            Le papillon se pose sur un terminal verrouillé.<br>
                            L’écran affiche : <strong>INPUT REQUIRED</strong>.<br>
                            Il attend que tu identifies une chose : <br>
                            Un terme simple, souvent utilisé pour désigner l’accès total<br>
                            ou plutot le rôle qui supervise tout.<br>
                            Ou alors, Un mot court, connu de ceux qui touchent au cœur des systèmes,<br>
                            l’identifiant de l’accès le plus absolu.<br>
                            Une chose est sûre : il n’y a de place que pour <strong>4 à 5 caractères</strong>.<br>
                        </em>
                    </p>

                    <form method="post"
                          action="index.php?controller=ButterflyWay&action=submitCode"
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

                    <?php if (!empty($data['code_ok'])) : ?>
                        <p class="lead lead-success">
                            Un chemin se forme devant toi 🦋
                        </p>
                        <a class="btn active btn-but"
                           href="index.php?controller=Redirection&action=openPhishingPuzzle">
                            Quitter le système et Continuer</a>
                    <?php endif; ?>
                <?php endif; ?>

                <p class="lead bw-status">
                    Lieux explorés : <?= $step ?> / <?= $max ?> — Stabilité du signal :
                    <strong class="bw-score<?= $score < 0 ? ' is-negative' : ''; ?>"><?= $score ?>
                    </strong>
                </p>
            <?php endif; ?>
        </div>
    </section>
</main>
