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

require_once 'toolbox.php';

$toolbox = new toolbox($base_path, $base_url, $environment);
$toolbox->run_application();
