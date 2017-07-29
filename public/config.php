<?php
return [
    'development' => [
        'base_path'  => __DIR__ . '/../application',
        'base_url'   => sprintf('https://%s/ebiblio', $_SERVER['HTTP_HOST']), // the domain subpath MUST BE the same as the one used in .htaccess
        'data_path'  => __DIR__ . '/../application/data.development',
        'email_from' => $_SERVER['SERVER_ADMIN'],
    ],
    'production'  => [
        'base_path'  => __DIR__ . '/../../cgi-bin/ebiblio',
        'base_url'   => sprintf('https://%s/ebiblio', $_SERVER['HTTP_HOST']), // the domain subpath MUST BE the same as the one used in .htaccess
        'data_path'  => __DIR__ . '/../../cgi-bin/ebiblio/data.production',
        'email_from' => $_SERVER['SERVER_ADMIN'],
    ],
];
