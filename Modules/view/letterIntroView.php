<?php if (isset($_SESSION['user_id'])) : ?>
    <div class="hero-container-main">
        <p>Vous incarnez le personnage d'Alice, une jeune lycéenne de 16 ans, vivant aux alentours de Marseille avec
            ses parents. </p>
        <p>Une après-midi de fin d’hiver, vous rangez discrètement les chaussures de votre mère que vous
            lui avez empruntées sans sa permission, lorsque vous renversez une boîte, qui s’ouvre et éparpille tous les
            documents sur le sol. Vous récupérez les documents et la boîte et vous vous dirigez vers votre chambre pour
            tout ranger correctement.</p>
        <p> Vous remarquez que les papiers sont très anciens et qu’il y a quelques photos que
            vous n'avez jamais vues et une lettre avec un logo de papillon avec une clé.</p>
        <p> En la retournant, une étrange inscription attire votre attention :</p>
        <p>“Sache que la clé du savoir sommeille dans un empereur antique. Certains l’appelaient Caesar,
            d’autres l’appellent encore le décalage du destin.” </p>
        <p>L’air semble plus froid soudainement… Vous décidez d’ouvrir la lettre.</p>
    </div>

    <a  href="index.php?controller=Redirection&action=openLetter"
        class="active btn-nav">Ouvrir la lettre</a>

    <div class="hero-container-main">
        <p>cq sxuhu tyqdu
            iy jk byi sui bywdui s uij gku bu jucfi q fekhikyly iq hekju iqdi deki qjjudthu deki du iqledi fqi su gku
            bq lyu j q huiuhlu cqyi deki uifuhedi gk ubbu j q evvuhj qkjqdj tu hqyiedi t qycuh gku tu fqhtedduh
            yb uij tui rbuiikhui gku b ed jqyj jhef bedwjucfi sheoqdj gk ubbui tyifqhqyjhedj t ubbui cucui cqyi
            bu iybudsu du ieywdu fqi yb udtehj iukbucudj bq tekbukh
            deki qledi lk tqdi bu huwqht tu jq vybbu qbysu sujju cucu bkukh gku jk qlqyi udvqdj subbu tu bq skhyeiyju
            uj tk sekhqwu cubui du bq bqyiiu fqi i ujuydthu cucu iy bu cedtu judju tu bq seklhyh t ecrhu
            jekj su gku deki qledi sedijhkyj jekj su gku deki qledi sqsxu deki b qledi vqyj fekh gku gkubgk kd seccu
            ubbu fkyiiu kd zekh secfhudthu
            qlus jekju bq judthuiiu gku bu ludj d q fqi ucfehjuu jui whqdti fqhudji gky j qycudj
            10/10/2010
        </p>
    </div>


<?php else : ?>
    <div class="hero-container-main">
        <h1 class="hero-question">Serez-vous capable de résoudre le mystère ?</h1>
        <a  href="index.php?controller=Redirection&action=openFormConnection"
            class="active btn-nav">SE CONNECTER POUR JOUER</a>
    </div>
<?php endif; ?><?php
