<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if ($toolbox->is_post()) {
        if ($book_id = $toolbox->get_input('id')) {
            $toolbox->undelete_book($book_id);
        }

        $toolbox->redirect_to_booklist('undelete', $book_id);
    }

    $booklist = $toolbox->get_booklist(true);

} catch (Exception $exception) {
    $error = $exception->getMessage();
    require '../common/error.php';
}
?>

<h3 class="w3-container">Annuler la suppression d'un livre</h3>

<?php if (empty($booklist)) : ?>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>Aucun livre n'a encore été supprimé.</p>
    </div>

<?php else: ?>

    <form class="w3-container" method="post">

        <select class="w3-select w3-border w3-pale-green" name="id">

            <option value="" disabled selected>Choisir un livre</option>

            <?php foreach ($booklist as $book_id => $bookinfo): ?>
                <option value="<?= $book_id; ?>"><?= $toolbox->display_bookname($bookinfo); ?></option>
            <?php endforeach; ?>

        </select>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Annuler la suppression</button>
        </p>

    </form>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>La suppression d'un livre ne doit être effectuée que si le livre a été supprimé par erreur.</p>
    </div>

<?php endif; ?>

<?php require '../common/footer.php'; ?>
