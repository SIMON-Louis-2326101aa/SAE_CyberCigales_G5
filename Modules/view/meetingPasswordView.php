<?php

?>
<br>
<p>Vous allez pouvoir rencontrez votre cousin (l'autre joueure(use) avec qui vous jouez) </p>
<p>Rassembler vos information pour voir vers ou ca peut vous mener </p>
<br>
<div class="s-section">
    <form method="POST" action="index.php?controller=Puzzle&action=valideMotCle">
        <p>Entrez les deux mots du passé pour déverrouiller la mémoire .</p>
        <label>Mots-clés : <br>
            <textarea name="answer1" required placeholder="Ex: mot1 et mot2"></textarea>
        </label>
        <button type="submit" class="active btn-nav">Déverrouiller le coffre</button>
    </form>
</div>
