<?php
/* @var $this toolbox */

try {
    if (! $book_id = $this->get_input('id') or ! $bookinfo = $this->get_bookinfo($book_id)) {
        $this->redirect_to_booklist();
    }

    $language = $this->get_language($bookinfo['language']);

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<div class="w3-row">

    <div class="w3-half">

        <h3 class="w3-margin-right w3-padding w3-pale-green">À propos du livre</h3>

        <ul class="w3-ul w3-margin-right">

            <?php if (isset($bookinfo['title'])): ?>
                <li>
                    <span class="w3-text-gray">Titre</span><br>
                    <span><?= $bookinfo['title']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['author'])): ?>
                <li>
                    <span class="w3-text-gray">Auteur</span><br>
                    <span><?= $bookinfo['author']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($language)): ?>
                <li>
                    <span class="w3-text-gray">Langue</span><br>
                    <span><?= $language; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['date'])): ?>
                <li>
                    <span class="w3-text-gray">Date</span><br>
                    <span><?= $bookinfo['date']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['subject'])): ?>
                <li>
                    <span class="w3-text-gray">Sujet</span><br>
                    <span><?= $bookinfo['subject']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['description'])): ?>
                <li>
                    <span class="w3-text-gray">Description</span><br>
                    <span><?= $bookinfo['description']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['publisher'])): ?>
                <li>
                    <span class="w3-text-gray">Éditeur</span><br>
                    <span><?= $bookinfo['publisher']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['rights'])): ?>
                <li>
                    <span class="w3-text-gray">Droit d'auteur</span><br>
                    <span><?= $bookinfo['rights']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['identifier'])): ?>
                <li>
                    <span class="w3-text-gray">Identifiant</span><br>
                    <span><?= $bookinfo['identifier']; ?></span>
                </li>
            <?php endif; ?>

        </ul>

        <h3 class="w3-margin-right w3-padding w3-pale-green">Informations techniques</h3>

        <ul class="w3-ul w3-margin-right">

            <?php if (isset($bookinfo['name'])): ?>
                <li>
                    <span class="w3-text-gray">Nom du fichier actuel</span><br>
                    <span><?= $bookinfo['name']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['source'])): ?>
                <li>
                    <span class="w3-text-gray">Nom du fichier original</span><br>
                    <span><?= $bookinfo['source']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['number'])): ?>
                <li>
                    <span class="w3-text-gray">Numéro d'entrée dans la base</span><br>
                    <span><?= $bookinfo['number']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['created'])): ?>
                <li>
                    <span class="w3-text-gray">Date d'entrée</span><br>
                    <span><?= $bookinfo['created']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['updated'])): ?>
                <li>
                    <span class="w3-text-gray">Date de mise à jour</span><br>
                    <span><?= $bookinfo['updated']; ?></span>
                </li>
            <?php endif; ?>

            <?php if (isset($bookinfo['email'])): ?>
                <li>
                    <span class="w3-text-gray">Ajouté par</span><br>
                    <span><?= $bookinfo['email']; ?></span>
                </li>
            <?php endif; ?>

        </ul>

    </div>

    <div class="w3-half w3-padding">
        <?php if ($bookinfo['cover_ext']): ?>
            <img class="w3-image" src="<?= $this->create_url('display_cover', ['id' => $book_id]); ?>" >
        <?php endif; ?>
    </div>

</div>