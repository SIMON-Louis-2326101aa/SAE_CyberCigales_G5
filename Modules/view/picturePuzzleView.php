<?php if (isset($_SESSION['team']) && ($_SESSION['team'] === "alice")) : ?>
    <div class="enigma-container">

        <div class="enigma-text">
            <p>Cette lettre était donc adressée à ma mère… Mais pourquoi était-elle cryptée comme ça ?
                Et de quel trésor mes arrière-grandparents parlent.</p>
            <p> Oh on dirait une photo au fond de l'enveloppe. </p>
        </div>

        <div class="photo-enigma">
            <div class="photo-card" id="photoCard">
                <div class="photo-face photo-front">
                    <img src="./assets/images/photoFamille.png" alt="photodefamille">
                </div>
                <div class="photo-face photo-back">
                    <p class="back-image">Le papillon garde la mémoire.</p>
                </div>
            </div>
        </div>

        <div class="enigma-text">
            <p>Tiens, la photo est vraiment très floue.</p>
        </div>

        <div class="enigma-form">
        <form method="POST" action="index.php?controller=Puzzle&action=validatePhoto">
            <label>
                Quoi retenir de ça ? (2 mot attendu)<br>
                <textarea name="answer" required></textarea>
            </label>

            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>
    </div>

<?php elseif (isset($_SESSION['team']) && ($_SESSION['team'] === "bob")) : ?>
<div class="enigma-container">

    <div class="enigma-text">
        <p>Cette lettre était donc adressée à ma mère… Mais pourquoi était-elle cryptée comme ça ?
            Et de quel trésor mes arrière-grandparents parlent.</p>
        <p> Oh on dirait une photo au fond de l'enveloppe. </p>
    </div>

<div class="photo-enigma">
    <div class="photo-card" id="photoCard">
        <div class="photo-face photo-front">
            <img src="./assets/images/photoFamille.png" alt="photodefamille">
        </div>
        <div class="photo-face photo-back">
            <p class="back-image">
                    La clé ouvre le passé.
            </p>
        </div>
    </div>
</div>
    <div class="enigma-text">
        <p>tien la photo est vraiment très flou</p>
    </div>

    <div class="enigma-form">
        <form method="POST" action="index.php?controller=Puzzle&action=validatePhoto">
            <label>
                Quoi retenir de ça ? (2 mot attendu)<br>
                <textarea name="answer" required></textarea>
            </label>

            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>
</div>

<?php else : ?>
    <p>Erreur : Veuillez choisir une equipe ou vous reconnecter</p>
<br>
<?php endif; ?>
