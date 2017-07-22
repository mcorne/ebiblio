<?php
/* @var $this toolbox */

if ($book_id = $this->get_input('id') and $bookinfo = $this->get_bookinfo($book_id)) {
    $filename  = $this->create_book_filename($bookinfo['name']);

    header('Content-Description: File Transfer');
    header('Content-Type: application/epub+zip');
    header('Content-Disposition: attachment; filename="' . $bookinfo['name'] .'"');
    header('Cache-Control: must-revalidate');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
}
