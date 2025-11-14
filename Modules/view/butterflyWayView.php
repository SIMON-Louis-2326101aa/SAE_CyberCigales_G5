<?php
/** @var array $data */

$started  = !empty($data['started']);
$step     = (int) ($data['step'] ?? 0);
$max      = (int) ($data['maxSteps'] ?? 10);
$score    = (int) ($data['score'] ?? 0);
$hint     = $data['hint'] ?? '';
$fb       = $data['feedback'] ?? null;
?>
<main class="app">
    <section class="page page--active">
        <div class="center">
            <h1 class="heading">La petite épreuve du papillon</h1>

            <?php if (!$started) : ?>
                <p class="lead">Tu arrives devant la boutique endormie. Le papillon attend ton premier pas.</p>
                <form method="post" action="?m=papillon&a=start">
                    <button class="btn">Commencer</button>
                </form>
            <?php else : ?>
                <p class="lead" id="hint"><?= htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') ?></p>

                <?php if ($fb) : ?>
                    <p class="lead" style="opacity:.9"><?= htmlspecialchars($fb, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <div style="display:flex; gap:12px; align-items:center; justify-content:center; flex-wrap:wrap">
                    <form method="post" action="?m=papillon&a=right"><button class="btn">Aller à droite →</button>
                    </form>
                    <form method="post" action="?m=papillon&a=left"><button class="btn">← Aller à gauche</button></form>

                    <?php if ($started) : ?>
                        <form method="post" action="?m=papillon&a=turn">
                            <button class="btn" style="background:#222">Se retourner</button>
                        </form>
                    <?php endif; ?>
                </div>

                <p class="lead" style="margin-top:8px; opacity:.75">
                    Étape <?= $step ?> / <?= $max ?> — Score :
                    <strong<?= $score < 0 ? ' style="color:#b91c1c"' : ''; ?>><?= $score ?></strong>
                </p>

                <?php if ($step >= $max) : ?>
                    <p class="lead"><em>Tu sens qu’il n’y a plus rien devant. “Se retourner”…</em></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
