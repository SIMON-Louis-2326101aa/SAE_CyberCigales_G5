<?php
/** @var array $data */
$unlocked = !empty($data['unlocked']);
$fb = $data['feedback'] ?? null;
?>
<main class="app">
    <section class="page page--active">
        <div class="center">
            <h1 class="heading">Épreuve : Le murmure des ailes</h1>
            <p class="lead">Le papillon se pose près d’une étiquette effacée. On distingue quelques lettres :
                <em>_ _ _ i l l o n</em>. Il bat des ailes trois fois quand tu fais le bon choix.</p>

            <div class="card" style="display:flex; flex-direction:column; gap:14px; align-items:center;">
                <form method="post" action="?m=papillon&a=validate" class="row" style="display:flex;
                gap:10px; align-items:center; justify-content:center">
                    <input name="code" id="ui-code" class="input" type="text"
                           placeholder="Entre le mot secret…" autocomplete="off" spellcheck="false">
                    <button class="btn" id="ui-check" type="submit">Valider</button>
                </form>
                <?php if ($fb) : ?>
                    <div id="ui-feedback" style="color:<?= $unlocked ? 'green' : '#b91c1c' ?>;">
                        <?= htmlspecialchars($fb, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($unlocked) : ?>
                <a class="btn" id="ui-continue" href="?m=papillon&a=page2">Continuer</a>
            <?php else : ?>
                <a class="btn" id="ui-continue" aria-disabled="true"
                   style="opacity:.6; pointer-events:none;">Continuer</a>
            <?php endif; ?>
        </div>
    </section>
</main>
