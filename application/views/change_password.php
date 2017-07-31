<form class="w3-container" method="post">

    <label>Votre adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email; ?>">

    <label>Votre mot de passe actuel</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_password" type="password" value="<?= $password; ?>">

    <label>Votre nouveau mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_password" type="password">

    <p class="w3-center">
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer de mot de passe</button>
    </p>

</form>
