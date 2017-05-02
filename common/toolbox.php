<?php
class toolbox
{
    const MAX_FILE_SIZE = 10 * 1024 *1024; // 10 Mo

    /**
     *
     * @var array
     */
    public $booklist;

    /**
     *
     * @var string
     */
    public $booklist_filename;

    /**
     *
     * @var string
     */
    public $data_dir;

    /**
     *
     * @param string $data_dir
     */
    public function __construct($data_dir)
    {
        $this->data_dir = $data_dir;

        $this->booklist_filename = sprintf('%s/booklist.php', $data_dir);

        date_default_timezone_set('UTC');
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
            $from = 'áàâãäåāăąÀÁÂÃÄÅĀĂĄćčçĆČÇĐÐèéêёëēĕėęěÈÊËЁĒĔĖĘĚğĞıìíîïìĩīĭÌÍÎÏЇÌĨĪĬłŁńňñŃŇÑòóôõöōŏőøÒÓÔÕÖŌŎŐØřŘšşșŚŠŞȘŢùúûüũūŭůÙÚÛÜŨŪŬŮýÿÝŸžżźŽŻŹ/';
            $to   = 'aaaaaaaaaAAAAAAAAAcccCCCDDeeeeeeeeeeEEEEEEEEEgGiiiiiiiiiIIIIIIIIILLnnnNNNoooooooooOOOOOOOOOrRsssSSSSTuuuuuuuuUUUUUUUUyyYYzzzZZZ-';

            preg_match_all('~\pL~u', $from, $matches);
            $search = current($matches);

            $replace = str_split($to);

            $search  = array_merge($search,  ['æ' , 'Æ' , 'œ' , 'Œ' , 'ß' ]);
            $replace = array_merge($replace, ['ae', 'ae', 'oe', 'OE', 'ss']);
        }

        $ascii = str_replace($search, $replace, $string);
        $ascii = preg_replace("~[^a-z0-9 -]+~i", ' ', $ascii);
        $ascii = trim($ascii);

        return $ascii;
    }

    /**
     *
     * @param string $bookname
     * @return string
     */
    public function create_book_filename($bookname)
    {
        $filename = sprintf('%s/books/%s', $this->data_dir, $bookname);

        return $filename;
    }

    /**
     *
     * @param array $bookinfo
     * @return string
     */
    public function create_bookname($bookinfo)
    {
        $title    = $this->convert_string_to_ascii($bookinfo['title']);
        $author   = $this->convert_string_to_ascii($bookinfo['author']);

        $bookname = sprintf('%s_%s', $title, $author);
        $bookname = preg_replace('~\s+~', ' ', $bookname);
        $bookname = trim($bookname, '_ ');

        if (! $bookname) {
            // defaults to "ebiblio" if both the title and author could not be converted to ascii
            $bookname = 'ebiblio';
        }

        // truncates the filename due to the 256 bytes file name max length
        $bookname = substr($bookname, 0, 200);

        // concatenates the book number to differenciate possible title/author duplicates in different editions
        $bookname .= '_' . $bookinfo['number'];

        $bookname .= '.epub';

        return $bookname;
    }

    /**
     *
     * @param string $bookname
     * @param string $extension
     * @return string
     */
    public function create_cover_filename($bookname, $extension)
    {
        $bookname = pathinfo($bookname, PATHINFO_FILENAME);
        $filename = sprintf('%s/covers/%s.%s', $this->data_dir, $bookname, $extension);

        return $filename;
    }

    /**
     *
     * @param string $encoded
     * @return array
     */
    public function decode_bookinfo($encoded)
    {
        if ($url_decoded = urldecode($encoded) and
            $json = base64_decode($url_decoded, true) and
            $bookinfo = json_decode($json, true)
        ) {
            return $bookinfo;
        }
    }

    /**
     *
     * @param string $book_id
     */
    public function delete_book($book_id)
    {
        $booklist = $this->read_booklist();
        $booklist[$book_id]['deleted'] = $this->get_date();
        $this->write_booklist($booklist);
    }

    /**
     *
     * @param string $dirname
     */
    public function delete_dir($dirname)
    {
        if ($filenames = glob("$dirname/*")) {
            foreach ($filenames as $filename) {
                if (is_dir($filename)) {
                    $this->delete_dir($filename);
                } else {
                    @unlink($filename);
                }
            }
        }

        @rmdir($dirname);
    }

    /**
     *
     * @param array $bookinfo
     * @return string
     */
    public function display_bookname($bookinfo)
    {
        $bookname = sprintf('%s, %s', $bookinfo['title'], $bookinfo['author']);
        $bookname = trim($bookname, ', ');
        $bookname = htmlspecialchars($bookname);

        return $bookname;
    }

    /**
     *
     * @param array $bookinfo
     * @return string
     */
    public function encode_bookinfo($bookinfo)
    {
        if ($json = json_encode($bookinfo) and $encoded = base64_encode($json)) {
            return $encoded;
        }
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
     * @param string $tmp_book_dirname
     * @return string
     */
    public function extract_book_cover($tmp_book_dirname)
    {
        $file_pattern = '*cover*.{bmp,gif,jpg,jpeg,png,tif,tiff,svg}';

        if ($filenames = glob("$tmp_book_dirname/*/$file_pattern", GLOB_BRACE) or
            $filenames = glob("$tmp_book_dirname/*/*/$file_pattern", GLOB_BRACE) or
            $filenames = glob("$tmp_book_dirname/*/*/*/$file_pattern", GLOB_BRACE)
        ) {
            return current($filenames);
        }
    }

    /**
     *
     * @param string $tmp_book_dirname
     * @return array
     * @throws Exception
     * @see http://www.idpf.org/epub/20/spec/OPF_2.0.1_draft.htm#Section2.2
     * @see http://www.idpf.org/epub/31/spec/epub-packages.html#sec-metadata-elem
     * @see https://github.com/IDPF/epub3-samples
     */
    public function extract_bookinfo($tmp_book_dirname)
    {
        if (! $filenames = glob("$tmp_book_dirname/*/*.opf")) {
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
        $bookinfo['date']        = $this->extract_xml_tag_data('date', $metadata);
        $bookinfo['description'] = $this->extract_xml_tag_data('description', $metadata);
        $bookinfo['identifier']  = $this->extract_xml_tag_data('identifier', $metadata);
        $bookinfo['language']    = $this->extract_xml_tag_data('language', $metadata);
        $bookinfo['publisher']   = $this->extract_xml_tag_data('publisher', $metadata);
        $bookinfo['rights']      = $this->extract_xml_tag_data('rights', $metadata);
        $bookinfo['source']      = $this->extract_xml_tag_data('subject', $metadata);
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
     * @param array $booklist
     * @param string $action
     * @param string $book_id
     * @param string $encoded_bookinfo
     * @return array
     */
    public function fix_booklist($booklist, $action, $book_id, $encoded_bookinfo)
    {
        switch ($action) {
            case 'delete':
                unset($booklist[$book_id]);
                break;

            case 'put':
                if ($bookinfo = $this->decode_bookinfo($encoded_bookinfo)) {
                    $booklist[$book_id] = $bookinfo;
                }
                break;

            case 'undelete':
                var_dump($booklist[$book_id]); // TODO: remove !!!
                if (isset($booklist[$book_id])) {
                    $booklist[$book_id]['delete'] = null;
                    var_dump($booklist[$book_id]); // TODO: remove !!!
                }
                break;
        }

        return $booklist;
    }

    /**
     *
     * @param string $book_id
     * @return array
     */
    public function get_bookinfo($book_id)
    {
        $booklist = $this->read_booklist();

        if (isset($booklist[$book_id])) {
            return $booklist[$book_id];
        }
    }

    /**
     *
     * @param bool $deleted
     * @param string $sorting
     * @param string $action
     * @param string $selected_book_id
     * @param array $encoded_bookinfo
     * @return array
     */
    public function get_booklist($deleted, $sorting = 'title', $action = null, $selected_book_id = null, $encoded_bookinfo = null)
    {
        $booklist = $this->read_booklist();
        $books    = [];

        if ($selected_book_id) {
            $booklist = $this->fix_booklist($booklist, $action, $selected_book_id, $encoded_bookinfo);
        }

        foreach ($booklist as $book_id => $bookinfo) {
            if (! $deleted and ! $bookinfo['deleted'] or
                  $deleted and   $bookinfo['deleted']
            ) {
                $bookinfo['uri']       = '/ebiblio/restricted/data/books/' . $bookinfo['name'];
                $sort_column[]         = $sorting == 'title' ? $bookinfo['title'] : $bookinfo['author'];
                $books[$book_id] = $bookinfo;
            }
        }

        if ($books) {
            array_multisort($sort_column, SORT_ASC, $books);
        }

        return $books;
    }

    /**
     *
     * @param string $bookname
     * @param string $extension
     * @return type
     */
    public function get_cover_image_source($bookname, $extension)
    {
        if ($extension) {
            $bookname = pathinfo($bookname, PATHINFO_FILENAME);
            $image_source = sprintf('/ebiblio/restricted/data/covers/%s.%s', $bookname, $extension);

            return $image_source;
        }
    }

    /**
     *
     * @return string
     */
    public function get_date()
    {
        $date = date('Y-m-d H:i:s');

        return $date;
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
     * @param string $language_code
     * @return string
     */
    public function get_language($language_code)
    {
        list($language_code) = preg_split('~[_-]~', $language_code);

        if (! $language_code = strtolower($language_code)) {
            return;
        }

        switch ($language_code) {
            case 'en':
                $language = 'Anglais';
                break;

            case 'fr':
                $language = 'Français';
                break;

            default:
                $language = sprintf('Autre (%s)', $language_code);
                break;
        }

        return $language;
    }

    /**
     *
     * @return string
     * @see http://php.net/manual/en/features.file-upload.errors.php
     */
    private function get_upload_error_message()
    {
        switch ($_FILES['filename']['error']) {
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
     * @param string $tmp_book_filename
     * @param array $bookinfo
     * @param array $previous_bookinfo
     */
    public function move_book($tmp_book_filename, $bookinfo, $previous_bookinfo)
    {
        if ($previous_bookinfo) {
            $filename = $this->create_book_filename($previous_bookinfo['name']);
            @unlink($filename);
        }

        $filename = $this->create_book_filename($bookinfo['name']);
        @rename($tmp_book_filename, $filename);
    }

    /**
     *
     * @param string $cover_filename
     * @param array $bookinfo
     * @param array $previous_bookinfo
     */
    public function move_cover($cover_filename, $bookinfo, $previous_bookinfo)
    {
        if ($previous_bookinfo and $previous_bookinfo['cover_ext']) {
            $filename = $this->create_cover_filename($previous_bookinfo['name'], $previous_bookinfo['cover_ext']);
            @unlink($filename);
        }

        if ($cover_filename) {
            $filename = $this->create_cover_filename($bookinfo['name'], $bookinfo['cover_ext']);
            @rename($cover_filename, $filename);
        }
    }

    /**
     *
     * @return array
     */
    public function put_book() // TODO: detect if file encrypted !!!
    {
        $tmp_book_filename = $this->upload_book();
        $tmp_book_dirname  = $this->unzip_book($tmp_book_filename);
        $bookinfo          = $this->extract_bookinfo($tmp_book_dirname);
        $cover_filename    = $this->extract_book_cover($tmp_book_dirname);

        list($book_id, $bookinfo, $previous_bookinfo) = $this->put_book_in_booklist($bookinfo, $tmp_book_filename, $cover_filename);

        $this->move_book($tmp_book_filename, $bookinfo, $previous_bookinfo);
        $this->move_cover($cover_filename, $bookinfo, $previous_bookinfo);
        $this->delete_dir($tmp_book_dirname);

        return [$book_id, $bookinfo];
    }

    /**
     *
     * @param array $bookinfo
     * @param string $tmp_book_filename
     * @param string $cover_filename
     * @return string
     */
    public function put_book_in_booklist($bookinfo, $tmp_book_filename, $cover_filename)
    {
        $book_id  = md5_file($tmp_book_filename);
        $booklist = $this->read_booklist();

        if (isset($booklist[$book_id])) {
            $bookinfo['updated'] = $this->get_date();
            $previous_bookinfo   = $booklist[$book_id];
            $bookinfo            += $previous_bookinfo;
        } else {
            $bookinfo['created'] = $this->get_date();
            $bookinfo['deleted'] = null;
            $bookinfo['number']  = count($booklist) + 1;
            $bookinfo['updated'] = null;
            $previous_bookinfo   = null;
        }

        $bookinfo['cover_ext'] = pathinfo($cover_filename, PATHINFO_EXTENSION);
        $bookinfo['deleted']   = null;
        $bookinfo['name']      = $this->create_bookname($bookinfo);
        $bookinfo['source']    = empty($_FILES['filename']['name']) ? null : $_FILES['filename']['name'];

        ksort($bookinfo);

        $booklist[$book_id] = $bookinfo;

        $this->write_booklist($booklist);

        return [$book_id, $bookinfo, $previous_bookinfo];
    }

    /**
     *
     * @return array
     */
    public function read_booklist()
    {
        if (is_null($this->booklist)) {
            $this->booklist = file_exists($this->booklist_filename) ? include $this->booklist_filename : [];
        }

        return $this->booklist;
    }

    /**
     *
     * @param string $action
     * @param string $book_id
     * @param array $bookinfo
     */
    public function redirect_to_booklist($action = null, $book_id = null, $bookinfo = null)
    {
        $uri = '/ebiblio/restricted/get_booklist.php';

        $params = [];

        if ($action) {
            $params['action'] = $action;
        }

        if ($book_id) {
            $params['id'] = $book_id;
        }

        if ($bookinfo) {
            $params['info'] = $this->encode_bookinfo($bookinfo);
        }

        if ($params) {
            $uri .= '?' . http_build_query($params);
        }

        header("Location: $uri");
        exit;
    }

    /**
     *
     * @param string $book_id
     */
    public function undelete_book($book_id)
    {
        $booklist = $this->read_booklist();
        $booklist[$book_id]['deleted'] = null;
        $this->write_booklist($booklist);
    }

    /**
     *
     * @return array
     * @see https://localhost/ebiblio/restricted/test.php?method=create_bookname&args[]={"title":"aaa","author":"bbb","number":"1"}
     * @see https://localhost/ebiblio/restricted/test.php?method=decode_bookinfo&args[]=eyJ0aXRsZSI6ImFhYSIsImF1dGhvciI6ImJiYiIsIm51bWJlciI6IjEifQ==
     * @see https://localhost/ebiblio/restricted/test.php?method=encode_bookinfo&args[]={%22title%22:%22aaa%22,%22author%22:%22bbb%22,%22number%22:%221%22}
     * @see https://localhost/ebiblio/restricted/test.php?method=extract_bookinfo&args[]=unzip/pg41211-images.epub
     * @see https://localhost/ebiblio/restricted/test.php?method=put_book_in_booklist&args[]={"title":"qqq","author":"sss","name":"zzz"}&args[]=tmp/pg41211-images.epub&args[]=aaa.jpg
     * @see https://localhost/ebiblio/restricted/test.php?method=unzip_book&args[]=Eye of the Needle_Ken Follett.epub
     */
    public function unit_test()
    {
        $args = $_GET['args'];

        foreach ($args as &$arg) {
            if ($decoded = json_decode($arg, true)) {
                $arg = $decoded;
            }
        }

        $result = call_user_func_array([$this, $_GET['method']], $args);

        return $result;
    }

    /**
     *
     * @param string $tmp_book_filename
     * @return string
     */
    public function unzip_book($tmp_book_filename)
    {
        $zip = new ZipArchive;

        $tmp_book_dirname = sprintf('%s/unzip/%s', $this->data_dir, basename($tmp_book_filename));

        if (! $zip->open($tmp_book_filename)) {
            throw new Exception("Impossible d'ouvrir le fichier");
        }

        if (! $zip->extractTo($tmp_book_dirname)) {
            throw new Exception('Impossible de décompresser le fichier');
        }

        $zip->close();

        return $tmp_book_dirname;
    }

    /**
     *
     * @return string
     */
    public function upload_book()
    {
        $tmp_book_filename = sprintf('%s/tmp/%s', $this->data_dir, md5(basename($_FILES['filename']['name'])));

        if (pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION) != 'epub') {
            throw new Exception('Extension de fichier non valide');
        }

        if ($_FILES['filename']['size'] > toolbox::MAX_FILE_SIZE) {
            throw new Exception('Fichier trop volumineux');
        }

        if ($_FILES['filename']['error'] != UPLOAD_ERR_OK) {
            throw new Exception($this->get_upload_error_message());
        }

        if (! move_uploaded_file($_FILES['filename']['tmp_name'], $tmp_book_filename)) {
            throw new Exception('Impossible de déplacer le fichier');
        }

        return $tmp_book_filename;
    }

    /**
     *
     * @param array $booklist
     * @throws Exception
     */
    public function write_booklist($booklist)
    {
        $this->booklist = $booklist;

        $exported = var_export($booklist, true);
        $content  = "<?php\nreturn $exported;\n";

        if (! file_put_contents($this->booklist_filename, $content)) {
            throw new Exception('Impossible de lire la liste des livres');
        }
    }
}
