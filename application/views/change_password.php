<form method="post">

    <label class="w3-margin-left">Votre adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email; ?>">

    <label class="w3-margin-left">Votre mot de passe actuel</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_password" type="password" value="<?= $password; ?>">

    <label class="w3-margin-left">Votre nouveau mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_password" type="password">

    <button class="w3-btn w3-ripple w3-green w3-block" type="submit" value="submit">Changer de mot de passe</button>

</form>
