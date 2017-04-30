<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if ($toolbox->is_post()) {
        $toolbox->put_book();
        $toolbox->redirect_to_booklist();
    }
} catch (Exception $exception) {
    $error = $exception->getMessage();
}
?>

<div class="w3-container">

    <header class="w3-container w3-green w3-margin-bottom">
        <h1>Ajouter ou recharger un livre dans eBiblio</h1>
    </header>

    <?php if (! empty($error)): ?>
        <?php require '../common/error.php'; ?>
    <?php endif; ?>

    <form enctype="multipart/form-data" method="post">

        <input type="hidden" name="MAX_FILE_SIZE" value="<?= toolbox::MAX_FILE_SIZE; ?>" />

        <p>
            <div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
                <p>
                    <label>Sélectionner un fichier avec l'extension .epub</label>
                </p>
                <p>
                    <input class="w3-btn" name="filename" type="file">
                </p>
            </div>

        </p>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Envoyer</button>
        </p>

    </form>

    <div class="w3-panel w3-pale-yellow w3-leftbar w3-border-yellow">
        <p>Le livre doit être au format EPUB sans DRM et d'une taille inférieure à 5 Mo.</p>
    </div>

</div>

<div class="w3-panel">
    <a href="/ebiblio/restricted/get_booklist.php"><i class="fa fa-list" aria-hidden="true"></i>
        Liste des livres
    </a>
</div>

<?php require '../common/footer.php'; ?>
