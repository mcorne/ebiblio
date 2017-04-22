<?php
class toolbox
{
    const BOOKLIST_FILENAME  = 'data/booklist.php';
    const BOOKNAME_SEPARATOR = '_';
    const MAX_FILE_SIZE      = 5242880; // 5 Mo

    public function add_book() // TODO: finish !!!
    {
        $filename = $this->upload_book();
        $directory = $this->unzip_book($filename);
        $bookinfo = $this->extract_book_info($directory);
        $bookname = $this->add_book_in_booklist($bookinfo);
    }

    /**
     *
     * @param array $bookinfo
     * @return string
     */
    public function add_book_in_booklist($bookinfo)
    {
        $booklist = $this->read_booklist();

        end($booklist);
        $book_id = key($bookinfo) ?: 0;

        $bookinfo['name']  = $this->create_bookname($bookinfo, $book_id);
        $bookinfo['added'] = date('Y-m-d H:i:s');

        ksort($bookinfo);
        $booklist[] = $bookinfo;

        $this->write_booklist($booklist);

        return $bookinfo['name'];
    }

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
    public function convert_string_to_ascii($string)
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
     * @param array $bookinfo
     * @param int $book_id
     * @return string
     */
    public function create_bookname($bookinfo, $book_id)
    {
        $title  = $this->convert_string_to_ascii($bookinfo['info']);
        $author = $this->convert_string_to_ascii($bookinfo['author']);

        $bookname = sprintf('%s_%s_%d', $title, $author, $book_id);

        return $bookname;
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
            $displayed .= sprintf(' (n°&nbsp;%s)', $book['number']);
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
        $len  = strlen($password);
        $text = $password . '$apr1$' . $salt;
        $bin  = pack("H32", md5($password . $salt . $password));

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
     * @param string $directory
     * @return string
     */
    public function extract_book_cover($directory)
    {
        $file_pattern = '*cover*.{bmp,gif,jpg,jpeg,png,tif,tiff,svg}';

        if ($filenames = glob("$directory/*/$file_pattern", GLOB_BRACE) or
            $filenames = glob("$directory/*/*/$file_pattern", GLOB_BRACE)
        ) {
            return current($filenames);
        }
    }

    /**
     *
     * @param string $directory
     * @return array
     * @throws Exception
     * @see http://www.idpf.org/epub/20/spec/OPF_2.0.1_draft.htm#Section2.2
     * @see http://www.idpf.org/epub/31/spec/epub-packages.html#sec-metadata-elem
     * @see https://github.com/IDPF/epub3-samples
     */
    public function extract_book_info($directory)
    {
        if (! $filenames = glob("$directory/*/*.opf")) {
            throw new Exception("Impossible d'extraire le fichier OPF");
        }

        $opf_filename = current($filenames);

        if (! $content = file_get_contents($opf_filename)) {
            throw new Exception("Impossible de lire le fichier OPF");
        }

        if (! $metadata = $this->extract_xml_tag_data('metadata', $content)) {
            throw new Exception("Impossible d'extraire les metadonnées");
        }

        if (! $bookinfo['title'] = $this->extract_xml_tag_data('title', $metadata)) {
            throw new Exception("Impossible d'extraire le titre");
        }

        $bookinfo['author']      = $this->extract_xml_tag_data('creator', $metadata);
        $bookinfo['cover']       = $this->extract_book_cover($directory);
        $bookinfo['date']        = $this->extract_xml_tag_data('date', $metadata);
        $bookinfo['deleted']     = null;
        $bookinfo['description'] = $this->extract_xml_tag_data('description', $metadata);
        $bookinfo['identifier']  = $this->extract_xml_tag_data('identifier', $metadata);
        $bookinfo['language']    = $this->extract_xml_tag_data('language', $metadata);
        $bookinfo['publisher']   = $this->extract_xml_tag_data('publisher', $metadata);
        $bookinfo['rights']      = $this->extract_xml_tag_data('rights', $metadata);
        $bookinfo['subject']     = $this->extract_xml_tag_data('subject', $metadata);

        return $bookinfo;
    }

    /**
     *
     * @param string $tag
     * @param string $content
     * @return string
     */
    public function extract_xml_tag_data($tag, $content)
    {
        $pattern = "~<((?:\w+:)?$tag)( [^>]*)?>(.+?)</\\1>~is";

        if (preg_match($pattern, $content, $match)) {
            return $match[3];
        }
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
     * @return string
     * @see http://php.net/manual/en/features.file-upload.errors.php
     */
    private function get_upload_error_message()
    {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;

            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;

            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;

            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;

            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }

        return $message;
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

    /**
     *
     * @return array
     */
    public function read_booklist()
    {
        if (file_exists(toolbox::BOOKLIST_FILENAME)) {
            $books = include toolbox::BOOKLIST_FILENAME;
        } else {
            $books = array();
        }

        return $books;
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
        $pathinfo     = pathinfo($old_filename);
        $bookname     = $this->assemble_bookname($title, $author, $pathinfo['extension']);
        $new_filename = $pathinfo['dirname'] . '/' . $bookname;

        rename($old_filename, $new_filename); // TODO: fix to manage duplicates !!!
    }

    /**
     *
     * @param array $bookinfo
     * @param int $book_id
     * @param int
     */
    public function replace_book_in_booklist($bookinfo, $book_id)
    {
        $booklist = $this->read_booklist();

        $old_bookname = $bookinfo['name'];

        $bookinfo['name']  = $this->create_bookname($bookinfo, $book_id);
        $bookinfo['updated'] = date('Y-m-d H:i:s');
        ksort($bookinfo);

        $booklist[$book_id] = $bookinfo;

        $this->write_booklist($booklist);

        return $old_bookname;
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

        $title  = current($parts);
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

    /**
     *
     * @param string $filename
     * @return string
     */
    public function unzip_book($filename)
    {
        $zip = new ZipArchive;

        $directory = 'unzip/' . basename($filename);

        if (! $zip->open($filename)) {
            throw new Exception("Impossible d'ouvrir le fichier");
        }

        if (! $zip->extractTo($directory)) {
            throw new Exception('Impossible de décompresser le fichier');
        }

        $zip->close();

        return $directory;
    }

    /**
     *
     * @return string
     */
    public function upload_book()
    {
        $destination = 'tmp/' . md5(basename($_FILES['filename']['name']));

        if (pathinfo($target_file, PATHINFO_EXTENSION) != 'epub') {
            throw new Exception('Extension de fichier non valide');
        }

        if ($_FILES['filename']['size'] > toolbox::MAX_FILE_SIZE) {
            throw new Exception('Fichier trop volumineux');
        }

        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
            throw new Exception($this->get_upload_error_message());
        }

        if (! move_uploaded_file($_FILES['filename']['tmp_name'], $destination)) {
            throw new Exception('Impossible de déplacer le fichier');
        }

        return $destination;
    }

    /**
     *
     * @param array $booklist
     * @throws Exception
     */
    public function write_booklist($booklist)
    {
        $exported = var_export($booklist, true);
        $content  = "<?php\n$exported;\n";

        if (! file_put_contents(toolbox::BOOKLIST_FILENAME, $content)) {
            throw new Exception('Impossible de lire la liste des livres');
        }
    }
}
