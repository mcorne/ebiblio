<?php
/* @var $this toolbox */

if ($book_id = $this->get_input('id') and $bookinfo = $this->get_bookinfo($book_id)) {
    $filename  = $this->create_cover_filename($bookinfo['name'], $bookinfo['cover_ext']);
    $mime_info = mime_content_type($filename);

    header("Content-type: $mime_info");
    header('Content-length: '. filesize($filename));
    readfile($filename);
}

exit;
