<?php

?>
<h2> Rassemblement des deux équipes et mot de passe a rentré pour avoir accés au texte de fin qui indique le coffre</h2>
<h2> Résumé des indices trouvés </h2>

<div class="s-section">
    <form method="POST" action="index.php?controller=Puzzle&action=valideMotCle">
        <p>Entrez les mots-clés découverts par les deux équipes pour ouvrir le coffre :</p>
        <label>Mots-clés : <br>
            <textarea name="answer1" required placeholder="Ex: mot1 et mot2"></textarea>
        </label>
        <button type="submit" class="active btn-nav">Déverrouiller le coffre</button>
    </form>
</div>
