<main>
    <h1>Vérifier votre email</h1>

    <form action="index.php?controller=emailVerification&action=verify" method="post">
        <ul>
            <li>
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                <label for="code">Code reçu par email</label>
                <input type="text" id="code" name="code" required maxlength="6" pattern="^[0-9]{6}$" inputmode="numeric" placeholder="123456">
            </li>
        </ul>
        <button type="submit">Vérifier</button>
    </form>

    <p>
        <a href="index.php?controller=emailVerification&action=request&email=<?php echo urlencode($email ?? ''); ?>">Renvoyer un code</a>
    </p>
</main>
</body>
</html>

