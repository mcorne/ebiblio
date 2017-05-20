<?php
class toolbox
{
    const MAX_FILE_SIZE     = 10 * 1024 * 1024;     // 10 Mo
    const REPLY_TO_EMAIL    = 'ebiblio@micmap.com';
    const SESSION_LIFE_TIME = 3600;                 // 1 hour

    /**
     *
     * @var string
     */
    public $accounts;

    /**
     *
     * @var string
     */
    public $accounts_filename;

    /**
     *
     * @var string
     */
    public $base_path;

    /**
     *
     * @var string
     */
    public $base_url;

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
     * @var string
     */
    public $environment;

    /**
     *
     * @param string $base_path
     * @param string $base_url
     * @param string $environment
     */
    public function __construct($base_path, $base_url, $environment)
    {
        $this->base_path   = $base_path;
        $this->base_url    = $base_url;
        $this->environment = $environment;
        $this->data_dir    = sprintf('%s/data.%s', $base_path, $environment);

        $this->accounts_filename = sprintf('%s/accounts.php', $this->data_dir);
        $this->booklist_filename = sprintf('%s/booklist.php', $this->data_dir);

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
     * @param string $old_email
     * @param string $password
     * @param string $new_email
     */
    public function change_email($old_email, $password, $new_email)
    {
        if (! $old_email or ! $password or ! $new_email) {
            throw new Exception('Tous les champs sont obligatoires.');
        }

        if (! $this->is_registered_account($old_email, $password)) {
            throw new Exception('Adresse e-mail actuelle inconnue ou mot de passe incorrect.');
        }

        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Nouvelle adresse e-mail incorrecte.');
        }

        $this->replace_account_email($old_email, $new_email);
    }

    /**
     *
     * @param string $email
     * @param string $old_password
     * @param string $new_password
     */
    public function change_password($email, $old_password, $new_password)
    {
        if (! $email or ! $old_password or ! $new_password) {
            throw new Exception('Tous les champs sont obligatoires.');
        }

        if (! $this->is_registered_account($email, $old_password)) {
            throw new Exception('Adresse e-mail inconnue ou mot de passe actuel incorrect.');
        }

        if (strlen($new_password) < 8 or ! preg_match('~\d~', $new_password) or ! preg_match('~[a-z]~', $new_password)) {
            throw new Exception('Le nouveau mot de passe doit contenir au moins 8 catactères avec des lettres et des chiffres.');
        }

        $this->replace_account_password($email, $new_password, false);
    }

    /**
     *
     * @param string $action
     * @return string
     */
    public function create_action_filename($action)
    {
        $filename = sprintf('%s/actions/%s.php', $this->base_path, $action);

        return $filename;
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
     * @return string
     */
    public function create_random_password()
    {
        $digits    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ._';
        $max_index = strlen($digits) - 1;

        $password = '';

        for ($i = 0; $i < 8; $i++) {
            $index     = random_int(0, $max_index);
            $password .= $digits[$index];
        }

        return $password;
    }

    /**
     *
     * @param string $uri
     * @param array $params
     * @return string
     */
    public function create_url($uri = null, $params = [])
    {
        $url = $this->base_url;

        if ($uri) {
            $url .= "/$uri";
        }

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
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
     * @param string $encoded
     * @return array
     */
    public function decode_uri($encoded)
    {
        if ($url_decoded = urldecode($encoded) and $uri = base64_decode($url_decoded, true)) {
            return $uri;
        }
    }

    /**
     *
     * @param string $book_id
     */
    public function delete_book($book_id)
    {
        $booklist = $this->read_booklist();
        $booklist[$book_id]['deleted'] = $this->get_datetime();
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
     * @param string $exception
     */
    public function display_exception($exception)
    {
        $message = $exception->getMessage();
        require $this->base_path . '/common/exception.php';
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
     *
     * @return string
     */
    public function encode_uri()
    {
        $uri = str_replace('/ebiblio', '', $_SERVER['REQUEST_URI']);

        if ($uri = trim($uri, '/')) {
            $encoded = base64_encode($uri);
            return $encoded;
        }
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
            throw new Exception("Impossible d'extraire le fichier OPF.");
        }

        $opf_filename = current($filenames);

        if (! $content = file_get_contents($opf_filename)) {
            throw new Exception("Impossible de lire le fichier OPF.");
        }

        if (! $metadata = $this->extract_xml_tag_data('metadata', $content)) {
            throw new Exception("Impossible d'extraire les metadonnées.");
        }

        if (! $bookinfo['title'] = $this->extract_xml_tag_data('title', $metadata)) {
            throw new Exception("Impossible d'extraire le titre.");
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
     * Fixes the booklist
     *
     * Reading the booklist right after writing it will not reflect the changes due to disk caching latency (?).
     * This happens on the production server but not on the development box.
     * Any change to the booklist must then be passed back to the redirect URL to display the booklist!
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
                if (isset($booklist[$book_id])) {
                    $booklist[$book_id]['deleted'] = null;
                }
                break;
        }

        return $booklist;
    }

    /**
     *
     * @return string
     */
    public function get_action()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/ebiblio', '', $path);

        if (! $action = trim($path, '/') or ! file_exists($this->create_action_filename($action))) {
            $action = 'get_booklist';
        }

        return $action;
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
            if (! $deleted and ! $bookinfo['deleted'] or $deleted and $bookinfo['deleted']) {
                $sort_column[]   = $sorting == 'title' ? $bookinfo['title'] : $bookinfo['author'];
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
     * @return string
     */
    public function get_cover_image_source($bookname, $extension)
    {
        if ($extension) {
            $bookname = pathinfo($bookname, PATHINFO_FILENAME);
            $image_source = "$bookname.$extension";

            return $image_source;
        }
    }

    /**
     *
     * @param int $timestamp
     * @return string
     */
    public function get_datetime($timestamp = null)
    {
        $datetime = date('Y-m-d H:i:s', $timestamp ?: time());

        return $datetime;
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
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                break;

            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                break;

            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded.";
                break;

            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded.";
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder.";
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk.";
                break;

            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension.";
                break;

            default:
                $message = "Unknown upload error.";
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
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function is_registered_account($email, $password = null)
    {
        $accounts = $this->read_accounts();

        if (! isset($accounts[$email])) {
            $this->reset_session();
            return false;
        }

        $account = $accounts[$email];

        $current_datetime = $this->get_datetime();

        if ($account['start_date'] > $current_datetime or
            $account['end_date'] and $account['end_date'] < $current_datetime
        ) {
            $this->reset_session();
            return false;
        }

        if (! $password) {
            return true;
        }

        if ($account['password_end_date'] and $account['password_end_date'] < $current_datetime or
            ! password_verify($password, $account['password'])
        ) {
            $this->reset_session();
            return false;
        }

        return true;
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
     * @param array $bookinfo
     * @param string $tmp_book_filename
     * @param string $cover_filename
     * @return string
     */
    public function update_booklist($bookinfo, $tmp_book_filename, $cover_filename)
    {
        $book_id  = md5_file($tmp_book_filename);
        $booklist = $this->read_booklist();

        if (isset($booklist[$book_id])) {
            $bookinfo['updated'] = $this->get_datetime();
            $previous_bookinfo   = $booklist[$book_id];
            $bookinfo            += $previous_bookinfo;
        } else {
            $bookinfo['created'] = $this->get_datetime();
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
     * @return string
     * @throws Exception
     */
    public function read_accounts()
    {
        if (! is_null($this->accounts)) {
            return $this->accounts;
        }

        if (! file_exists($this->accounts_filename)) {
            throw new Exception("Impossible de lire le fichier des comptes utilisateurs.");
        }

        $this->accounts = include $this->accounts_filename;

        return $this->accounts;
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
     * @param string $uri
     * @param array $params
     */
    public function redirect($uri = null, $params = [])
    {
        $url = $this->create_url($uri, $params);
        header("Location: $url");
        exit;
    }

    /**
     *
     * @param string $action
     * @param string $book_id
     * @param array $bookinfo
     */
    public function redirect_to_booklist($action = null, $book_id = null, $bookinfo = null)
    {
        $uri = 'get_booklist';


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

        $this->redirect($uri, $params);
    }

    /**
     *
     * @param string $old_email
     * @param string $new_email
     */
    public function replace_account_email($old_email, $new_email)
    {
        $accounts = $this->read_accounts();

        $accounts[$new_email] = $accounts[$old_email];
        unset($accounts[$old_email]);

        $this->write_accounts($accounts);
    }

    /**
     *
     * @param string $email
     * @param string $password
     * @param bool $is_temp_password
     */
    public function replace_account_password($email, $password, $is_temp_password)
    {
        $accounts = $this->read_accounts();

        $accounts[$email]['password'] = password_hash($password, PASSWORD_DEFAULT);

        if ($is_temp_password) {
            $accounts[$email]['password_end_date'] = $this->get_datetime(time() + 24 * 3600);
        } else {
            $accounts[$email]['password_end_date'] = null;
        }

        $this->write_accounts($accounts);
    }

    public function reset_session()
    {
        unset($_SESSION['email'], $_SESSION['password']);
    }

    /**
     *
     * @return string
     */
    public function retrieve_book()
    {
        $tmp_book_filename = sprintf('%s/tmp/%s', $this->data_dir, md5(basename($_FILES['filename']['name'])));

        if (pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION) != 'epub') {
            throw new Exception('Extension de fichier non valide.');
        }

        if ($_FILES['filename']['size'] > toolbox::MAX_FILE_SIZE) {
            throw new Exception('Fichier trop volumineux.');
        }

        if ($_FILES['filename']['error'] != UPLOAD_ERR_OK) {
            throw new Exception($this->get_upload_error_message());
        }

        if (! move_uploaded_file($_FILES['filename']['tmp_name'], $tmp_book_filename)) {
            throw new Exception('Impossible de déplacer le fichier.');
        }

        return $tmp_book_filename;
    }

    public function run_application()
    {
        session_start(['cookie_lifetime' => toolbox::SESSION_LIFE_TIME]);

        $action = $this->get_action();

        $this->verify_user_signed_in($action);

        if ($action == 'display_cover' or $action == 'download_book') {
            require $this->create_action_filename($action);
        } else {
            require $this->base_path . '/common/header.php';
            require $this->create_action_filename($action);
            require $this->base_path . '/common/footer.php';
        }

    }

    /**
     *
     * @param string $email
     * @throws Exception
     */
    public function send_password($email)
    {
        if (! $this->is_registered_account($email)) {
            return;
        }

        $subject = 'Votre nouveau mot de passe eBiblio';
        $password = $this->create_random_password();
        $this->replace_account_password($email, $password, true);
        $this->reset_session();

        $url = $this->create_url('change_password', ['email' => $email, 'password' => $password]);

        $message = '
<html>
    <head>
        <title>eBiblio</title>
        <meta charset="UTF-8">
    </head>

    <body>
        Bonjour,<br>
        <br>
        Vous recevez ce message car vous avez demandé un nouveau mot de passe.<br>
        Veuillez cliquer sur le lien suivant pour vous connecter&nbsp;:<br>
        <a href="%1$s">%1$s</a><br>
        <br>
        Si vous avez reçu ce message par erreur, veuillez simplement le supprimer.<br>
        Veuillez ne pas répondre à ce message.<br>
        <br>
        Cordialement,<br>
        eBiblio
    </body>
</html>';

        $message = sprintf($message, $url);

        $headers[] = sprintf('From: %s', toolbox::REPLY_TO_EMAIL);
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers   = implode("\r\n", $headers);

        if (! mail($email, $subject, $message, $headers)) {
            throw new Exception("Impossible d'envoyer le nouveau mot de passe.");
        }
    }

    /**
     *
     * @param string $email
     * @param string $password
     */
    public function signin($email, $password)
    {
        if (! $this->is_registered_account($email, $password)) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        $_SESSION['email']    = $email;
        $_SESSION['password'] = $password;

        if ($encoded = $this->get_input('redirect') and $uri = $this->decode_uri($encoded)) {
            $this->redirect($uri);
        } else {
            $this->redirect();
        }

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
     * @see https://localhost/ebiblio/restricted/test.php?method=update_booklist&args[]={"title":"qqq","author":"sss","name":"zzz"}&args[]=tmp/pg41211-images.epub&args[]=aaa.jpg
     * @see https://localhost/ebiblio/restricted/test.php?method=unzip_book&args[]=Eye of the Needle_Ken Follett.epub
     */
    public function unit_test()
    {
        $args = $_GET['args'] ?? [];

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
            throw new Exception("Impossible d'ouvrir le fichier.");
        }

        if (! $zip->extractTo($tmp_book_dirname)) {
            throw new Exception('Impossible de décompresser le fichier.');
        }

        $zip->close();

        return $tmp_book_dirname;
    }

    /**
     *
     * @return array
     */
    public function upload_book() // TODO: detect if file encrypted
    {
        $tmp_book_filename = $this->retrieve_book();
        $tmp_book_dirname  = $this->unzip_book($tmp_book_filename);
        $bookinfo          = $this->extract_bookinfo($tmp_book_dirname);
        $cover_filename    = $this->extract_book_cover($tmp_book_dirname);

        list($book_id, $bookinfo, $previous_bookinfo) = $this->update_booklist($bookinfo, $tmp_book_filename, $cover_filename);

        $this->move_book($tmp_book_filename, $bookinfo, $previous_bookinfo);
        $this->move_cover($cover_filename, $bookinfo, $previous_bookinfo);
        $this->delete_dir($tmp_book_dirname);

        return [$book_id, $bookinfo];
    }

    /**
     *
     * @param string $action
     */
    public function verify_user_signed_in($action)
    {
        if (isset($_SESSION['email']) or
            in_array($action, ['change_password', 'send_password', 'signin', 'signout'])
        ) {
            return;
        }

        if ($action == 'download_book') {
            // must redirect to the booklist instead of the sign-in page otherwise it gets back to the signin page in a loop (!)
            $this->redirect('get_booklist');
        }

        if ($encoded = $this->encode_uri()) {
            $params = ['redirect' => $encoded];
            $this->redirect('signin', $params);
        }

        $this->redirect('signin');
    }

    /**
     *
     * @param string $accounts
     * @throws Exception
     */
    public function write_accounts($accounts)
    {
        $this->accounts = $accounts;

        $exported = var_export($accounts, true);
        $content  = "<?php\nreturn $exported;\n";

        if (! file_put_contents($this->accounts_filename, $content)) {
            throw new Exception('Impossible de mettre à jour le fichier des comptes utilisateurs.');
        }
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
            throw new Exception('Impossible de mettre à jour la liste des livres.');
        }
    }
}
