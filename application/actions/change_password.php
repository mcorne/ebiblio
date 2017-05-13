<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        $email         = $this->get_input('email');
        $old_password  = $this->get_input('old_password');
        $new_password  = $this->get_input('new_password');

        if ($email and $old_password and $new_password) {
            $this->change_password($email, $old_password, $new_password);
        }

        $this->redirect();
    } else {
        $email         = $this->get_input('email');
        $old_password  = $this->get_input('password');
        $new_password  = null;
    }

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">Changer de mot de passe</h3>

<form class="w3-container" method="post">

    <label>Votre adresse électronique</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email; ?>">

    <label>Votre mot de passe actuel</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_password" type="password" value="<?= $old_password; ?>">

    <label>Votre nouveau mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_password" type="password">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
    </p>

</form>

<div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
    <p>
        Vous allez être redirigé sur la page d'accueil.
    </p>
</div>
