<?php if ($_SESSION['team'] === "alice") : ?>
    <div class="enigma-container">

        <div class="enigma-text">
            <p>Cette lettre etait donc adressais a ma mere... Mais pourquoi etait elle crypter comme ca
                et de quel trésor mes arriere-grand parent parle</p>
            <p> Oh, on dirais une photo au fond de l'enveloppe </p>
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

<?php elseif ($_SESSION['team'] === "bob") : ?>
<div class="enigma-container">

    <div class="enigma-text">
        <p>Cette lettre etait donc adressais a ma mere... Mais pourquoi etait elle crypter comme ca
            et de quel trésor mes arriere-grand parent parle</p>
        <p> oh on dirais une photo au fond de l'enveloppe </p>
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
<?php endif; ?>
