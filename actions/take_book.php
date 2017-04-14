<?php
$booknames = glob('../books/*.epub');
?>

<?php require '../common/header.php'; ?>

    <h1>Prendre un livre</h1>

    <table>
        <?php foreach ($booknames as $bookname): ?>
        <li>
            <a href="<?= $bookname;?>"><?= basename($bookname); ?></a>
        </li>
      <?php endforeach; ?>
    </table>

    <p>
        <a>Comment mettre un livre sur une liseuse Kobo Aura ?</a>
        <br>
        <a>Comment mettre un livre sur l'application Android Kobo ?</a>
        <br>
        <a>etc.</a>
    </p>

<?php require '../common/footer.php'; ?>
