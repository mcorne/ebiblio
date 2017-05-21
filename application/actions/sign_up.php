<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        if ($email = $this->get_input('email')) {
            $this->sign_up($email);
        }

        $this->redirect();
    }

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">S'inscrire à la bibliothèque</h3>

<form class="w3-container" method="post">

    <label>Votre adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email ?? null;?>">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">S'inscrire</button>
    </p>

</form>

<div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
    <p>
        Un message va être envoyé à votre adresse e-mail pour confirmation.<br>
        Veuillez consulter vos courriels, y compris les courriels indésirables, et suivez les instructions.
    </p>
</div>
