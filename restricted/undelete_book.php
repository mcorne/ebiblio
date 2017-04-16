<?php require '../common/header.php'; ?>

<?php
$filenames = glob('../*.epub');
$filenames = array_map('basename', $filenames);
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

<select>
    <option value="">-- Choisir un fichier --</option>

    <?php foreach ($filenames as $filename): ?>
        <option><?= $filename; ?></option>
    <?php endforeach; ?>
</select>

<form action="" method="post">
    <input name="bookname" type="hidden" value="<?= $_GET['bookname']; ?>"/>
    <button type="submit" value="submit">Supprimer</button>
</form>

<p>
    Noter qu'un livre n'est jamais supprimé définitivement.
    <br>
    Pour annuler la suppression d'un livre, cliquer sur le lien correspondant dans la liste des livres.
</p>

<?php require '../common/footer.php'; ?>
