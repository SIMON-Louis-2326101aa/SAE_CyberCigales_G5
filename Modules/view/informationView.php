<?php

?>
<h1>Centre d'informations</h1>

<?php if ($_SESSION["level"] >= 6 || $_SESSION["utilisateur"]["nbTry"] > 1) :
    include __DIR__ . "/infos/encryption.php";
    include __DIR__ . "/infos/phishing.php";
    include __DIR__ . "/infos/password.php";
elseif ($_SESSION["level"] >= 5) :
    include __DIR__ . "/infos/encryption.php";
    include __DIR__ . "/infos/phishing.php";
elseif ($_SESSION["level"] >= 3) :
    include __DIR__ . "/infos/encryption.php";
    include __DIR__ . "/infos/butterfly.php";
elseif ($_SESSION["level"] >= 2) :
    include __DIR__ . "/infos/encryption.php";
else : ?>
    <p>Information à venir...</p>
<?php endif; ?>