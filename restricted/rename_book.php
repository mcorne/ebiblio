<?php
require '../common/header.php';

/* @var $toolbox toolbox */

if (! $bookname = $toolbox->get_input('bookname')) {
    $toolbox->redirect_to_booklist();
}

if ($toolbox->is_post()) {
    if ($filename = $toolbox->get_filename($bookname) and
        $title    = $toolbox->get_input('title') and
        $author   = $toolbox->get_input('author')
    ) {
        $toolbox->rename_book($filename, $title, $author);
    }

    $toolbox->redirect_to_booklist();
}

$book = $toolbox->split_bookname($bookname);
?>

<div class="w3-container">

    <h1>Changer le nom du livre</h1>

    <h3><?= $toolbox->display_bookname($bookname); ?></h3>

    <form method="post">

        <input name="bookname" type="hidden" value="<?= $bookname; ?>"/>

        <p>
            <label>
                Titre
                <br>
                <span class="w3-text-grey">Ex. La Comédie humaine - Volume 01, Eye of the Needle</span>
            </label>
            <input class="w3-input" name="title" type="text" value="<?= $book['title'] ;?>">
        </p>

        <p>
            <label>
                Auteur principal
                <br>
                <span class="w3-text-grey">Ex. Honoré de Blazac, Ken Follett, Collectif, Anonyme</span>
            </label>
            <input class="w3-input" name="author" type="text" value="<?= $book['author'] ;?>">
        </p>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
        </p>

    </form>

</div>

<div class="ul-panel">

    <ul class="w3-ul">

        <li class=" w3-border-0">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            Le titre et l'auteur sont obligatoires.
        </li>

        <li class=" w3-border-0">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            Noter que les accents seront supprimés, et que les caractères de ponctuation, excepté les tirets, seront remplacés par des espaces.
        </li>

    </ul>

</div>

<?php require '../common/footer.php'; ?>

