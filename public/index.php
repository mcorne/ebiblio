<?php
// exit('eBiblio is down for maintenance. Sorry for the inconvenience. Please, come back soon.');

$environment = getenv('ENVIRONMENT');
$subpath     = $environment == 'production' ? '/../../cgi-bin/ebiblio' : '/../application';
$base_path   = realpath(__DIR__ . $subpath);

set_include_path($base_path);

$base_url = 'https://' . $_SERVER['HTTP_HOST'];

if (strpos($_SERVER['REQUEST_URI'], '/ebiblio') === 0) {
    $base_url .= '/ebiblio';
}

try {
    require_once 'controller.php';
    $controller = new controller($base_path, $base_url, $environment);
    $controller->run_application();
} catch (Exception $exception) {
    echo 'There is a technical problem. Sorry for the inconvenience. Please contact the administrator.';
    echo '<br>';
    echo $exception->getMessage();
}
