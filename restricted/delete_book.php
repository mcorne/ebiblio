<?php
require '../common/header.php';

/* @var $toolbox toolbox */

if (! $bookname = $toolbox->get_input('bookname')) {
    $toolbox->redirect_to_booklist();
}

if ($toolbox->is_post()) {
    if ($filename = $toolbox->get_filename($bookname)) {
        $toolbox->delete_book($filename);
    }

    $toolbox->redirect_to_booklist();
}
?>

<div class="w3-container">

    <h1>Supprimer le livre de la liste</h1>

    <h3><?= $toolbox->display_bookname($bookname); ?></h3>

    <form method="post">

        <input name="bookname" type="hidden" value="<?= $bookname; ?>"/>

        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Supprimer</button>

    </form>
</div>

<div class="ul-panel">

    <ul class="w3-ul">

        <li class=" w3-border-0">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            Noter qu'un livre n'est jamais supprimé définitivement.
        </li>

        <li class=" w3-border-0">
            <i class="fa fa-undo" aria-hidden="true"></i>
            Pour annuler la suppression d'un livre, cliquer sur le lien correspondant dans la liste des livres.
        </li>

    </ul>

</div>

<?php require '../common/footer.php'; ?>
