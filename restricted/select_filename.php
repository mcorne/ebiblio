<?php
$filenames = glob('../*.epub');
$filenames = array_map('basename', $filenames);
?>

<select>
    <option value="">-- Choisir un fichier --</option>

    <?php foreach ($filenames as $filename): ?>
        <option><?= $filename; ?></option>
    <?php endforeach; ?>
</select>