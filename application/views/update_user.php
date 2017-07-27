<form method="post">
    <input name="old_email" type="hidden" value="<?= $old_email;?>">

    <label class="w3-margin-left">Adresse e-mail</label>
    <input class="w3-input w3-border w3-margin-bottom" name="new_email" type="text" value="<?= $new_email;?>">


    <p>
        <input class="w3-check" name="new_book_notification" type="checkbox" <?= $new_book_notification ? 'checked' : null; ?> >
        <label>Recevoir un e-mail pour tout nouveau livre ajouté dans le bibliothèque</label>
    </p>

    <p>
        <input class="w3-check" name="admin" type="checkbox" <?= $admin ? 'checked' : null; ?> >
        <label>Utilisateur de type administrateur</label>
    </p>

    <p>
        <button class="w3-btn w3-ripple w3-green w3-block" type="submit" value="submit">Mettre à jour un utilisateur</button>
    </p>

</form>
