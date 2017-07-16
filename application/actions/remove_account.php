<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        $email    = $this->get_input('email');
        $password = $this->get_input('password');

        $this->remove_account($email, $password);
        $this->redirect();
    }

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">Supprimer le compte</h3>

<form class="w3-container" method="post">

    <label>Votre adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email ?? null;?>">

    <label>Votre mot de passe</label>
    <input class="w3-input w3-border w3-margin-bottom" name="password" type="password" value="">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Supprimer</button>
    </p>

</form>
