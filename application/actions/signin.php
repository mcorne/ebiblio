<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        $email    = $this->get_input('email');
        $password = $this->get_input('password');

        if ($email and $password) {
            $this->signin($email, $password);
        }
    }

} catch (Exception $exception) {
}
?>

<div class="w3-modal w3-show">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-padding" style="max-width:600px">

        <?php isset($exception) and $this->display_exception($exception); ?>

        <form class="w3-container w3-margin-top" method="post">

            <label>Votre adresse e-mail</label>
            <input class="w3-input w3-border w3-margin-bottom" name="email" type="text">

            <label>Votre mot de passe</label>
            <input class="w3-input w3-border w3-margin-bottom" name="password" type="password">

            <p>
                <button class="w3-button w3-block w3-green w3-section w3-padding" type="submit" value="submit">Se connecter</button>
            </p>

        </form>

        <div class="w3-container">
            <a href="<?= $this->create_url('send_password'); ?>">
                <i class="fa fa-lock" aria-hidden="true"></i>
                Mot de passe oublié
            </a>
        </div>

    </div>
</div>
