<?php

?>
<h1>Centre d'informations</h1>

<?php if ($_SESSION["level"] = 2) :
     include __DIR__ . "/infos/encryption.php";
elseif (($_SESSION["level"] = 5)) :
    include __DIR__ . "/infos/phishing.php";
elseif ($_SESSION["level"] = 6) :
    include __DIR__ . "/infos/password.php";
else : ?>
<p>Information a venir</p>
<?php endif; ?>