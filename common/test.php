<?php
require 'header.php';

session_start([
    'cookie_lifetime' => 10,
]);

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $_SESSION['user'] = 'toto';
    header("Location: /ebiblio/common/test.php");
}

?>

<?php if (empty($_SESSION['user'])): ?>
<form method="post">
    <input type="submit" value="Signin">
</form>
<?php else: ?>
    <a href="test2.php">indirect</a><br>
    <a href="/ebiblio/restricted/data/books/Eye of the Needle_Ken Follett_2.epub">direct</a>
<?php endif; ?>

    </body>
</html>

<?php
require 'footer.php';
