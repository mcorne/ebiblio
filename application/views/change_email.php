<h3 class="w3-container w3-margin-bottom">Changer d'adresse e-mail</h3>

<form class="w3-container" method="post">

    <label>Votre adresse e-mail actuelle</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_email" type="text" value="<?= $old_email ?? null;?>">

    <label>Votre mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="password" type="password" value="">

    <label>Votre nouvelle adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_email" type="text" value="<?= $new_email ?? null;?>">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
    </p>

</form>
