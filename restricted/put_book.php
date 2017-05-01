<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if ($toolbox->is_post()) {
        $book_id = $toolbox->put_book();
        $toolbox->redirect_to_booklist($book_id);
    }
} catch (Exception $exception) {
    $error = $exception->getMessage();
}
?>

<h3>Ajouter ou recharger un livre</h3>

<form enctype="multipart/form-data" method="post">

    <input type="hidden" name="MAX_FILE_SIZE" value="<?= toolbox::MAX_FILE_SIZE; ?>" />

    <p>
        <div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
            <p>
                <label>Sélectionner un fichier avec l'extension .epub</label>
            </p>
            <p>
                <input class="w3-button" name="filename" type="file">
            </p>
        </div>

    </p>

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Envoyer</button>
    </p>

</form>

<div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
    <p>Le livre doit être au format EPUB sans DRM et d'une taille inférieure à 10 Mo.</p>
</div>

<?php require '../common/footer.php'; ?>
