<?php

?>
<h1>Centre d'informations</h1>

<?php if ($_SESSION["level"] >= 4) : ?>
    <?php include __DIR__ . "/infos/encryption.php"; ?>
<?php endif; ?>

<?php if ($_SESSION["level"] >= 5) : ?>
    <?php include __DIR__ . "/infos/phishing.php"; ?>
<?php endif; ?>

<?php if ($_SESSION["level"] >= 6) : ?>
    <?php include __DIR__ . "/infos/password.php"; ?>
<?php endif; ?>


