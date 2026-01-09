<?php
/** @var array $data */

$started = !empty($data['started']);
$step    = (int)($data['step'] ?? 0);
$max     = (int)($data['maxSteps'] ?? 10);
$score   = (int)($data['score'] ?? 0);
$hint    = (string)($data['hint'] ?? '');
$fb      = $data['feedback'] ?? null;

$done = ($max > 0 && $step >= $max);
?>
<main class="app">
    <section class="page">
        <div class="center">
            <h1 class="heading">Le chemin du petit papillon</h1>

            <?php if (!$started) : ?>
                <p class="lead">
                    Un papillon traverse les couloirs, comme s’il connaissait un secret.
                    Suis-le. Ne le perds pas.
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

                <?php if (!$done) : ?>
                    <div class="bw-actions">
                        <div class="bw-left">
                            <a class="btn active btn-but"
                               href="index.php?controller=ButterflyWay&action=left">← Aller à gauche</a>
                        </div>
                        <div class="bw-center">
                            <a class="btn btn-dark active btn-but"
                               href="index.php?controller=ButterflyWay&action=turn">Se retourner</a>
                        </div>
                        <div class="bw-right">
                            <a class="btn active btn-but"
                               href="index.php?controller=ButterflyWay&action=right">Aller à droite →</a>
                        </div>
                    </div>

                <?php else : ?>
                    <p class="lead">
                        <em>
                            Le papillon s’est posé devant une étiquette.
                            Il ne reste qu’un mot à murmurer…
                        </em>
                    </p>

                    <form method="post"
                          action="index.php?controller=ButterflyWay&action=submitCode"
                          class="card bw-code-form">
                        <label for="code">Code</label>
                        <input
                                class="input"
                                id="code"
                                name="code"
                                autocomplete="off"
                                placeholder="Écris le mot…"
                        />
                        <button class="btn bw-code-form" type="submit">Valider</button>
                    </form>

                    <?php if (!empty($data['code_ok'])) : ?>
                        <p <p class="lead bw-success">>
                            Bravo, tu peux passer à la suite.
                        </p>
                    <?php endif; ?>
                <?php endif; ?>

                <p class="lead bw-status">
                    Étape <?= $step ?> / <?= $max ?> — Score :
                    <strong class="bw-score<?= $score < 0 ? ' is-negative' : ''; ?>"><?= $score ?>>
                        <?= $score ?>
                    </strong>
                </p>
            <?php endif; ?>
        </div>
    </section>
</main>
