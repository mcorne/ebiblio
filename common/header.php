<?php
if (! $data_dir = realpath(__DIR__ . '/../restricted/data')) {
    echo 'Invalid data directory';
    exit;
}

define('DATA_DIR', $data_dir);

require 'toolbox.php';
$toolbox = new toolbox(DATA_DIR);
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/ebiblio/w3css/4/w3.css" />
        <link rel="stylesheet" type="text/css" href="/ebiblio/font-awesome-4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="/ebiblio/interface.css" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>eBiblio</title>
    </head>

    <body>

        <div class="w3-container">

            <header class="w3-container w3-green w3-margin-bottom">
                <h1>
                    <button class="w3-button w3-xlarge" onclick="w3_open()" style="transform:rotate(180deg)">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                    eBiblio
                </h1>
            </header>

            <div class="w3-sidebar w3-bar-block w3-border-right w3-text-green" style="display:none" id="mySidebar">

                <button onclick="w3_close()" class="w3-bar-item w3-large"><i class="fa fa-window-close" aria-hidden="true"></i></button>

                <a class="w3-bar-item w3-button" href="/ebiblio">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    Accueil
                </a>

                <a class="w3-bar-item w3-button" href="/ebiblio/restricted/get_booklist.php">
                    <i class="fa fa-list-ol" aria-hidden="true"></i>
                    Liste des livres
                </a>

                <a class="w3-bar-item w3-button" href="/ebiblio/restricted/put_book.php">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Ajouter un livre
                </a>

                <a class="w3-bar-item w3-button" href="/ebiblio/restricted/delete_book.php">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    Supprimer un livre
                </a>

                <a class="w3-bar-item w3-button" href="/ebiblio/restricted/undelete_book.php">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                    Annuler la suppression
                </a>

            </div>

            <div class="w3-overlay w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" id="myOverlay"></div>

