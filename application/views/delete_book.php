<h3 class="w3-container w3-margin-bottom">Supprimer un livre</h3>

<?php if (empty($booklist)) : ?>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>La bibliothèque est vide.</p>
    </div>

<?php else: ?>

<form class="w3-container" method="post">

        <select class="w3-select w3-border w3-pale-green" name="id">

            <option value="" disabled selected>Choisir un livre</option>

            <?php foreach ($booklist as $book_id => $bookinfo): ?>
                <option value="<?= $book_id; ?>"><?= $this->display_bookname($bookinfo); ?></option>
            <?php endforeach; ?>

        </select>

        <p>
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Supprimer</button>
        </p>

    </form>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>Un livre ne doit être supprimé qu'exceptionnellement, par exemple s'il s'avère illisible sur une liseuse.</p>
    </div>

    <div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
        <p>Noter qu'un livre n'est jamais supprimé définitivement, il est simplement retiré de la liste des livres.</p>
        <p>
            Pour annuler la suppression d'un livre, aller dans le menu <i class="fa fa-bars" aria-hidden="true"></i>,
            et cliquer sur le lien correspondant <i class="fa fa-undo" aria-hidden="true"></i>.
        </p>
    </div>

<?php endif; ?>
