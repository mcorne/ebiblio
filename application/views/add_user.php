<h3 class="w3-container w3-margin-bottom">Ajouter un utilisateur</h3>

<form class="w3-container" method="post">

    <label>Adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="email" type="text" value="<?= $email;?>">

    <p>
        <input class="w3-check" name="new_book_notification" type="checkbox" <?= $new_book_notification ? 'checked' : null; ?> >
        <label>Recevoir un e-mail pour tout nouveau livre ajouté dans le bibliothèque</label>
    </p>

    <p>
        <input class="w3-check" name="new_book_notification" type="checkbox" <?= $is_admin_user ? 'checked' : null; ?> >
        <label>Utilisateur de type administrateur</label>
    </p>

    <p>
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Ajouter</button>
    </p>

</form>
