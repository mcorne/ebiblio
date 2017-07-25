<h3 class="w3-container w3-margin-bottom">
    <a class="w3-text-black" href="">Liste des utilisateurs</a>
    <a href=""><i class="fa fa-refresh" aria-hidden="true" style="font-size: 70%"></i></a>
</h3>

<table class="w3-table w3-striped w3-bordered">
    <tr>

        <th>E-mail</th>
        <th>Admin</th>
        <th>Actif</th>
        <th></th>

    </tr>

    <?php foreach ($users as $email => $user): ?>
        <tr <?php if ($email == $selected_email) : ?>class="w3-pale-red"<?php endif; ?> >

            <td><?= $email; ?></td>
            <td><?= $user['admin'] ? '✔' : ''; ?></td>
            <td><?= $user['end_date'] ? '' : '✔'; ?></td>

            <td>
                <a href="<?= $this->toolbox->create_url('update_user', ['email' => $email]); ?>">
                    <i class="fa fa-pencil fa-lg w3-margin-right icon" aria-hidden="true"></i>
                </a>
            </td>

        </tr>
    <?php endforeach; ?>

</table>
