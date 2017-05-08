<?php
session_start([
    'cookie_lifetime' => 15,
]);

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $_SESSION['user'] = 'toto';
}

?>

<?php if (empty($_SESSION['user'])): ?>
<form method="post">
    <input type="submit" value="Signin">
</form>

<?php else: ?>
    <?php
        header('Content-Type: application/epub+zip');
        header('Content-Disposition: attachment; filename="test.epub"');
        readfile('../restricted/data/books/Eye of the Needle_Ken Follett_2.epub');
    ?>
<?php endif; ?>
