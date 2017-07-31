<div class="w3-modal w3-show">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-padding w3-margin-top" style="max-width:600px">

        <?php if (isset($message)): ?>
            <?php require $this->toolbox->config['base_path'] . '/views/message.php'; ?>
        <?php endif; ?>

        <form class="w3-panel" method="post">

            <label>Votre adresse e-mail</label>
            <input class="w3-input w3-border w3-margin-bottom" name="email" type="text">

            <label>Votre mot de passe</label>
            <input class="w3-input w3-border w3-margin-bottom" name="password" type="password">

            <p class="w3-center">
                <button class="w3-button w3-green w3-section w3-padding" type="submit" value="submit">Se connecter</button>

                <p>
                    <a href="<?= $this->toolbox->create_url('send_password'); ?>">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        Mot de passe oublié
                    </a>
                </p>
            </p>

        </form>

    </div>
</div>
