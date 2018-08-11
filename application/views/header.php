<?php
/* @var $this toolbox */
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?= $this->toolbox->create_url('w3css/4/w3.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?= $this->toolbox->create_url('font-awesome-4.7.0/css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?= $this->toolbox->create_url('application.css'); ?>" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>eBiblio</title>
    </head>

    <body class="w3-content">

        <header class="w3-display-container w3-green w3-xlarge w3-padding w3-margin-bottom">

            <span class="w3-large">
                <a href="javascript:open_sidebar()" class="w3-text-white w3-hover-grey"><i class="fa fa-bars fa-lg" aria-hidden="true"></i></a>
            </span>

            <a href="<?= $this->toolbox->create_url(); ?>" class="w3-text-white w3-hover-grey">
                eBiblio
            </a>

            <span class="w3-large w3-right">
                <a href="<?= $this->toolbox->create_url(); ?>" class="w3-text-white w3-hover-grey"><i class="fa fa-home fa-lg" aria-hidden="true"></i></a>

                <a href="<?= $this->toolbox->create_url('sign_out'); ?>" class="w3-text-white w3-hover-grey"><i class="fa fa-sign-out fa-lg" aria-hidden="true"></i></a>
            </span>

        </header>

        <div class="w3-sidebar w3-bar-block w3-border-right w3-text-green" id="sidebar">

            <button class="w3-button w3-block w3-left-align" onclick="accordion('manage_books')">
                <i class="fa fa-book" aria-hidden="true"></i>
                Gérer les livres
                <i class="fa fa-caret-down" aria-hidden="true"></i>
            </button>

            <div class="w3-hide w3-white" id="manage_books">

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('upload_book'); ?>">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Ajouter un livre
                </a>

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('delete_book'); ?>">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    Supprimer un livre
                </a>

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('update_bookinfo'); ?>">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    Mettre à jour le titre ou l'auteur
                </a>

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('undelete_book'); ?>">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                    Annuler la suppression
                </a>

            </div>

            <button class="w3-button w3-block w3-left-align" onclick="accordion('my_account')">
                <i class="fa fa-user" aria-hidden="true"></i>
                Gérer mon compte
                <i class="fa fa-caret-down" aria-hidden="true"></i>
            </button>

            <div class="w3-hide w3-white" id="my_account">

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('change_email'); ?>">
                    <i class="fa fa-at" aria-hidden="true"></i>
                    Changer d'adresse e-mail
                </a>

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('change_password'); ?>">
                    <i class="fa fa-key" aria-hidden="true"></i>
                    Changer de mot de passe
                </a>

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('change_options'); ?>">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    Changer l'option
                </a>

                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('delete_user'); ?>">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    Supprimer le compte
                </a>

            </div>

            <?php if ($this->toolbox->is_admin_user()): ?>
                <a class="w3-bar-item w3-button" href="<?= $this->toolbox->create_url('get_users'); ?>">
                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                    Gérer les utilisateurs
                </a>
            <?php endif; ?>

        </div>

        <div class="w3-overlay w3-animate-opacity" onclick="close_sidebar()" style="cursor:pointer" id="overlay"></div>
