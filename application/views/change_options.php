<form class="w3-container" method="post">

    <input class="w3-check" name="new_book_notification" type="checkbox" <?= ! empty($options['new_book_notification']) ? 'checked' : null; ?> >
    <label>Recevoir un e-mail pour tout nouveau livre ajouté dans le bibliothèque</label>

    <p class="w3-center">
        <button class="w3-btn w3-ripple w3-green w3-margin-top" type="submit" value="submit">Changer l'option</button>
    </p>

</form>
