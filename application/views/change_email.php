<form class="w3-container" method="post">

    <label>Votre adresse e-mail actuelle</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_email" type="text" value="<?= $old_email;?>">

    <label>Votre mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="password" type="password" value="">

    <label>Votre nouvelle adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_email" type="text" value="<?= $new_email;?>">

    <p class="w3-center">
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer d'adresse e-mail</button>
    </p>

</form>
