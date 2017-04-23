<?php
require '../common/header.php';

/* @var $toolbox toolbox */

if (! $sorting = $toolbox->get_input('sorting') or
    ! in_array($sorting, ['author', 'title'])
) {
    $sorting = 'title';
}

try {
    $booklist = $toolbox->get_booklist($sorting);
} catch (Exception $exception) {
    $error = $exception->getMessage();
}
?>

<div class="w3-container">

    <h1>Liste des livres</h1>

    <?php if (! empty($error)): ?>
        <div class="w3-container w3-red">
            <h3>Erreur !</h3>
            <p><?= $error; ?></p>
        </div>
    <?php else: ?>

        <table class="w3-table w3-striped w3-bordered">
            <tr>
                <th>
                    <a href="?sorting=name"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i>&nbsp;Titre</a>
                </th>

                <th>
                    <a href="?sorting=author"><i class="fa fa-sort-asc fa-lg" aria-hidden="true"></i>&nbsp;Auteur</a>
                </th>

                <th>
                    <a onclick="document.getElementById('id01').style.display='block'">
                        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
                    </a>
                </th>
            </tr>

            <?php foreach ($booklist as $id => $bookinfo): ?>
            <tr>

                <td>
                    <a href="<?= $bookinfo['uri']; ?>" title="Télécharger le livre sur l'appareil">
                        <i class="fa fa-download fa-lg" aria-hidden="true"></i>
                        <?= $bookinfo['title']; ?>
                    </a>
                </td>

                <td><?= $bookinfo['author']; ?></td>

                <td class="nowrap">
                    <a href="/ebiblio/restricted/book_info.php?id=<?= $id; ?>">
                        <i class="fa fa-info-circle fa-lg w3-margin-right icon" aria-hidden="true"></i>
                    </a>
                    <a href="/ebiblio/restricted/delete_book.php?id=<?= $id; ?>">
                        <i class="fa fa-trash fa-lg icon" aria-hidden="true"></i>
                    </a>
                </td>

            </tr>
            <?php endforeach; ?>

        </table>

        <div id="id01" class="w3-modal">
            <div class="w3-modal-content w3-card-4">
                <div class="w3-container">

                    <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">
                        &times;
                    </span>

                    <p>
                        <i class="fa fa-info-circle fa-lg w3-margin-right icon" aria-hidden="true"></i>
                        Afficher les infos sur le livre avec la couverture si disponible.
                    </p>

                    <p>
                        <i class="fa fa-trash fa-lg w3-margin-right icon" aria-hidden="true"></i>
                        Supprimer le livre de la liste.
                        Noter qu'une confirmation sera demandée, et que le suppression peut être annulée ensuite en cas d'erreur.
                    </p>

                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<div class="w3-panel">

    <ul class="w3-ul">

        <li class=" w3-border-0">
            <a href="/ebiblio/restricted/put_book.php"><i class="fa fa-plus" aria-hidden="true"></i>
                Ajouter ou recharger un livre
            </a>
        </li>

        <?php if ($toolbox->get_deleted_booknames()): ?>
            <li class=" w3-border-0">
                <a href="/ebiblio/restricted/undelete_book.php"><i class="fa fa-undo" aria-hidden="true"></i>
                    Annuler la suppression d'un livre de la liste
                </a>
            </li>
        <?php endif; ?>

    </ul>

</div>

<?php require '../common/footer.php'; ?>
