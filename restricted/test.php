<?php
require_once '../common/toolbox.php';

// TODO: comment or remove on production system !!!

// ex: https://localhost/ebiblio/common/test_method?method=unzip_book&args[]=Eye of the Needle_Ken Follett.epub
$result = call_user_func_array(array(new toolbox(), $_GET['method']), $_GET['args']);

echo '<pre>';
print_r($result);
