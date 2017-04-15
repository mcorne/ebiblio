<?php
/**
 *
 * @param string $sorting
 * @return array
 */
function get_book_names($sorting)
{
    $filenames   = glob('books/*.epub');
    $booknames   = [];
    $sort_column = [];

    foreach ($filenames as $filename) {
        $basename = basename($filename, '.epub');
        list($title, $author) = explode(' - ', $basename);

        $booknames[] = [
            'author'   => $author,
            'basename' => $basename,
            'filename' => $filename,
            'title'    => $title,
        ];

        $sort_column[] = $sorting == 'title' ? $title : $author;
    }

    array_multisort($sort_column, SORT_ASC, $booknames);

    return $booknames;
}

/**
 *
 * @return bool
 */
function is_post()
{
    $is_post = strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';

    return $is_post;
}

function redirect_to_booklist()
{
    header('Location: /ebiblio/restricted/booklist.php');
    exit;
}

/**
 *
 * @param string $bookname
 * @return string
 */
function validate_bookname($bookname)
{
    $bookname = urldecode($bookname);
    $filename = dirname(__FILE__) . "/../restricted/books/$bookname.epub";

    if (file_exists($filename)) {
        return $filename;
    }
}
