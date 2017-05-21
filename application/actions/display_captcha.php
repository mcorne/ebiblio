<?php
/* @var $this toolbox */

$filename = $this->create_captcha();

header('Content-Type: image/png');
header('Content-Length: ' . filesize($filename));
readfile($filename);
