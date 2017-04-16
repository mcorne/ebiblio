<?php require '../common/header.php'; ?>

<?php
$sorting  = (isset($_GET['sorting']) and in_array($_GET['sorting'], ['author', 'title'])) ? $_GET['sorting'] : 'title';
$booklist = get_booklist($sorting);
?>

<h1>Liste des livres</h1>

<table>
    <tr>
        <th><a href="?sorting=name"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i></a>&nbsp;Titre</th>
        <th><a href="?sorting=author"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i></a>&nbsp;Auteur</th>
    </tr>

    <?php foreach ($booklist as $book):
            $bookname = urlencode($book['basename']);
    ?>
    <tr>
        <td>
            <a href="<?= $book['filename'];?>" title="Télécharger le livre sur l'appareil">
                <i class="fa fa-download fa-lg" aria-hidden="true"></i>
            </a>
            <?= $book['title'];?>
        </td>
        <td>
            <?= $book['author'];?>
        </td>
        <td>
            <a href="/ebiblio/restricted/book_info.php?bookname=<?= $bookname; ?>" title="Voir les infos sur le livre">
                <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
            </a>
            <a href="/ebiblio/restricted/rename_book.php?bookname=<?= $bookname; ?>" title="Mettre à jour le titre ou l'auteur">
                <i class="fa fa-pencil-square fa-lg" aria-hidden="true"></i>
            </a>
            <a href="/ebiblio/restricted/reload_book.php?bookname=<?= $bookname; ?>" title="Recharger le livre dans la bibliothèque">
                <i class="fa fa-upload fa-lg" aria-hidden="true"></i>
            </a>
            <a href="/ebiblio/restricted/delete_book.php?bookname=<?= $bookname; ?>" title="Supprimer le livre de la liste">
                <i class="fa fa-trash fa-lg" aria-hidden="true"></i>
            </a>
        </td>
    </tr>

  <?php endforeach; ?>
</table>

<p>
    <a href="/ebiblio/restricted/add_book.php"><i class="fa fa-plus" aria-hidden="true"></i></a>
    Ajouter un livre

    <?php if (get_deleted_booknames()): ?>
        <br>
        <a href="/ebiblio/restricted/undelete_book.php"><i class="fa fa-undo" aria-hidden="true"></i></a>
        Annuler la suppression d'un livre de la liste
    <?php endif; ?>
</p>

<?php require '../common/footer.php'; ?>
