<?php if (empty($booklist)) : ?>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>Aucun livre n'a encore été supprimé.</p>
    </div>

<?php else: ?>

    <form class="w3-container" method="post">

        <select class="w3-select w3-border w3-pale-green w3-margin-bottom" name="id">

            <option value="" disabled selected>Choisir un livre</option>

            <?php foreach ($booklist as $book_id => $bookinfo): ?>
                <option value="<?= $book_id; ?>"><?= $this->toolbox->display_bookname($bookinfo); ?></option>
            <?php endforeach; ?>

        </select>

        <p class="w3-center">
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Annuler la suppression d'un livre</button>
        </p>

    </form>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>La suppression d'un livre ne doit être annulée que si le livre a été supprimé par erreur.</p>
    </div>

<?php endif; ?>
