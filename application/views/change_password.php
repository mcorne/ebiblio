<h3 class="w3-container w3-margin-bottom">Changer de mot de passe</h3>

<form class="w3-container" method="post">

    <label>Votre adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email; ?>">

    <label>Votre mot de passe actuel</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_password" type="password" value="<?= $password; ?>">

    <label>Votre nouveau mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_password" type="password">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
    </p>

</form>
