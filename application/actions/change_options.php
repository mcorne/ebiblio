<?php
/* @var $this toolbox */

try {
    if ($this->is_post()) {
        $new_book_notification = $this->get_input('new_book_notification');
        $this->change_options($new_book_notification);
        $this->redirect('change_options');
    }

    $options = $this->get_options();

} catch (Exception $exception) {
    $this->display_exception($exception);
}
?>

<h3 class="w3-container w3-margin-bottom">Changer les options</h3>

<form class="w3-container" method="post">

    <input class="w3-check" name="new_book_notification" type="checkbox" <?= $options['new_book_notification'] ? 'checked' : null; ?>>
    <label>Recevoir un e-mail pour tout nouveau livre ajouté dans le bibliothèque</label>

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
    </p>

</form>
