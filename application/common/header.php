<?php
/* @var $this toolbox */
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?= $this->create_url('w3css/4/w3.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?= $this->create_url('font-awesome-4.7.0/css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?= $this->create_url('interface.css'); ?>" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>eBiblio</title>
    </head>

    <body class="w3-content">

        <header class="w3-green w3-margin-bottom">
            <h1 class="">
                <button class="w3-button" onclick="open_sidebar()" style="transform:rotate(180deg)">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </button>
                eBiblio
            </h1>
        </header>

        <div class="w3-sidebar w3-bar-block w3-border-right w3-text-green" style="display:none" id="sidebar">

            <button onclick="close_sidebar()" class="w3-bar-item w3-large"><i class="fa fa-window-close" aria-hidden="true"></i></button>

            <a class="w3-bar-item w3-button" href="<?= $this->create_url(); ?>">
                <i class="fa fa-home" aria-hidden="true"></i>
                Accueil
            </a>

            <a class="w3-bar-item w3-button" href="<?= $this->create_url('get_booklist'); ?>">
                <i class="fa fa-list-alt" aria-hidden="true"></i>
                Liste des livres
            </a>

            <a class="w3-bar-item w3-button" href="<?= $this->create_url('send_password'); ?>">
                <i class="fa fa-lock" aria-hidden="true"></i>
                Mot de passe oublié
            </a>

            <div class="w3-dropdown-click">

                <button class="w3-button" onclick="open_or_close_dropdown('manage_books')">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    Gérer les livres
                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>

                <div class="w3-dropdown-content w3-bar-block" id="manage_books">

                    <a class="w3-bar-item w3-button" href="<?= $this->create_url('put_book'); ?>">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Ajouter un livre
                    </a>

                    <a class="w3-bar-item w3-button" href="<?= $this->create_url('delete_book'); ?>">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        Supprimer un livre
                    </a>

                    <a class="w3-bar-item w3-button" href="<?= $this->create_url('undelete_book'); ?>">
                        <i class="fa fa-undo" aria-hidden="true"></i>
                        Annuler la suppression
                    </a>

                </div>

            </div>

            <div class="w3-dropdown-click">

                <button class="w3-button" onclick="open_or_close_dropdown('my_account')">
                    <i class="fa fa-user" aria-hidden="true"></i>
                    Gérer mon compte
                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>

                <div class="w3-dropdown-content w3-bar-block" id="my_account">

                    <a class="w3-bar-item w3-button" href="<?= $this->create_url('change_email'); ?>">
                        <i class="fa fa-at" aria-hidden="true"></i>
                        Changer d'adresse électronique
                    </a>

                    <a class="w3-bar-item w3-button" href="<?= $this->create_url('change_password'); ?>">
                        <i class="fa fa-key" aria-hidden="true"></i>
                        Changer de mot de passe
                    </a>

                </div>

            </div>

        </div>

        <div class="w3-overlay w3-animate-opacity" onclick="close_sidebar()" style="cursor:pointer" id="overlay"></div>

