<?php
session_start([
    'cookie_lifetime' => 10,
]);

$file = '../restricted/data/books/Eye of the Needle_Ken Follett_2.epub';

header('Content-Description: File Transfer');
header('Content-Type: application/epub+zip');
header('Content-Disposition: attachment; filename="' . basename($file).'"');
header('Cache-Control: must-revalidate');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
