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
<section class="morse-info">

    <h4>Le code Morse</h4>

    <p>
        Le code Morse est un système de communication inventé au XIXe siècle.
        Il permet de transmettre des messages à l’aide de signaux courts et longs.
        À l’origine, il était utilisé avec le télégraphe.
    </p>

    <p>
        Chaque lettre est représentée par une combinaison de :
    </p>

    <ul>
        <li>• un point (signal court)</li>
        <li>– un tiret (signal long)</li>
    </ul>

    <h3>Alphabet Morse</h3>

    <ul>
        <li>A : • –</li>
        <li>B : – • • •</li>
        <li>C : – • – •</li>
        <li>D : – • •</li>
        <li>E : •</li>
        <li>F : • • – •</li>
        <li>G : – – •</li>
        <li>H : • • • •</li>
        <li>I : • •</li>
        <li>J : • – – –</li>
        <li>K : – • –</li>
        <li>L : • – • •</li>
        <li>M : – –</li>
        <li>N : – •</li>
        <li>O : – – –</li>
        <li>P : • – – •</li>
        <li>Q : – – • –</li>
        <li>R : • – •</li>
        <li>S : • • •</li>
        <li>T : –</li>
        <li>U : • • –</li>
        <li>V : • • • –</li>
        <li>W : • – –</li>
        <li>X : – • • –</li>
        <li>Y : – • – –</li>
        <li>Z : – – • •</li>
    </ul>

    <h4>Exemple</h4>

    <p>
        Le célèbre signal de détresse <em>SOS</em> en Morse est :
    </p>

    <p>
        • • •   – – –   • • •
    </p>

    <p>
        Astuce : dans un message Morse, les lettres sont séparées par un petit espace
        et les mots par un espace plus long.
    </p>

</section>