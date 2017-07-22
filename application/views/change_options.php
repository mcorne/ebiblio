<h3 class="w3-container w3-margin-bottom">Changer les options</h3>

<form class="w3-container" method="post">

    <input class="w3-check" name="new_book_notification" type="checkbox" <?= $options['new_book_notification'] ? 'checked' : null; ?>>
    <label>Recevoir un e-mail pour tout nouveau livre ajouté dans le bibliothèque</label>

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Changer</button>
    </p>

</form>
