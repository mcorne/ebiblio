<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        if (! empty($_FILES['filename']['name'])) {
            list($book_id, $bookinfo) = $this->upload_book();
            $this->redirect_to_booklist('put', $book_id, $bookinfo);
        } else {
            $this->redirect_to_booklist();
        }
    }

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">Ajouter ou recharger un livre</h3>

<form class="w3-container" enctype="multipart/form-data" method="post">

    <input type="hidden" name="MAX_FILE_SIZE" value="<?= toolbox::MAX_FILE_SIZE; ?>" />

    <p>
        <div class="w3-container w3-pale-green">
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
