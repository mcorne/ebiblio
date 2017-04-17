<?php
class toolbox
{
    const BOOKNAME_SEPARATOR = '_';

    /**
     *
     * @param string $title
     * @param string $author
     * @param string $extension
     * @return array
     */
    public function assemble_bookname($title, $author, $extension)
    {
        $title  = $this->convert_string_to_ascii($title)  ?: '--sans titre--';
        $author = $this->convert_string_to_ascii($author) ?: '--sans auteur--';

        $bookname = $title . toolbox::BOOKNAME_SEPARATOR . $author . '.' . $extension;

        return $bookname;
    }

    /**
     *
     * Note that iconv() is not used as it produces different results across PHP versions,
     * eg $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
     *
     * @param string $string
     * @return string
     */
    function convert_string_to_ascii($string)
    {
        static $search, $replace;

        if (! isset($search)) {
            $from = 'áàâãäåāăąÀÁÂÃÄÅĀĂĄćčçĆČÇĐÐèéêёëēĕėęěÈÊËЁĒĔĖĘĚğĞıìíîïìĩīĭÌÍÎÏЇÌĨĪĬłŁńňñŃŇÑòóôõöōŏőøÒÓÔÕÖŌŎŐØřŘšşșŚŠŞȘŢùúûüũūŭůÙÚÛÜŨŪŬŮýÿÝŸžżźŽŻŹ';
            $to   = 'aaaaaaaaaAAAAAAAAAcccCCCDDeeeeeeeeeeEEEEEEEEEgGiiiiiiiiiIIIIIIIIILLnnnNNNoooooooooOOOOOOOOOrRsssSSSSTuuuuuuuuUUUUUUUUyyYYzzzZZZ';

            preg_match_all('~\pL~u', $from, $matches);
            $search = current($matches);

            $replace = str_split($to);

            $search  = array_merge($search,  array('æ', 'Æ', 'œ', 'Œ', 'ß' ));
            $replace = array_merge($replace, array('ae', 'ae', 'oe', 'OE', 'ss'));
        }

        $ascii = str_replace($search, $replace, $string);
        $ascii = preg_replace("~[^a-z0-9 -]+~i", ' ', $ascii);
        $ascii = trim($ascii);

        return $ascii;
    }

    /**
     *
     * @param string $old_filename
     */
    public function delete_book($old_filename)
    {
        $new_filename = $old_filename . '.DEL';

        rename($old_filename, $new_filename);
    }

    /**
     *
     * @param mixed $book
     * @return string
     */
    public function display_bookname($book, $exclude_author = false)
    {
        if (is_string($book)) {
            $book = $this->split_bookname($book);
        }

        $displayed = $book['title'];

        if ($book['number']) {
            $displayed .= sprintf(' (n° %s)', $book['number']);
        }

        if (! $exclude_author and $book['author']) {
            $displayed .= sprintf(', %s', $book['author']);
        }

        return $displayed;
    }

    /**
     * APR1-MD5 encryption method (windows compatible)
     *
     * @param string $password
     * @return string
     * @see https://www.virendrachandak.com/techtalk/using-php-create-passwords-for-htpasswd-file/
     */
    public function encrypt_password($password)
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
    public function get_booklist($sorting)
    {
        $filenames   = glob('books/*.epub');
        $books       = [];
        $sort_column = [];

        foreach ($filenames as $filename) {
            $bookname = basename($filename);

            $book = ['filename' => $filename];
            $book += $this->split_bookname($bookname);

            $books[]  = $book;

            $sort_column[] = $sorting == 'title' ? $book['title'] : $book['author'];
        }

        array_multisort($sort_column, SORT_ASC, $books);

        return $books;
    }

    /**
     *
     * @return array
     */
    public function get_deleted_booknames()
    {
        $filenames = glob('books/*.epub.DEL');
        $booknames = array_map('basename', $filenames);

        return $booknames;
    }

    /**
     *
     * @param string $bookname
     * @return string
     */
    public function get_filename($bookname)
    {
        $bookname = urldecode($bookname);

        $filename = dirname(__FILE__) . "/../restricted/books/$bookname";

        if (! file_exists($filename)) {
            return;
        }

        return $filename;
    }

    /**
     *
     * @param string $key
     * @return string
     */
    public function get_input($key)
    {
        $input = $_POST + $_GET;

        if (array_key_exists($key, $input)) {
            return trim($input[$key]);
        }
    }

    /**
     *
     * @return bool
     */
    public function is_post()
    {
        $is_post = strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';

        return $is_post;
    }

    public function redirect_to_booklist()
    {
        header('Location: /ebiblio/restricted/booklist.php');
        exit;
    }

    /**
     *
     * @param string $old_filename
     * @param string $title
     * @param string $author
     */
    public function rename_book($old_filename, $title, $author)
    {
        $pathinfo = pathinfo($old_filename);

        $bookname = $this->assemble_bookname($title, $author, $pathinfo['extension']);
        $new_filename = $pathinfo['dirname'] . '/' . $bookname;

        rename($old_filename, $new_filename); // TODO: fix to manage duplicates !!!
    }

    /**
     *
     * @param string $bookname
     * @return array
     */
    public function split_bookname($bookname)
    {
        list($basename) = explode('.', $bookname);

        $parts = explode(toolbox::BOOKNAME_SEPARATOR, $basename);

        $title = current($parts);
        $author = next($parts) ?: null;
        $number = next($parts) ?: null;

        return [
            'author'   => $author,
            'bookname' => $bookname,
            'number'   => $number,
            'title'    => $title,
        ];
    }

    /**
     *
     * @param string $old_filename
     */
    public function undelete_book($old_filename)
    {
        $new_filename = str_replace('.DEL', '', $old_filename);

        rename($old_filename, $new_filename);
    }
}
