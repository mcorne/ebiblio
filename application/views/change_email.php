<form method="post">

    <label class="w3-margin-left">Votre adresse e-mail actuelle</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_email" type="text" value="<?= $old_email;?>">

    <label class="w3-margin-left">Votre mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="password" type="password" value="">

    <label class="w3-margin-left">Votre nouvelle adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_email" type="text" value="<?= $new_email;?>">

    <button class="w3-btn w3-ripple w3-green w3-block" type="submit" value="submit">Changer d'adresse e-mail</button>

</form>
