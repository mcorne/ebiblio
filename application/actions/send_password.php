<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        if ($email = $this->get_input('email')) {
            $this->send_password($email);
        }

        $this->redirect();
    }
    
} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">Mot de passe oublié</h3>

<form class="w3-container" method="post">

    <label>Votre adresse électronique</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text">

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Envoyer</button>
    </p>

</form>

<div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
    <p>L'adresse doit être la même que celle de votre compte.</p>
</div>

<div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
    <p>
        Un nouveau mot de passe va être envoyé à votre adresse électronique.<br>
        Veuillez consulter vos courriels, y compris les courriels indésirables, et suivez les instructions.<br>
        Vous allez être redirigé sur la page d'accueil.
    </p>
</div>
