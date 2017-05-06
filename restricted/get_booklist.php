<?php
require '../common/header.php';

/* @var $toolbox toolbox */

try {
    if (! $sorting = $toolbox->get_input('sorting') or ! in_array($sorting, ['author', 'title'])) {
        $sorting = 'title';
    }

    $action           = $toolbox->get_input('action');
    $encoded_bookinfo = $toolbox->get_input('info');
    $selected_book_id = $toolbox->get_input('id');

    $booklist = $toolbox->get_booklist(false, $sorting, $action, $selected_book_id, $encoded_bookinfo);
} catch (Exception $exception) {
    $error = $exception->getMessage();
    require '../common/error.php';
}
?>

<h3 class="w3-container w3-margin-bottom">
    <a class="w3-text-black" href="">Liste des livres</a>
    <a href=""><i class="fa fa-refresh" aria-hidden="true" style="font-size: 70%"></i></a>
</h3>

<?php if (empty($booklist)) : ?>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>La bibliothèque est vide.</p>
    </div>

<?php else: ?>

    <table class="w3-table w3-striped w3-bordered">
        <tr>

            <th></th>

            <th>
                <a href="?sorting=name"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i>&nbsp;Titre</a>
            </th>

            <th>
                <a href="?sorting=author"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i>&nbsp;Auteur</a>
            </th>

            <th></th>

        </tr>

        <?php foreach ($booklist as $book_id => $bookinfo): ?>
        <tr <?php if ($book_id == $selected_book_id) : ?>class="w3-pale-red"<?php endif; ?> >

            <td>
                <a href="<?= $bookinfo['uri']; ?>"><i class="fa fa-download fa-lg" aria-hidden="true"></i></a>
            </td>

            <td>
                <a href="<?= $bookinfo['uri']; ?>"><?= htmlspecialchars($bookinfo['title']); ?></a>
            </td>

            <td><?= htmlspecialchars($bookinfo['author']); ?></td>

            <td>
                <a href="/ebiblio/restricted/get_bookinfo.php?id=<?= $book_id; ?>">
                    <i class="fa fa-info-circle fa-lg w3-margin-right icon" aria-hidden="true"></i>
                </a>
            </td>

        </tr>
        <?php endforeach; ?>

    </table>

<?php endif; ?>

<?php require '../common/footer.php'; ?>
