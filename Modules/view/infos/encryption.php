<?php

?>
<section class="encryption-info">
    <h4>Le Chiffrement de César</h4>

    <p>Le chiffrement de César est l’un des plus anciens systèmes de cryptographie.
        Il a été utilisé par Jules César pour envoyer des messages secrets à ses généraux.</p>
    <ul>
        <li>L’idée est simple :<br>
            ➡ Chaque lettre du message est décalée d’un certain nombre de positions dans l’alphabet.
        </li>
        <li>Exemple :<br>
            Clé = 3<br>
            A → D<br>
            B → E<br>
            C → F<br>
            ...<br>
            Z → C<br>

            Ainsi, le mot “BONJOUR” devient “ERQMRXU”.</li>
        <li>Comment déchiffrer ?</li>
        <li>Pour retrouver le texte d’origine, il suffit de faire l’inverse du décalage.
            Si le message a été codé avec une clé de +3, il faut le décaler de −3.
        </li>
        <li>Exemple :<br>
            “ERQMRXU” (clé +3) → “BONJOUR”</li>
    </ul>
    <p>mais pour cela, il faut connaître la clé utilisée.</p>";
</section>