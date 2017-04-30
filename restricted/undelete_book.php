<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if ($toolbox->is_post()) {
        if ($book_id = $toolbox->get_input('id')) {
            $toolbox->undelete_book($book_id);
        }

        $toolbox->redirect_to_booklist();
    }

    $booklist = $toolbox->get_deleted_books();

} catch (Exception $exception) {
    $error = $exception->getMessage();
}
?>

<div class="w3-container">

    <header class="w3-container w3-green w3-margin-bottom">
        <h1>Annuler la suppression d'un livre de eBiblio</h1>
    </header>

    <?php if (! empty($error)): ?>
        <?php require '../common/error.php'; ?>
    <?php endif; ?>

    <form method="post">

        <select class="w3-select w3-border w3-pale-green" name="id">

            <option value="" disabled selected>Choisir un livre</option>

            <?php foreach ($booklist as $book_id => $book_info): ?>
                <option value="<?= $book_id; ?>"><?= $toolbox->display_bookname($book_info); ?></option>
            <?php endforeach; ?>

        </select>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Annuler la suppression</button>
        </p>

    </form>

</div>

<div class="w3-panel">
    <a href="/ebiblio/restricted/get_booklist.php"><i class="fa fa-list" aria-hidden="true"></i>
        Liste des livres
    </a>
</div>

<?php require '../common/footer.php'; ?>
