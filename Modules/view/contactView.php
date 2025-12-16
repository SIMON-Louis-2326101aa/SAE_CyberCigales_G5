<h1>Contact</h1>
<form id="form-contact" action="index.php?controller=EmailContact&action=sendContactEmail" method="post">

    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">

    <ul>
        <li>
            <label for="mail">E-mail :</label>
            <input type="email" id="mail" name="email" required>
        </li>
        <li>
            <label for="sujet">Sujet : </label>
            <input type="text" id="sujet" name="sujet" required>
        </li>
        <li>
            <label for="message">Message :</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </li>
    </ul>
    <br>
    <button type="submit">Envoyer</button>
</form>