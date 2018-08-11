<form class="w3-container" enctype="multipart/form-data" method="post">

    <input type="hidden" name="MAX_FILE_SIZE" value="<?= toolbox::MAX_FILE_SIZE; ?>" />

    <input class="w3-input w3-border w3-pale-green w3-margin-bottom" name="filename" type="file">

    <p class="w3-center">
        <button class="w3-btn w3-ripple w3-green" type="submit" value="submit">Ajouter ou recharger un livre</button>
    </p>

</form>

<div class="w3-panel w3-pale-red w3-leftbar w3-border-red">
    <p>Le livre doit être au format EPUB sans DRM et d'une taille inférieure à 100 Mo.</p>
</div>
