<?php require '../common/header.php'; ?>

<?php
$booknames = get_deleted_booknames();
?>

<?php
if (is_post()) {
    if (! empty($_POST['bookname']) and $filename = get_filename($_POST['bookname'], true)) {
        rename($filename . '.DEL', $filename);
    }

    redirect_to_booklist();
}
?>

<h1>Annuler la suppression d'un livre de la liste</h1>


<form class="w3-container" action="" method="post">
    <select class="w3-select w3-border" name="bookname">
        <option value="" disabled selected>Choisir un livre</option>

        <?php foreach ($booknames as $bookname): ?>
        <option value="<?= urlencode($bookname); ?>"><?= $bookname; ?></option>
        <?php endforeach; ?>
    </select>

    <p>
        <button type="submit" value="submit">Annuler la suppression</button>
    </p>
</form>

<?php require '../common/footer.php'; ?>
