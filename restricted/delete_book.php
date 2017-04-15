<?php require '../common/header.php'; ?>

<?php
if (is_post()) {
    if (isset($_POST['bookname']) and $filename = validate_bookname($_POST['bookname'])) {
        rename($filename, $filename . '.DEL');
    }

    redirect_to_booklist();
} else {
    if (! isset($_GET['bookname']) or ! $filename = validate_bookname($_GET['bookname'])) {
        redirect_to_booklist();
    }
}
?>

<h1>Supprimer le livre de la liste</h1>

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
