<?php if (empty($booklist)) : ?>
    <?php require 'empty_boolist.php'; ?>
<?php else: ?>

    <form class="w3-container" method="post">

        <select class="w3-select w3-border w3-pale-green w3-margin-bottom"
                name="id"
                onchange="select_book('<?= $this->toolbox->create_url('update_bookinfo'); ?>', this.value)"
        >

            <option value="" disabled selected>Choisir un livre</option>

            <?php foreach ($booklist as $bookinfo_id => $bookinfo): ?>
                <option value="<?= $bookinfo_id; ?>" <?php if ($bookinfo_id == $book_id): ?>selected<?php endif;?> >
                    <?= $this->toolbox->display_bookname($bookinfo); ?>
                </option>
            <?php endforeach; ?>

        </select>

        <?php if ($book_id and isset($booklist[$book_id])): ?>

            <label>Titre</label>
            <textarea class="w3-input w3-border w3-margin-bottom" name="title"><?= $booklist[$book_id]['title'];?></textarea>

            <label>Auteur</label>
            <textarea class="w3-input w3-border w3-margin-bottom" name="author"><?= $booklist[$book_id]['author'];?></textarea>

        <?php endif; ?>

        <p class="w3-center">
            <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Mettre à jour</button>
        </p>

        <div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
            <p>Laisser le titre ou l'auteur vide pour restaurer le titre ou l'auteur original.</p>
        </div>

    </form>

<?php endif; ?>
