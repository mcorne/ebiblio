<?php
/* @var $this toolbox */

try {
    // captures the password passed as url param if any, that is the new password sent by email
    $password = $this->get_input('password');

    if ($this->is_post()) {
        $email         = $this->get_input('email');
        $old_password  = $this->get_input('old_password');
        $new_password  = $this->get_input('new_password');

        $this->change_password($email, $old_password, $new_password);
        $this->redirect('signin');
    }

    $email    = $this->get_input('email');

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">Changer de mot de passe</h3>

<form class="w3-container" method="post">

    <label>Votre adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email ?? null; ?>">

    <label>Votre mot de passe actuel</label>
    <input class="w3-input w3-border w3-margin-bottom" name="old_password" type="password" value="<?= $password ?? null; ?>">

    <label>Votre nouveau mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_password" type="password">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
    </p>

</form>
