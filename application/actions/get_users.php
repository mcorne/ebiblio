<?php
/* @var $this toolbox */

try {
    $action         = $this->get_input('action');
    $encoded_user   = $this->get_input('info');
    $selected_email = $this->get_input('email');

    $users = $this->get_users(false, $action, $selected_email, $encoded_user);

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

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

        <td><?= $user['email']; ?></td>
        <td><?= $user['administrator'] ? '✔' : ''; ?></td>

        <td>
            <a href="<?= $this->create_url('get_bookinfo', ['email' => $email]); ?>">
                <i class="fa fa-info-circle fa-lg w3-margin-right icon" aria-hidden="true"></i>
            </a>
        </td>

    </tr>
    <?php endforeach; ?>

</table>
