<?php
$filenames = glob('../*.epub');

$method = $_SERVER['REQUEST_METHOD'];
?>
<h2>Changer le nom d'un fichier</h2>

<form method="post">

    <p>Nom actuel</p>
    <p><?php include 'select_filename.php'; ?></p>
    <p>Titre</p>
    <p><input type="text" name="title"></p>
    <p>Auteur</p>
    <p><input type="text" name="author"></p>
    <p>Droit d'accès</p>
    <p><input type="text" name="rights"></p>

    <p><button type="submit" value="submit">Changer</button><p>

    <p>Le nom du fichier doit a</p>

</form>
