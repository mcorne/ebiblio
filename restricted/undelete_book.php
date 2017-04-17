<?php
require '../common/header.php';

/* @var $toolbox toolbox */

if ($toolbox->is_post()) {
    if ($bookname = $toolbox->get_input('bookname') and
        $filename = $toolbox->get_filename($bookname)
    ) {
        $toolbox->undelete_book($filename);
    }

    $toolbox->redirect_to_booklist();
}

$booknames = $toolbox->get_deleted_booknames();
?>

<div class="w3-container">

    <h1>Annuler la suppression d'un livre de la liste</h1>

    <form method="post">

        <select class="w3-select w3-border" name="bookname">

            <option value="" disabled selected>Choisir un livre</option>

            <?php foreach ($booknames as $bookname): ?>
                <option value="<?= urlencode($bookname); ?>"><?= $toolbox->display_bookname($bookname); ?></option>
            <?php endforeach; ?>

        </select>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Annuler la suppression</button>
        </p>

    </form>

</div>

<?php require '../common/footer.php'; ?>
