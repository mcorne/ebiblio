<?php
class toolbox
{
    const MAX_FILE_SIZE     = 100 * 1024 * 1024; // 100 Mo
    const SESSION_LIFE_TIME = 3600;              // 1 hour

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
    public $config;

    /**
     *
     * @var string
     */
    public $users;

    /**
     *
     * @var string
     */
    public $users_filename;

    /**
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->users_filename = sprintf('%s/users.json', $this->config['data_path']);
        $this->booklist_filename = sprintf('%s/booklist.json', $this->config['data_path']);

        date_default_timezone_set('UTC');
    }

    /**
     *
     * @param string $email
     * @param string $new_book_notification
     * @param string $admin
     */
    public function add_user($email, $new_book_notification, $admin)
    {
        $users = $this->read_users();

        $this->validate_email($email, $users);

        $password = $this->create_random_password();

        $users[$email] = [
            'admin'             => $admin,
            'end_date'          => null,
            'options'           => ['new_book_notification' => $new_book_notification],
            'password'          => password_hash($password, PASSWORD_DEFAULT),
            'password_end_date' => $this->get_datetime(24 * 3600),
            'start_date'        => $this->get_datetime(),
        ];

        $this->write_users($users);

        $this->send_new_user_email($email, $password);
    }

    /**
     *
     * @param array $users
     * @return boolean
     */
    public function admin_exists($users)
    {
        foreach ($users as $user) {
            if ($user['admin'] and ! $user['end_date']) {
                return true;
            }
        }

        return false;
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

        if (! $this->is_registered_user($old_email, $password)) {
            throw new Exception('Adresse e-mail actuelle inconnue ou mot de passe incorrect.');
        }

        if ($new_email == $old_email) {
            throw new Exception("Nouvelle adresse e-mail identique à l'adresse actuelle.");
        }

        $users = $this->read_users();
        $users = $this->replace_email($old_email, $new_email, $users);
        $this->write_users($users);
        $this->update_session($new_email);
    }

    /**
     *
     * @param string $new_book_notification
     */
    public function change_options($new_book_notification)
    {
        $users = $this->read_users();

        $users[ $_SESSION['email'] ]['options']['new_book_notification'] = (bool) $new_book_notification;

        $this->write_users($users);
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

        if (! $this->is_registered_user($email, $old_password)) {
            throw new Exception('Adresse e-mail inconnue ou mot de passe actuel incorrect.');
        }

        if (strlen($new_password) < 8 or ! preg_match('~\d~', $new_password) or ! preg_match('~[a-z]~', $new_password)) {
            throw new Exception('Le nouveau mot de passe doit contenir au moins 8 catactères avec des lettres et des chiffres.');
        }

        $this->replace_password($email, $new_password, false);
    }

    /**
     *
     * @param string $action
     * @return string
     */
    public function create_action_filename($action)
    {
        $filename = sprintf('%s/views/%s.php', $this->config['base_path'], $action);

        return $filename;
    }

    /**
     *
     * @param string $bookname
     * @return string
     */
    public function create_book_filename($bookname)
    {
        $filename = sprintf('%s/books/%s', $this->config['data_path'], $bookname);

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
        $filename = sprintf('%s/covers/%s.%s', $this->config['data_path'], $bookname, $extension);

        return $filename;
    }

    /**
     *
     * @param string $digits
     * @param int $length
     * @return string
     */
    public function create_random_password($digits = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ._', $length = 8)
    {
        $max_index = strlen($digits) - 1;

        $password = '';

        for ($i = 0; $i < $length; $i++) {
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
        $url = $this->config['base_url'];

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
    public function decode_data($encoded)
    {
        if ($url_decoded = urldecode($encoded) and
            $json = base64_decode($url_decoded, true) and
            $data = json_decode($json, true)
        ) {
            return $data;
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
     * @param string $email
     * @param string $password
     */
    public function delete_user($email, $password)
    {
        if (! $email or ! $password) {
            throw new Exception('Tous les champs sont obligatoires.');
        }

        if (! $this->is_registered_user($email, $password)) {
            throw new Exception('Adresse e-mail inconnue ou mot de passe incorrect.');
        }

        $this->disable_user($email);

        $this->reset_session();
    }

    /**
     *
     * @param string $email
     */
    public function disable_user($email)
    {
        $users = $this->read_users();

        if (! $users[$email]) {
            throw new Exception('Adresse e-mail inconnue.');
        }

        $users[$email]['end_date'] = $this->get_datetime();

        if (! $this->admin_exists($users)) {
            throw new Exception('Interdiction de désactiver le dernier compte administrateur.');
        }

        $this->write_users($users);
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
     * @param string $email
     */
    public function enable_user($email)
    {
        $users = $this->read_users();

        if (! $users[$email]) {
            throw new Exception('Adresse e-mail inconnue.');
        }

        $users[$email]['end_date'] = null;

        $this->write_users($users);
    }

    /**
     *
     * @param array $data
     * @return string
     */
    public function encode_data($data)
    {
        if ($json = json_encode($data) and $encoded = base64_encode($json)) {
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
     * @param string $metadata
     * @return string
     */
    public function extract_author($metadata)
    {
        if (! $author = $this->extract_xml_tag_data('creator', $metadata)) {
            return;
        }

        if (preg_match_all('~, *~', $author, $matches) == 2) {
            // fixed last name before first name, "eg "Follett, Ken"
            list($last_name, $first_name) = current($matches);
            $author = "$first_name $last_name";
        }

        return $author;
    }

    /**
     *
     * @param string $tmp_book_dirname
     * @return string
     */
    public function extract_book_cover($tmp_book_dirname)
    {
        $file_pattern = '*cover*.{bmp,gif,jpg,jpeg,png,tif,tiff,svg}';

        if ($filenames = glob("$tmp_book_dirname/$file_pattern", GLOB_BRACE) or
            $filenames = glob("$tmp_book_dirname/*/$file_pattern", GLOB_BRACE) or
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
        if (! ($filenames = glob("$tmp_book_dirname/*/*.opf") or
               $filenames = glob("$tmp_book_dirname/*.opf"))
        ) {
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

        $bookinfo['author']      = $this->extract_author($metadata);
        $bookinfo['date']        = $this->extract_date($metadata);
        $bookinfo['description'] = $this->extract_xml_tag_data('description', $metadata);
        $bookinfo['identifier']  = $this->extract_xml_tag_data('identifier', $metadata);
        $bookinfo['language']    = $this->extract_language($metadata);
        $bookinfo['publisher']   = $this->extract_xml_tag_data('publisher', $metadata);
        $bookinfo['rights']      = $this->extract_xml_tag_data('rights', $metadata);
        $bookinfo['source']      = $this->extract_xml_tag_data('subject', $metadata);
        $bookinfo['subject']     = $this->extract_xml_tag_data('subject', $metadata);

        return $bookinfo;
    }

    /**
     *
     * @param string $metadata
     * @return string
     */
    public function extract_date($metadata)
    {
        if ($date = $this->extract_xml_tag_data('date', $metadata)) {
            // removes the timezone, eg "2011-12-28T19:32:06Z"
            list($date) = explode('T', $date);
        }

        return $date;
    }

    /**
     *
     * @param string $metadata
     * @return string
     */
    public function extract_language($metadata)
    {
        if ($language = $this->extract_xml_tag_data('language', $metadata)) {
            // removes the country info from the language code
            $language = substr($language, 0, 2);
            $language = strtolower($language);
        }

        return $language;
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
            $data = html_entity_decode($match[3], ENT_COMPAT, 'UTF-8');
            return $data;
        }
    }

    /**
     *
     * @param string $old_email
     * @param string $new_email
     * @return array
     */
    public function fix_bookinfo_email($old_email, $new_email)
    {
        $booklist = $this->read_booklist();

        foreach ($booklist as &$bookinfo) {
            if ($bookinfo['email'] == $old_email) {
                $bookinfo['email'] = $new_email;
            }
        }

        $this->write_booklist($booklist);
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
     * @return array
     */
    public function get_booklist($deleted, $sorting = 'title')
    {
        $booklist = $this->read_booklist();

        foreach ($booklist as $book_id => $bookinfo) {
            if (! $deleted and ! $bookinfo['deleted'] or $deleted and $bookinfo['deleted']) {
                $sort_column[] = $sorting == 'title' ? $bookinfo['title'] : $bookinfo['author'];
            } else {
                unset($booklist[$book_id]);
            }
        }

        if ($booklist) {
            array_multisort($sort_column, SORT_ASC, $booklist);
        }

        return $booklist;
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
     * @param int $offset
     * @return string
     */
    public function get_datetime($offset = 0)
    {
        $datetime = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + $offset);

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
     */
    public function get_message()
    {
        if ($encoded_message = $this->get_input('message')) {
            $message = $this->decode_data($encoded_message);

            return $message;
        }
    }

    /**
     *
     * @return array
     */
    public function get_options()
    {
        $users = $this->read_users();

        return $users[ $_SESSION['email'] ]['options'];
    }

    /**
     *
     * @return string
     * @see http://php.net/manual/en/features.file-upload.errors.php
     */
    public function get_upload_error_message()
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
     * @param string $email
     * @return array
     */
    public function get_user($email)
    {
        $users = $this->read_users();

        if (isset($users[$email])) {
            return $users[$email];
        }
    }
    /**
     *
     * @param string $name
     * @return array
     */
    public function get_user_email($name)
    {
        $users      = $this->read_users();
        $user_email = null;

        foreach (array_keys($users) as $email) {
            list($local_part) = explode('@', $email);

            if ($local_part == $name) {
                if ($user_email) {
                    // there is more than one email with the same name, eg jsmith@yahoo.com and jsmith@gmail.com
                    return null;
                }

                $user_email = $email;
            }
        }

        return $user_email;
    }

    /**
     *
     * @return array
     */
    public function get_users()
    {
        $users = $this->read_users();

        ksort($users);

        return $users;
    }

    /**
     *
     * @return bool
     */
    public function is_admin_user()
    {
        $users = $this->read_users();

        $email = $_SESSION['email'];

        if (! isset($users[$email])) {
            return false;
        }

        return $users[$email]['admin'];
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
    public function is_registered_user($email, $password = true)
    {
        if (! $user = $this->get_user($email)) {
            return false;
        }

        $current_datetime = $this->get_datetime();

        if ($user['start_date'] > $current_datetime or
            $user['end_date'] and $user['end_date'] < $current_datetime
        ) {
            return false;
        }

        if ($password === true) {
            return true;
        }

        if ($user['password_end_date'] and $user['password_end_date'] < $current_datetime or
            ! password_verify($password, $user['password'])
        ) {
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
     * @return array
     */
    public function read_booklist()
    {
        if (is_null($this->booklist)) {
            $this->booklist = $this->read_file($this->booklist_filename) ?: [];
        }

        return $this->booklist;
    }

    /**
     *
     * @param string $filename
     * @return array
     */
    public function read_file($filename)
    {
        if (file_exists($filename) and
            $json = file_get_contents($filename) and
            $data = json_decode($json, true)
        ) {
            return $data;
        }
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function read_users()
    {
        if (is_null($this->users)) {
            $this->users = $this->read_file($this->users_filename) ?: [];
        }

        return $this->users;
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
     * @param string $message
     */
    public function redirect_with_message($action, $message)
    {
        $encoded_message = $this->encode_data($message);
        $this->redirect($action, ['message' => $encoded_message]);
    }

    /**
     *
     * @param string $old_email
     * @param string $new_email
     * @param array $users
     * @return array
     */
    public function replace_email($old_email, $new_email, $users)
    {
        $this->validate_email($new_email, $users);

        $users[$new_email] = $users[$old_email];
        unset($users[$old_email]);

        $this->fix_bookinfo_email($old_email, $new_email);

        return $users;
    }

    /**
     *
     * @param string $email
     * @param string $password
     * @param bool $is_temp_password
     */
    public function replace_password($email, $password, $is_temp_password)
    {
        $users = $this->read_users();

        $users[$email]['password'] = password_hash($password, PASSWORD_DEFAULT);

        if ($is_temp_password) {
            $users[$email]['password_end_date'] = $this->get_datetime(24 * 3600);
        } else {
            $users[$email]['password_end_date'] = null;
        }

        $this->write_users($users);
    }

    public function reset_session()
    {
        unset($_SESSION['email']);
    }

    /**
     *
     * @return string
     */
    public function retrieve_book()
    {
        $tmp_book_filename = sprintf('%s/tmp/%s', $this->config['data_path'], md5(basename($_FILES['filename']['name'])));

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

    /**
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return type
     */
    public function send_email($email, $subject, $message)
    {
        $headers[] = sprintf('From: %s', $this->config['email_from']);
        $headers[] = sprintf('Return-Path: %s', $this->config['email_from']);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers   = implode("\r\n", $headers);

        $success = @mail($email, $subject, $message, $headers);

        return $success;
    }

    /**
     *
     * @param string $email
     * @param string $book_id
     * @param array $bookinfo
     * @throws Exception
     */
    public function send_new_book_notification($email, $book_id, $bookinfo)
    {
        $subject = 'Nouveau livre dans eBiblio';

        $message = '
<html>
    <head>
        <title>eBiblio</title>
        <meta charset="UTF-8">
    </head>

    <body>
        Bonjour,<br>
        <br>
        Vous recevez ce message car un nouveau livre a été ajouté par %s dans la bibliothèque&nbsp;:<br>
        <a href="%s">%s, %s</a><br>
        <br>
        Si vous ne souhaitez plus recevoir de notification, <a href="%s">veuillez cliquer ici pour changer vos options</a>.<br>
        <br>
        Si vous avez reçu ce message par erreur, veuillez simplement le supprimer.<br>
        Veuillez ne pas répondre à ce message.<br>
        <br>
        Cordialement,<br>
        eBiblio
    </body>
</html>';

        $book_url    = $this->create_url('get_booklist', ['id' => $book_id]);
        $options_url = $this->create_url('change_options');

        $message = sprintf($message, $_SESSION['email'], $book_url, $bookinfo['title'], $bookinfo['author'], $options_url);

        $this->send_email($email, $subject, $message);
        // note that any error when sending the email is silently ignored
    }

    /**
     *
     * @param string $book_id
     * @param array $bookinfo
     */
    public function send_new_book_notifications($book_id, $bookinfo)
    {
        $users = $this->read_users();

        foreach ($users as $email => $user) {
            if ($user['options']['new_book_notification'] and $this->is_registered_user($email) and filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->send_new_book_notification($email, $book_id, $bookinfo);
            }
        }
    }

    /**
     *
     * @param string $email
     * @param string $password
     * @throws Exception
     */
    public function send_new_user_email($email, $password)
    {
        $subject = 'Création de votre compte eBiblio';

        $message = '
<html>
    <head>
        <title>eBiblio</title>
        <meta charset="UTF-8">
    </head>

    <body>
        Bonjour,<br>
        <br>
        Vous recevez ce message car votre compte vient d\'être créé par %s.<br>
        <a href="%s">Veuillez cliquer ici pour l\'activer</a><br>
        <br>
        Si vous avez reçu ce message par erreur, veuillez simplement le supprimer.<br>
        Veuillez ne pas répondre à ce message.<br>
        <br>
        Cordialement,<br>
        eBiblio
    </body>
</html>';

        $url     = $this->create_url('change_password', ['email' => $email, 'password' => $password]);
        $message = sprintf($message, $_SESSION['email'], $url);

        if (! $this->send_email($email, $subject, $message)) {
            throw new Exception("Impossible d'envoyer le mot de passe du nouveau compte.");
        }
    }

    /**
     *
     * @param string $email
     * @throws Exception
     */
    public function send_password($email)
    {
        if (! $this->is_registered_user($email)) {
            return;
        }

        $password = $this->create_random_password();
        $this->replace_password($email, $password, true);
        $this->reset_session();

        $subject = 'Votre nouveau mot de passe eBiblio';

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
        <a href="%s">Veuillez cliquer ici pour vous connecter</a><br>
        <br>
        Si vous avez reçu ce message par erreur, veuillez simplement le supprimer.<br>
        Veuillez ne pas répondre à ce message.<br>
        <br>
        Cordialement,<br>
        eBiblio
    </body>
</html>';

        $url     = $this->create_url('change_password', ['email' => $email, 'password' => $password]);
        $message = sprintf($message, $url);

        if (! $this->send_email($email, $subject, $message)) {
            throw new Exception("Impossible d'envoyer le nouveau mot de passe.");
        }
    }

    /**
     *
     * @param string $email
     * @param string $password
     */
    public function sign_in($email, $password)
    {
        if (strpos($email, '@') === false and ! $email = $this->get_user_email($email)) {
            throw new Exception("Plusieurs e-mails avec le même nom, entrer l'e-mail complet.");
        }

        if (! $this->is_registered_user($email, $password)) {
            throw new Exception('Adresse e-mail actuelle inconnue ou mot de passe incorrect.');
        }

        $this->update_session($email);

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
     * @param string $tmp_book_filename
     * @return string
     */
    public function unzip_book($tmp_book_filename)
    {
        $zip = new ZipArchive;

        $tmp_book_dirname = sprintf('%s/unzip/%s', $this->config['data_path'], basename($tmp_book_filename));

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
     * @param string $book_id
     * @param string $author
     * @param string $title
     */
    public function update_bookinfo($book_id, $author, $title)
    {
        $booklist = $this->read_booklist();

        if (! isset($booklist[$book_id]['original_author'])) {
            $booklist[$book_id]['original_author'] = $booklist[$book_id]['author'];
        }

        if (! isset($booklist[$book_id]['original_title'])) {
            $booklist[$book_id]['original_title'] = $booklist[$book_id]['title'];
        }

        $booklist[$book_id]['author'] = $author ?: $booklist[$book_id]['original_author'];
        $booklist[$book_id]['title']  = $title  ?: $booklist[$book_id]['original_title'];

        $this->write_booklist($booklist);
    }

    /**
     *
     * @param array $bookinfo
     * @param string $cover_filename
     * @return string
     */
    public function update_booklist($bookinfo, $cover_filename)
    {
        $book_id  = md5($bookinfo['title'] . $bookinfo['author'] . $bookinfo['date']);
        $booklist = $this->read_booklist();

        if (isset($booklist[$book_id])) {
            $bookinfo['updated'] = $this->get_datetime();
            $previous_bookinfo   = $booklist[$book_id];
            $bookinfo            += $previous_bookinfo;
        } else {
            $bookinfo['created'] = $this->get_datetime();
            $bookinfo['deleted'] = null;
            $bookinfo['updated'] = null;
            $previous_bookinfo   = null;
        }

        $bookinfo['cover_ext'] = pathinfo($cover_filename, PATHINFO_EXTENSION);
        $bookinfo['deleted']   = null;
        $bookinfo['email']     = $_SESSION['email'];
        $bookinfo['name']      = $this->create_bookname($bookinfo);
        $bookinfo['source']    = empty($_FILES['filename']['name']) ? null : $_FILES['filename']['name'];

        ksort($bookinfo);

        $booklist[$book_id] = $bookinfo;

        $this->write_booklist($booklist);

        return [$book_id, $bookinfo, $previous_bookinfo];
    }

    /**
     *
     * @param string $email
     */
    public function update_session($email)
    {
        $_SESSION['email'] = $email;
    }

    /**
     *
     * @param string $old_email
     * @param string $new_email
     * @param string $new_book_notification
     * @param string $admin
     */
    public function update_user($old_email, $new_email, $new_book_notification, $admin)
    {
        $users = $this->read_users();

        $users[$old_email]['admin']   = $admin;
        $users[$old_email]['options'] = ['new_book_notification' => $new_book_notification];

        if ($old_email != $new_email) {
            $users = $this->replace_email($old_email, $new_email, $users);
        }

        $this->write_users($users);
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

        list($book_id, $bookinfo, $previous_bookinfo) = $this->update_booklist($bookinfo, $cover_filename);

        $this->move_book($tmp_book_filename, $bookinfo, $previous_bookinfo);
        $this->move_cover($cover_filename, $bookinfo, $previous_bookinfo);
        $this->delete_dir($tmp_book_dirname);

        if (! $previous_bookinfo) {
            $this->send_new_book_notifications($book_id, $bookinfo);
        }

        return [$book_id, $bookinfo];
    }

    /**
     *
     * @param string $email
     * @param array $users
     * @throws Exception
     */
    public function validate_email($email, $users)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Adresse e-mail incorrecte.');
        }

        if (isset($users[$email])) {
            throw new Exception('Adresse e-mail déjà utilisée.');
        }
    }

    /**
     *
     * @param string $action
     */
    public function verify_admin_user_action($action)
    {
        if (in_array($action, ['add_user', 'disable_user', 'enable_user', 'get_users']) and ! $this->is_admin_user()) {
            $params = ['message' => $this->encode_data('Action seulement autorisée pour un administrateur.')];
            $this->redirect('sign_in', $params);
        }
    }

    /**
     *
     * @param string $action
     * @return bool
     */
    public function verify_user_signed_in($action)
    {
        if (isset($_SESSION['email'])) {
            return true;
        }

        if (in_array($action, ['change_password', 'send_password', 'sign_in', 'sign_out'])) {
            return false;
        }

        if ($action == 'download_book') {
            // must redirect to the booklist instead of the sign-in page otherwise it gets back to the sign_in page in a loop (!)
            $this->redirect('get_booklist');
        }

        if ($encoded = $this->encode_uri()) {
            $params = ['redirect' => $encoded];
            $this->redirect('sign_in', $params);
        }

        $this->redirect('sign_in');
    }

    /**
     *
     * @param string $users
     * @throws Exception
     */
    public function write_users($users)
    {
        $this->users = $users;

        $this->write_file($this->users_filename, $users);
    }

    /**
     *
     * @param array $booklist
     * @throws Exception
     */
    public function write_booklist($booklist)
    {
        $this->booklist = $booklist;

        $this->write_file($this->booklist_filename, $booklist);
    }

    /**
     *
     * @param string $filename
     * @param array $data
     * @throws Exception
     */
    public function write_file($filename, $data)
    {
        $basename = basename($filename);

        if (! $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) {
            throw new Exception("Impossible d'encoder les données de $basename");
        }

        if (! file_put_contents($filename, $json)) {
            throw new Exception("Impossible d'enregistrer $basename.");
        }
    }
}
