<?php if (isset($_SESSION['team']) && $_SESSION['team'] === "alice") : ?>
    <h2> Résumé des indices trouvés </h2>

    <div class="s-section">
        <form method="POST" action="index.php?controller=Puzzle&action=valideIndice">
            <input type="hidden" name="epreuve" value="1">
            <p>Epreuve 1</p>
            <label>Indice 1 : <br>
                <textarea name="answer1" required></textarea>
            </label>
            <label>Indice 2 : <br>
                <textarea name="answer2" required></textarea>
            </label>
            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>

    <div class="s-section">
        <form method="POST" action="index.php?controller=Puzzle&action=valideIndice">
            <input type="hidden" name="epreuve" value="2">
            <p>Epreuve 2</p>
            <label>Indice 1 : <br>
                <textarea name="answer1" required></textarea>
            </label>
            <label>Indice 2 : <br>
                <textarea name="answer2" required></textarea>
            </label>
            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>

    <div class="s-section">
        <form method="POST" action="index.php?controller=Puzzle&action=valideIndice">
            <input type="hidden" name="epreuve" value="3">
            <p>Epreuve 3</p>
            <label>Indice 1 : <br>
                <textarea name="answer1" required></textarea>
            </label>
            <label>Indice 2 : <br>
                <textarea name="answer2" required></textarea>
            </label>
            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>

<?php elseif (isset($_SESSION['team']) && $_SESSION['team'] === "bob") : ?>
    <h2> Résumé des indices trouvés </h2>

    <div class="s-section">
        <form method="POST" action="index.php?controller=Puzzle&action=valideIndice">
            <input type="hidden" name="epreuve" value="1">
            <p>Epreuve 1</p>
            <label>Indice 1 : <br>
                <textarea name="answer1" required></textarea>
            </label>
            <label>Indice 2 : <br>
                <textarea name="answer2" required></textarea>
            </label>
            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>

    <div class="s-section">
        <form method="POST" action="index.php?controller=Puzzle&action=valideIndice">
            <input type="hidden" name="epreuve" value="2">
            <p>Epreuve 2</p>
            <label>Indice 1 : <br>
                <textarea name="answer1" required></textarea>
            </label>
            <label>Indice 2 : <br>
                <textarea name="answer2" required></textarea>
            </label>
            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>

    <div class="s-section">
        <form method="POST" action="index.php?controller=Puzzle&action=valideIndice">
            <input type="hidden" name="epreuve" value="3">
            <p>Epreuve 3</p>
            <label>Indice 1 : <br>
                <textarea name="answer1" required></textarea>
            </label>
            <label>Indice 2 : <br>
                <textarea name="answer2" required></textarea>
            </label>
            <button type="submit" class="active btn-nav">Valider</button>
        </form>
    </div>
<?php else : ?>
    <p>Erreur : Veuillez choisir une équipe ou vous reconnecter</p>
<?php endif; ?>