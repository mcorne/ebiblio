<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if (! $book_id = $toolbox->get_input('id') or ! $bookinfo = $toolbox->get_bookinfo($book_id)) {
        $toolbox->redirect_to_booklist();
    }

    $image_source = $toolbox->get_cover_image_source($bookinfo['name'], $bookinfo['cover_ext']);
    $language     = $toolbox->get_language($bookinfo['language']);
} catch (Exception $exception) {
    $error = $exception->getMessage();
    require '../common/error.php';
}
?>

<div class="w3-row">

    <div class="w3-half">

        <h3 class="w3-margin-right w3-padding w3-pale-green">À propos du livre</h3>

        <ul class="w3-ul w3-margin-right">

            <?php if (isset($bookinfo['title'])): ?>
                <li>
                    <span>Titre</span><br>
                    <span class="w3-large"><?= $bookinfo['title']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['author'])): ?>
                <li>
                    <span>Auteur</span><br>
                    <span class="w3-large"><?= $bookinfo['author']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($language)): ?>
                <li>
                    <span>Langue</span><br>
                    <span class="w3-large"><?= $language; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['date'])): ?>
                <li>
                    <span>Date</span><br>
                    <span class="w3-large"><?= $bookinfo['date']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['subject'])): ?>
                <li>
                    <span>Sujet</span><br>
                    <span class="w3-large"><?= $bookinfo['subject']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['description'])): ?>
                <li>
                    <span>Description</span><br>
                    <span class="w3-large"><?= $bookinfo['description']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['publisher'])): ?>
                <li>
                    <span>Éditeur</span><br>
                    <span class="w3-large"><?= $bookinfo['publisher']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['rights'])): ?>
                <li>
                    <span>Droit d'auteur</span><br>
                    <span class="w3-large"><?= $bookinfo['rights']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['identifier'])): ?>
                <li>
                    <span>Identifiant</span><br>
                    <span class="w3-large"><?= $bookinfo['identifier']; ?></span>
                </li>
            <?php endif; ?>

        </ul>

        <h3 class="w3-margin-right w3-padding w3-pale-green">Informations techniques</h3>

        <ul class="w3-ul w3-margin-right">

            <?php if (isset($bookinfo['name'])): ?>
                <li>
                    <span>Nom du fichier actuel</span><br>
                    <span class="w3-large"><?= $bookinfo['name']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['source'])): ?>
                <li>
                    <span>Nom du fichier original</span><br>
                    <span class="w3-large"><?= $bookinfo['source']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['number'])): ?>
                <li>
                    <span>Numéro d'entrée dans la base</span><br>
                    <span class="w3-large"><?= $bookinfo['number']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['created'])): ?>
                <li>
                    <span>Date d'entrée</span><br>
                    <span class="w3-large"><?= $bookinfo['created']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['updated'])): ?>
                <li>
                    <span>Date de mise à jour</span><br>
                    <span class="w3-large"><?= $bookinfo['updated']; ?></span>
                </li>
            <?php endif; ?>

        </ul>

    </div>

    <div class="w3-half w3-padding">
        <?php if ($image_source): ?>
            <img class="w3-image" src="<?= $image_source; ?>" >
        <?php endif; ?>
    </div>

</div>

<?php require '../common/footer.php'; ?>
