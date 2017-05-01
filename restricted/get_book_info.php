<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if (! $book_id = $toolbox->get_input('id') or ! $book_info = $toolbox->get_book_info($book_id)) {
        $toolbox->redirect_to_booklist();
    }
} catch (Exception $exception) {
    $error = $exception->getMessage();
}
?>

<div class="w3-container">

    <header class="w3-container w3-green w3-margin-bottom">
        <h1>
            <button class="w3-button w3-xlarge" onclick="w3_open()" style="transform:rotate(180deg)"><i class="fa fa-bars" aria-hidden="true" aria-hidden="true"></i></button>
            Information sur le livre de eBiblio
        </h1>
    </header>

    <?php if (! empty($error)): ?>
        <?php require '../common/error.php'; ?>
    <?php endif; ?>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <h3><?= $toolbox->display_bookname($book_info); ?></h3>
    </div>

    <form method="post">

        <input name="id" type="hidden" value="<?= $book_id; ?>"/>

        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Supprimer</button>

    </form>

    <div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
        <p>Noter qu'un livre n'est jamais supprimé définitivement, il disparait seulement de la liste des livres.</p>
        <p>Pour annuler la suppression d'un livre, cliquer sur le lien correspondant dans la liste des livres <i class="fa fa-undo" aria-hidden="true" aria-hidden="true"></i>.</p>
    </div>

</div>

<div class="w3-panel">
    <a href="/ebiblio/restricted/get_booklist.php"><i class="fa fa-list" aria-hidden="true" aria-hidden="true"></i>
        Liste des livres
    </a>
</div>

<?php require '../common/footer.php'; ?>
