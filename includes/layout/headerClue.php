<?php

?>
<div id="info-tab">
    <div id="info-handle" class="tab-handle disabled">ℹ️</div>
    <div id="info-content" class="tab-content">
        <h3>Informations</h3>
        <?php
        switch ($info) {
            case "encryption":
                include __DIR__ . "/../../Modules/view/infos/encryption.php";
                break;

            case "phishing":
                include __DIR__ . "/../../Modules/view/infos/phishing.php";
                break;

            case "password":
                include __DIR__ . "/../../Modules/view/infos/password.php";
                break;
        }
        ?>
    </div>
</div>
<div id="clue-tab">
    <div id="clue-handle" class="tab-handle">💡</div>
    <div id="clue-content" class="tab-content">
        <h3>Indice</h3>
        <?php echo $clue; ?>
    </div>
</div>
<div id="inventory-tab">
    <div id="inventory-handle" class="tab-handle">🎒</div>

    <div id="inventory-content" class="tab-content">
        <h3>Toutes vos trouvailles</h3>

        <?php if (!isset($_SESSION["level"]) || $_SESSION["level"] <= 1) : ?>
            <p>Votre inventaire est vide pour le moment.</p>
        <?php else : ?>
            <ul>
                <?php if ($_SESSION["level"] >= 2) : ?>
                    <li>
                        <strong>Épreuve 1 :</strong> Lettre cryptée trouvée dans une enveloppe familiale.
                        <?php if ($_SESSION["team"] === "alice") : ?>
                            Ma chère Diane,
                            Si tu lis ces lignes, c’est que le temps a poursuivi sa route sans nous attendre.
                            Nous ne savons pas ce que la vie t’a réservé, mais nous espérons qu’elle t’a offert
                            autant de raisons d’aimer que de pardonner.
                            Il est des blessures que l’on tait trop longtemps, croyant qu’elles disparaîtront
                            d’elles-mêmes. Mais le silence ne soigne pas, il endort seulement la douleur.
                            Nous avons vu, dans le regard de ta fille Alice, cette même lueur que tu avais enfant :
                            celle de la curiosité et du courage mêlés.
                            Ne la laisse pas s’éteindre, même si le monde tente de la couvrir d’ombre.
                            Tout ce que nous avons construit, tout ce que nous avons caché, nous l’avons fait
                            pour que quelqu’un comme elle puisse un jour comprendre.
                            Avec toute la tendresse que le vent n’a pas emportée.
                            Tes grands-parents qui t'aiment.

                            Et a l'arriere le mot : REFLET
                        <?php else : ?>
                            Ma chère Clara,
                            Le temps nous a glissé entre les doigts comme du sable, mais avant qu’il ne disparaisse
                            complètement, nous voulions te confier ces mots.
                            Il n’y a pas de faute trop ancienne pour être pardonnée, ni de distance trop grande
                            pour être franchie.
                            Parfois, la vie nous sépare non pour nous punir, mais pour nous apprendre à revenir.
                            Ton fils Bob possède déjà cette flamme que nous avons reconnue : la soif de comprendre,
                            d’aller au-delà des évidences.
                            Aide-le à écouter ce qu’on ne dit pas, à lire ce qu’on ne montre plus.
                            Ce que nous avons laissé derrière nous n’est pas un trésor d’or ou de pierre, mais un
                            message, une part de notre histoire, cachée dans les plis du temps.
                            Avec l’espoir que les chemins perdus se croisent à nouveau.
                            Tes grands-parents qui t'aiment.

                            Et a l'arriere le mot : REFLET
                        <?php endif; ?>
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION["level"] >= 3) : ?>
                    <li>
                        <strong>Épreuve 2 :</strong> Une photo de famille mystérieuse avec un message caché.
                        <?php if ($_SESSION["team"] === "alice") : ?>
                        Le message etait "Le papillon garde la mémoire." et on a retenu de cette phrase PAPILLON MEMOIRE
                        <?php else : ?>
                        Le message etait "La clé ouvre le passé." et on a retenu de cette phrase CLE PASSE
                        <?php endif; ?>
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION["level"] >= 4) : ?>
                    <li>
                        <strong>Épreuve 3 :</strong> On a suivi la piste d'un signale deguise en papillon, qui nous a
                        mené  <?php if ($_SESSION["team"] === "alice") : ?>
                            admin
                              <?php else : ?>
                            root
                              <?php endif; ?>
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION["level"] >= 5) : ?>
                    <li>
                        <strong>Épreuve 4 :</strong> Un document important dans un mail nous fait prendre connaissance
                        qu'on a une tante
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION["level"] >= 6) : ?>
                    <li>
                        <strong>Épreuve 5 :</strong> Un compte sécurisé avec un mot de passe renforcé.
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION["level"] >= 7) : ?>
                    <li>
                        <strong>Epreuve 6 :</strong> Plusieurs fragments de l'histoire familiale ont été rassemblés.
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION["level"] >= 8) : ?>
                    <li>
                        <strong>Révélation :</strong> L'existence d'un
                        <?php if ($_SESSION["team"] === "alice") : ?>
                            cousin 
                        <?php else : ?>
                            cousine
                        <?php endif; ?>
                    </li>
                <?php endif; ?>

            </ul>

        <?php endif; ?>
    </div>
</div>