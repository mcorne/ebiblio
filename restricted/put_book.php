<?php
require '../common/header.php';


if ($toolbox->is_post()) {
    try {
        /* @var $toolbox toolbox */
        $toolbox->put_book();
        $toolbox->redirect_to_booklist();
    } catch (Exception $exception) {
        $error = $exception->getMessage(); // TODO: display error message !!!
    }
}
?>

<div class="w3-container">

    <h1>Ajouter ou recharger un livre</h1>

    <form enctype="multipart/form-data" method="post">

        <input type="hidden" name="MAX_FILE_SIZE" value="<?= toolbox::MAX_FILE_SIZE; ?>" />

        <p>
            <label>Sélectionner un fichier avec l'extension .epub</label>
            <input class="w3-input" name="filename" type="file">
        </p>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Envoyer</button>
        </p>

    </form>

</div>

<div class="ul-panel">

    <ul class="w3-ul">

        <li class=" w3-border-0">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            Le livre doit être au format EPUB sans DRM et d'une taille inférieure à 5 Mo.
        </li>

    </ul>

</div>

<?php require '../common/footer.php'; ?>
