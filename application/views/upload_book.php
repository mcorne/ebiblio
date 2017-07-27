<form enctype="multipart/form-data" method="post">

    <input type="hidden" name="MAX_FILE_SIZE" value="<?= toolbox::MAX_FILE_SIZE; ?>" />

    <input class="w3-input w3-border w3-pale-green w3-margin-bottom" name="filename" type="file">

    <button class="w3-btn w3-ripple w3-green w3-block" type="submit" value="submit">Ajouter ou recharger un livre</button>

</form>

<div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
    <p>Le livre doit être au format EPUB sans DRM et d'une taille inférieure à 10 Mo.</p>
</div>
