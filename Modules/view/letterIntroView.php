<!-- Marque-page latéral -->
<div id="info-tab">
    <div id="info-handle" class="tab-handle disabled">ℹ️</div>
    <div id="info-content" class="tab-content">
        <h3>Informations</h3>
        <h4>Le Chiffrement de César</h4>
        <p>Le chiffrement de César est l’un des plus anciens systèmes de cryptographie.
            Il a été utilisé par Jules César pour envoyer des messages secrets à ses généraux.</p>
        <ul>
            <li>
                L’idée est simple :<br>
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
            <li>
                Pour retrouver le texte d’origine, il suffit de faire l’inverse du décalage.
                Si le message a été codé avec une clé de +3, il faut le décaler de −3.
            </li>
            <li>
                Exemple :<br>
                “ERQMRXU” (clé +3) → “BONJOUR”</li>
        </ul>
        <p>mais pour cela, il faut connaître la clé utilisée.</p>
    </div>
</div>
<div id="clue-tab">
    <div id="clue-handle" class="tab-handle">💡</div>
    <div id="clue-content" class="tab-content">
        <h3>Indice</h3>
        <ul>
            <li>Indice 1 : </li>
            <li><span id="clue-text-1" class="timed-clue">La clée est cacher dans la lettre.</span></li>
            <li>Indice 2 :</li>
            <li><span id="clue-text-2" class="timed-clue">La clée ce cache dans la date. </span></li>
            <li>Solution :</li>
            <li><span id="clue-text-3" class="timed-clue">La clée est 10.</span></li>
        </ul>
    </div>
</div>
<?php if (isset($_SESSION['team']) && ($_SESSION['team'] === "alice")) : ?>
    <div class="intro-letter-content">
        <p>Vous incarnez le personnage d'Alice, une jeune lycéenne de 16 ans, vivant aux alentours de Marseille avec
            ses parents. Un après-midi de fin d’hiver, vous rangez discrètement les chaussures de votre mère que vous
            lui avez emprunté sans sa permission, lorsque vous renversez une boîte, qui s’ouvre et éparpille tous les
            documents sur le sol. Vous récupérez les documents et la boîte et vous vous dirigez vers votre chambre pour
            tout ranger correctement. Vous remarquez que les papiers sont très anciens et qu’il y a quelques photos que
            vous n'avez jamais vues ainsi qu'une lettre avec un logo de papillon tenant une clé.
            En la retournant, une étrange inscription attire votre attention :</p>
        <p class="clue-letter">“Sache que la clé du savoir sommeille dans un empereur antique. Certains l’appelaient
            Caesar, d’autres l’appellent encore le décalage du destin.” </p>
        <p>L’air semble plus froid soudainement… Vous décidez d’ouvrir la lettre.</p>
    </div><br>

    <div >
    <button id="open-letter-btn" class="active btn-nav" >Ouvrir la lettre</button>
    </div><br>

    <div id="letterContainer" class="letter-wrapper">
        <div id="letterContent" class="letter-content" role="button" tabindex="0" aria-pressed="false">

            <div class="letter-face letter-front">
                    <p>cq sxuhu tyqdu, </p>
                    <p> iy jk byi sui bywdui, s uij gku bu jucfi q fekhikyly iq hekju iqdi deki qjjudthu. deki du iqledi
                        fqi su gku bq lyu j q huiuhlu, cqyi deki uifuhedi gk ubbu j q evvuhj qkjqdj tu hqyiedi t qycuh
                        gku tu fqhtedduh.
                    </p>
                    <p>yb uij tui rbuiikhui gku b ed jqyj jhef bedwjucfi, sheoqdj gk ubbui tyifqhqyjhedj t ubbui cucui.
                        cqyi bu iybudsu du ieywdu fqi, yb udtehj iukbucudj bq tekbukh
                    </p>
                    <p>deki qledi lk, tqdi bu huwqht tu jq vybbu qbysu, sujju cucu bkukh gku jk qlqyi udvqdj subbu tu bq
                        skhyeiyju uj tk sekhqwu cubui. du bq bqyiiu fqi i ujuydthu, cucu iy bu cedtu judju tu bq seklhyh
                        t ecrhu.
                    </p>
                    <p>jekj su gku deki qledi sedijhkyj, jekj su gku deki qledi sqsxu, deki b qledi vqyj fekh gku gkubgk
                        kd seccu ubbu fkyiiu kd zekh secfhudthu.
                    </p>
                    <p>qlus jekju bq judthuiiu gku bu ludj d q fqi ucfehjuu. jui whqdti fqhudji gky j qycudj.
                    </p><br>
                    <p class="clue-letter2">10/10/2010</p>
            </div>

            <div class="letter-face letter-back">
                <p style="text-align: center;">·−· · ··−· ·−·· · − </p>
            </div>

        </div>
    </div>

    <div id="solutionLetter" class="solution-letter-content">
    <form method="POST" action="index.php?controller=Puzzle&action=validateLetter">
            <label>
                Qu'est-ce veut dire cette lettre ?<br>
                <textarea name="answer1" required></textarea>
            </label>

        <label>
            Que signifient ces symboles ?<br>
            <textarea name="answer2" required></textarea>
        </label>

        <button type="submit" class="active btn-nav">
            Valider
        </button>
    </form>
    </div>

<?php elseif (isset($_SESSION['team']) && ($_SESSION['team'] === "bob")) : ?>
    <div class="intro-letter-content">
        <p>Vous incarnez le personnage de Bob, un jeune lycéen de 17 ans, vivant aux alentours de Marseille avec ses
            parents. Un après-midi de fin d’hiver, vous êtes de corvée pour ranger les décorations de Noël au grenier.
            Lors de votre rangement, vous trébuchez sur une boîte qui se renverse et éparpille des papiers anciens et
            des photos jaunies. Vous la ramassez, intrigué : les visages sur les photos vous semblent familiers… mais
            sans savoir d'où. Au fond, une lettre scellée porte un logo élégant, un papillon et une clé.
            En la retournant, vous découvrez une étrange phrase :
        </p>
        <p class="clue-letter">“Sache que la clé du savoir sommeille dans un empereur antique. Certains l’appelaient
            Caesar, d’autres l’appellent encore le décalage du destin.” </p>
        <p>Pris d’une étrange intuition, vous ouvrez la lettre.</p>
    </div><br>

    <div >
        <button id="open-letter-btn" class="active btn-nav">Ouvrir la lettre</button>
    </div><br>

    <div id="letterContainer" class="letter-wrapper">
        <div id="letterContent" class="letter-content" role="button" tabindex="0" aria-pressed="false">

            <div class="letter-face letter-front">
                <p>wk mrobo mvkbk </p>
                <p> vo dowzc xyec k qvscco oxdbo voc nysqdc mywwo ne cklvo wksc kfkxd ae'sv xo nsczkbkscco mywzvodowoxd
                    xyec fyevsyxc do myxpsob moc wydc</p>
                <p>sv x'i k zkc no pkedo dbyz kxmsoxxo zyeb odbo zkbnyxxoo xs no nscdkxmo dbyz qbkxno zyeb odbo pbkxmrso
                    zkbpysc vk fso xyec cozkbo xyx zyeb xyec zexsb wksc zyeb xyec kzzboxnbo k bofoxsb
                </p>
                <p>dyx psvc lyl zyccono notk moddo pvkwwo aeo xyec kfyxc bomyxxeo  vk cysp no mywzboxnbo n'kvvob kenovk
                    noc ofsnoxmoc ksnovo k omyedob mo ae'yx xo nsd zkc k vsbo mo ae'yx xo wyxdbo zvec
                </p>
                <p>mo aeo xyec kfyxc vkscco nobbsobo xyec x'ocd zkc ex dbocyb n'yb ye no zsobbo wksc ex wocckqo exo zkbd
                    no xydbo rscdysbo mkmroo nkxc voc zvsc ne dowzc
                </p>
                <p>kfom v'oczysb aeo voc mrowsxc zobnec co mbyscoxd k xyefoke doc qbkxnczkboxdc aes dkswoxd
                </p><br>
                <p class="clue-letter2">10/10/2010</p>
            </div>

            <div class="letter-face letter-back">
                <p style="text-align: center;">·−· · ··−· ·−·· · − </p>
            </div>

        </div>
    </div>
    <div id="solutionLetter" class="solution-letter-content">
        <form method="POST" action="index.php?controller=Puzzle&action=validateLetter">
            <label>
                Qu'est-ce veut dire cette lettre ?<br>
                <textarea name="answer1" required></textarea>
            </label>

            <label>
                Que signifient ces symboles ?<br>
                <textarea name="answer2" required></textarea>
            </label>

            <button type="submit" class="active btn-nav">
                Valider
            </button>
        </form>
    </div>
<?php else : ?>
    <p>Erreur : veuillez choisir une équipe ou vous reconnecter.</p>
<?php endif; ?><?php
