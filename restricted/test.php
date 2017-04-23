<?php
require_once '../common/toolbox.php';

// used for unit testing only
// MUST BE uncommented in production mode !!!
// exit; !!!

$toolbox = new toolbox();
$result = $toolbox->unit_test();

echo '<pre>';
print_r($result);
