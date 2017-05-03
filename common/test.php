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
    <?= $_SESSION['user']; ?> already signed in
<?php endif; ?>
