<h1> Connection </h1>

<?php if (isset($error)): ?>
    <div class="error-message" style="color: red; background-color: #ffe6e6; padding: 10px; border: 1px solid red; border-radius: 5px; margin-bottom: 15px;">
        <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (isset($info)): ?>
    <div class="info-message" style="color: blue; background-color: #e6f3ff; padding: 10px; border: 1px solid blue; border-radius: 5px; margin-bottom: 15px;">
        <strong>Information :</strong> <?= htmlspecialchars($info) ?>
    </div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="success-message" style="color: green; background-color: #e6ffe6; padding: 10px; border: 1px solid green; border-radius: 5px; margin-bottom: 15px;">
        <strong>Succès :</strong> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<form action="index.php?controller=user&action=login" method="post">
    E-mail : <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"> <br>
    Mot de passe : <input type="password" name="pwd" required><br>
    <button type="submit" name="login">Se connecter</button>
    <a href="index.php?controller=user&action=forgot" class="active">Mot de passe oublié?</a>
</form>
<a href="index.php?controller=user&action=register" class="active">Pas encore inscrit ?</a>
