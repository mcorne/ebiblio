<?php require '../common/header.php'; ?>

<?php
$sorting = (isset($_GET['sorting']) and in_array($_GET['sorting'], ['author', 'title'])) ? $_GET['sorting'] : 'title';
$booknames = get_book_names($sorting);
?>

<h1>Prendre un livre</h1>

<table>
    <tr>
        <th><a href="?sorting=name">Titre</a></th>
        <th><a href="?sorting=author">Auteur</a></th>
    </tr>

    <?php foreach ($booknames as $bookname):
            $encoded = urlencode($bookname['basename']);
    ?>
    <tr>
        <td>
            <a href="<?= $bookname['filename'];?>"><?= $bookname['title'];?></a>
        </td>
        <td>
            <?= $bookname['author'];?>
        </td>
        <td>
            <a class="icon" href="../restricted/book_info.php?bookname=<?= $encoded; ?>">&#128712;</a>
            <a class="icon" href="../restricted/rename_book.php?bookname=<?= $encoded; ?>">&#128393;</a>
            <a class="icon" href="../restricted/reload_book.php?bookname=<?= $encoded; ?>">&#8634;</a>
            <a class="icon" href="../restricted/delete_book.php?bookname=<?= $encoded; ?>">x</a>
        </td>
    </tr>

  <?php endforeach; ?>
</table>

<p><a href="restricted/book_list.php">Ajouter un livre</a></p>

<p>
    <a>Comment mettre un livre sur une liseuse Kobo Aura ?</a>
    <br>
    <a>Comment mettre un livre sur l'application Android Kobo ?</a>
    <br>
    <a>etc.</a>
</p>

<?php require '../common/footer.php'; ?>
