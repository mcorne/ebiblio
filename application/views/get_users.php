<table class="w3-table w3-striped w3-bordered">

    <?php foreach ($users as $email => $user): ?>
        <tr <?php if ($email == $selected_email) : ?>class="w3-pale-red"<?php endif; ?> >

            <td>
                <?php if ($user['end_date']): ?>
                    <del><?= $email; ?></del>
                <?php else: ?>
                    <span><?= $email; ?></span>
                <?php endif; ?>
            </td>

            <?php if ($email == $_SESSION['email']): ?>

                <td></td>

            <?php else: ?>

                <td>
                    <a href="<?= $this->toolbox->create_url('update_user', ['email' => $email]); ?>">
                        <i class="fa fa-pencil fa-lg w3-margin-right" aria-hidden="true"></i>
                    </a>

                    <?php if ($user['end_date']): ?>
                        <a href="<?= $this->toolbox->create_url('enable_user', ['email' => $email]); ?>">
                            <i class="fa fa-plus fa-lg" aria-hidden="true"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= $this->toolbox->create_url('disable_user', ['email' => $email]); ?>">
                            <i class="fa fa-times fa-lg w3-text-red" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </td>

            <?php endif; ?>

        </tr>
    <?php endforeach; ?>

</table>

<p>
    <a class="w3-button" href="<?= $this->toolbox->create_url('add_user'); ?>">
        <i class="fa fa-user-plus w3-text-green" aria-hidden="true"></i>
        Ajouter un utilisateur
    </a>
</p>
