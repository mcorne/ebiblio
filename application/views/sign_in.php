<div class="w3-modal w3-show">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-padding w3-margin-top" style="max-width:600px">

        <?php if (isset($message)): ?>
            <?php require $this->toolbox->base_path . '/views/message.php'; ?>
        <?php endif; ?>

        <form class="w3-margin-top" method="post">

            <label class="w3-margin-left">Votre adresse e-mail</label>
            <input class="w3-input w3-border w3-margin-bottom" name="email" type="text">

            <label class="w3-margin-left">Votre mot de passe</label>
            <input class="w3-input w3-border w3-margin-bottom" name="password" type="password">

            <button class="w3-button w3-block w3-green w3-section w3-padding w3-block" type="submit" value="submit">Se connecter</button>

        </form>

        <div class="w3-bar">
            <a class="w3-bar-item w3-mobile" href="<?= $this->toolbox->create_url('send_password'); ?>">
                <i class="fa fa-lock" aria-hidden="true"></i>
                Mot de passe oublié
            </a>
        </div>

    </div>
</div>
