<?php
/**
 * APR1-MD5 encryption method (windows compatible)
 *
 * @param string $password
 * @return string
 * @see https://www.virendrachandak.com/techtalk/using-php-create-passwords-for-htpasswd-file/
 */
function encrypt_password($password)
{
    $salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
    $len = strlen($password);
    $text = $password . '$apr1$' . $salt;
    $bin = pack("H32", md5($password . $salt . $password));

    for($i = $len; $i > 0; $i -= 16) {
        $text .= substr($bin, 0, min(16, $i));
    }

    for($i = $len; $i > 0; $i >>= 1) {
        $text .= ($i & 1) ? chr(0) : $password{0};
    }

    $bin = pack("H32", md5($text));

    for($i = 0; $i < 1000; $i++) {
        $new = ($i & 1) ? $password : $bin;
        if ($i % 3) $new .= $salt;
        if ($i % 7) $new .= $password;
        $new .= ($i & 1) ? $bin : $password;
        $bin = pack("H32", md5($new));
    }

    $tmp = '';

    for ($i = 0; $i < 5; $i++) {
        $k = $i + 6;
        $j = $i + 12;
        if ($j == 16) $j = 5;
        $tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
    }

    $tmp = chr(0) . chr(0) . $bin[11] . $tmp;
    $tmp = strtr(
        strrev(substr(base64_encode($tmp), 2)),
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");

    $encrypted = "$" . "apr1" . "$" . $salt . "$" . $tmp;

    return $encrypted;
}

/**
 *
 * @param string $sorting
 * @return array
 */
function get_booklist($sorting)
{
    $filenames   = glob('books/*.epub');
    $booklist   = [];
    $sort_column = [];

    foreach ($filenames as $filename) {
        $basename = basename($filename, '.epub');
        list($title, $author) = explode(' - ', $basename);

        $booklist[] = [
            'author'   => $author,
            'basename' => $basename,
            'filename' => $filename,
            'title'    => $title,
        ];

        $sort_column[] = $sorting == 'title' ? $title : $author;
    }

    array_multisort($sort_column, SORT_ASC, $booklist);

    return $booklist;
}

/**
 *
 * @return array
 */
function get_deleted_booknames()
{
    $filenames = glob('books/*.epub.DEL');
    $booknames = [];

    foreach ($filenames as $filename) {
        $booknames[] = basename($filename, '.epub.DEL');
    }

    return $booknames;
}

/**
 *
 * @param string $bookname
 * @return string
 */
function get_filename($bookname, $is_deleted = false)
{
    $bookname = urldecode($bookname);

    $filename = dirname(__FILE__) . "/../restricted/books/$bookname.epub";

    $suffix = $is_deleted ? '.DEL' : null;

    if (! file_exists($filename . $suffix)) {
        return;
    }

    return $filename;
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
