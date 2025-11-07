<h1>Contact</h1>
<form id="form-contact" action="index.php?controller=User&action=contact" method="post">
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
            <input type="text" id="message" required>
        </li>
    </ul>
    <br>
    <button type="submit">Envoyer</button>
</form>
