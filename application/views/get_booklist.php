<?php if (empty($booklist)) : ?>

    <div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
        <p>La bibliothèque est vide.</p>
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

        </tr>

        <?php foreach ($booklist as $book_id => $bookinfo):
                $download_url = $this->toolbox->create_url('download_book', ['id' => $book_id]); ?>

            <tr <?php if ($book_id == $selected_book_id) : ?>class="w3-pale-red"<?php endif; ?> >

                <td>
                    <a href="<?= $download_url; ?>">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        <?= htmlspecialchars($bookinfo['title']); ?>
                    </a>
                </td>

                <td>
                    <a href="<?= $this->toolbox->create_url('get_bookinfo', ['id' => $book_id]); ?>">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        <?= htmlspecialchars($bookinfo['author']); ?>
                    </a>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

<?php endif; ?>
