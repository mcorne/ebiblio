<?php
// uncomment the line below when ebiblio is unavailable for maintenance
// exit('eBiblio is down for maintenance. Sorry for the inconvenience. Please, come back soon.');

$config = require 'config.php';

$environment = getenv('ENVIRONMENT');

if (! isset($config[$environment])) {
    exit("Invalid environment: $environment");
}

$config = $config[$environment];

set_include_path($config['base_path']);

try {
    require_once 'controller.php';
    $controller = new controller($config);
    $controller->run_application();
} catch (Exception $exception) {
    echo 'There is a technical problem. Sorry for the inconvenience. Please contact the administrator.';
    echo '<br>';
    echo $exception->getMessage();
}
