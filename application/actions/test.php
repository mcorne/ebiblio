<?php
// TODO: fix or remove !!!

// used for unit testing only
// MUST BE uncommented in production mode !!!
exit;

if (! $data_dir = realpath(__DIR__ . '/data')) {
    echo 'Invalid data directory';
    exit;
}

define('DATA_DIR', $data_dir);

require_once '../common/toolbox.php';
$toolbox = new toolbox(DATA_DIR);
$result = $toolbox->unit_test();

echo '<pre>';
print_r($result);
