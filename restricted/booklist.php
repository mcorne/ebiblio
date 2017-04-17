<?php
require '../common/header.php';

/* @var $toolbox toolbox */

if (! $sorting = $toolbox->get_input('sorting') or
    ! in_array($sorting, ['author', 'title'])
) {
    $sorting = 'title';
}

$books = $toolbox->get_booklist($sorting);
?>

<div class="w3-container">

    <h1>Liste des livres</h1>

    <table class="w3-table w3-striped w3-bordered">
        <tr>
            <th></th>
            <th><a href="?sorting=name"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i></a>&nbsp;Titre</th>
            <th><a href="?sorting=author"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i></a>&nbsp;Auteur</th>
        </tr>

        <?php foreach ($books as $book):
                $bookname = urlencode($book['bookname']);
        ?>
        <tr>

            <td>
                <a href="<?= $book['filename'];?>" title="Télécharger le livre sur l'appareil">
                    <i class="fa fa-download fa-lg" aria-hidden="true"></i>
                </a>
            </td>

            <td><?= $toolbox->display_bookname($book, true);?></td>

            <td><?= $book['author'];?></td>

            <td>
                <a href="/ebiblio/restricted/book_info.php?bookname=<?= $bookname; ?>" title="Voir les infos sur le livre">
                    <i class="fa fa-info-circle fa-lg icon" aria-hidden="true"></i>
                </a>
                <a href="/ebiblio/restricted/rename_book.php?bookname=<?= $bookname; ?>" title="Changer le nom du livre">
                    <i class="fa fa-pencil-square fa-lg icon" aria-hidden="true"></i>
                </a>
                <a href="/ebiblio/restricted/reload_book.php?bookname=<?= $bookname; ?>" title="Recharger le livre dans la bibliothèque">
                    <i class="fa fa-upload fa-lg icon" aria-hidden="true"></i>
                </a>
                <a href="/ebiblio/restricted/delete_book.php?bookname=<?= $bookname; ?>" title="Supprimer le livre de la liste">
                    <i class="fa fa-trash fa-lg icon" aria-hidden="true"></i>
                </a>
            </td>

        </tr>
        <?php endforeach; ?>

    </table>

</div>

<div class="w3-panel">

    <ul class="w3-ul">

        <li class=" w3-border-0">
            <a href="/ebiblio/restricted/add_book.php"><i class="fa fa-plus" aria-hidden="true"></i></a>
            Ajouter un livre
        </li>

        <?php if ($toolbox->get_deleted_booknames()): ?>
            <li class=" w3-border-0">
                <a href="/ebiblio/restricted/undelete_book.php"><i class="fa fa-undo" aria-hidden="true"></i></a>
                Annuler la suppression d'un livre de la liste
            </li>
        <?php endif; ?>

    </ul>

</div>

<?php require '../common/footer.php'; ?>
