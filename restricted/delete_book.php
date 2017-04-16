<?php
require '../common/header.php';

if (is_post()) {
    if (! empty($_POST['bookname']) and $filename = get_filename($_POST['bookname'])) {
        rename($filename, $filename . '.DEL');
    }

    redirect_to_booklist();
} else {
    if (empty($_GET['bookname']) or ! $filename = get_filename($_GET['bookname'])) {
        redirect_to_booklist();
    }
}
?>

<div class="w3-container">

    <h1>Supprimer le livre de la liste</h1>

    <h3><?= $_GET['bookname']; ?></h3>

    <form action="" method="post">
        <input name="bookname" type="hidden" value="<?= $_GET['bookname']; ?>"/>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Supprimer</button>
    </form>
</div>

<div class="w3-panel">
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
    Noter qu'un livre n'est jamais supprimé définitivement.
    <br>
    <i class="fa fa-undo" aria-hidden="true"></i>
    Pour annuler la suppression d'un livre, cliquer sur le lien correspondant dans la liste des livres.
</div>

<?php require '../common/footer.php'; ?>
