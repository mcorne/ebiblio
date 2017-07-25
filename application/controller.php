<?php
require_once 'toolbox.php';

class controller
{
    /**
     *
     * @var toolbox
     */
    public $toolbox;

    /**
     *
     * @param string $base_path
     * @param string $base_url
     * @param string $environment
     */
    public function __construct($base_path, $base_url, $environment)
    {
        $this->toolbox = new toolbox($base_path, $base_url, $environment);
    }

    /**
     *
     * @return array
     */
    public function action_add_user()
    {
        try {
            if ($this->toolbox->is_post()) {
                $email = $this->toolbox->get_input('email');
                $admin = $this->toolbox->get_input('admin');
                $new_book_notification = $this->toolbox->get_input('new_book_notification');

                $this->toolbox->add_user($email, $new_book_notification, $admin);
                $this->toolbox->redirect('get_users');
            }

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'admin'                 => $admin                 ?? false,
            'email'                 => $email                 ?? null,
            'message'               => $message               ?? null,
            'new_book_notification' => $new_book_notification ?? true,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_change_email()
    {
        try {
            if ($this->toolbox->is_post()) {
                $new_email = $this->toolbox->get_input('new_email');
                $old_email = $this->toolbox->get_input('old_email');
                $password  = $this->toolbox->get_input('password');

                $this->toolbox->change_email($old_email, $password, $new_email);
                $this->toolbox->redirect();
            }

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'message'   => $message   ?? null,
            'new_email' => $new_email ?? null,
            'old_email' => $old_email ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_change_options()
    {
        try {
            if ($this->toolbox->is_post()) {
                $new_book_notification = $this->toolbox->get_input('new_book_notification');
                $this->toolbox->change_options($new_book_notification);
                $this->toolbox->redirect('change_options');
            }

            $options = $this->toolbox->get_options();

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'message' => $message ?? null,
            'options' => $options ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_change_password()
    {
        try {
            $email = $this->toolbox->get_input('email');

            // captures the password passed as url param if any, that is the new password sent by email
            $password = $this->toolbox->get_input('password');

            if ($this->toolbox->is_post()) {
                $old_password  = $this->toolbox->get_input('old_password');
                $new_password  = $this->toolbox->get_input('new_password');

                $this->toolbox->change_password($email, $old_password, $new_password);
                $this->toolbox->redirect();
            }


        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'email'    => $email    ?? null,
            'message'  => $message  ?? null,
            'password' => $password ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_delete_book()
    {
        try {
            if ($this->toolbox->is_post()) {
                if ($book_id = $this->toolbox->get_input('id')) {
                    $this->toolbox->delete_book($book_id);
                }

                $this->toolbox->redirect_to_booklist('delete', $book_id);
            }

            $booklist = $this->toolbox->get_booklist(false);

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'booklist' => $booklist ?? null,
            'message'  => $message  ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_delete_user()
    {
        try {
            if ($this->toolbox->is_post()) {
                $email    = $this->toolbox->get_input('email');
                $password = $this->toolbox->get_input('password');

                $this->toolbox->delete_user($email, $password);
                $this->toolbox->redirect();
            }

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'email'   => $email   ?? null,
            'message' => $message ?? null,
        ];
    }

    public function action_display_cover()
    {
        if ($book_id = $this->toolbox->get_input('id') and $bookinfo = $this->toolbox->get_bookinfo($book_id)) {
            $filename  = $this->toolbox->create_cover_filename($bookinfo['name'], $bookinfo['cover_ext']);
            $mime_info = mime_content_type($filename);

            header("Content-type: $mime_info");
            header('Content-length: '. filesize($filename));
            readfile($filename);
        }
    }

    public function action_download_book()
    {
        if ($book_id = $this->toolbox->get_input('id') and $bookinfo = $this->toolbox->get_bookinfo($book_id)) {
            $filename  = $this->toolbox->create_book_filename($bookinfo['name']);

            header('Content-Description: File Transfer');
            header('Content-Type: application/epub+zip');
            header('Content-Disposition: attachment; filename="' . $bookinfo['name'] .'"');
            header('Cache-Control: must-revalidate');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
        }
    }

    /**
     *
     * @return array
     */
    public function action_get_bookinfo()
    {
        try {
            if (! $book_id = $this->toolbox->get_input('id') or ! $bookinfo = $this->toolbox->get_bookinfo($book_id)) {
                $this->toolbox->redirect_to_booklist();
            }

            $language = $this->toolbox->get_language($bookinfo['language']);

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'book_id'  => $book_id  ?? null,
            'bookinfo' => $bookinfo ?? null,
            'language' => $language ?? null,
            'message'  => $message  ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_get_booklist()
    {
        try {
            if (! $sorting = $this->toolbox->get_input('sorting') or ! in_array($sorting, ['author', 'title'])) {
                $sorting = 'title';
            }

            $action           = $this->toolbox->get_input('action');
            $encoded_bookinfo = $this->toolbox->get_input('info');
            $selected_book_id = $this->toolbox->get_input('id');

            $booklist = $this->toolbox->get_booklist(false, $sorting, $action, $selected_book_id, $encoded_bookinfo);

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'booklist'         => $booklist ?? null,
            'message'          => $message  ?? null,
            'selected_book_id' => $selected_book_id ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_get_users()
    {
        try {
            $action         = $this->toolbox->get_input('action');
            $encoded_user   = $this->toolbox->get_input('info');
            $selected_email = $this->toolbox->get_input('email');

            $users = $this->toolbox->get_users($selected_email, $encoded_user);

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'message'        => $message ?? null,
            'selected_email' => $selected_email ?? null,
            'users'          => $users   ?? null,
        ];
    }

    public function action_send_password()
    {
        try {
            if ($this->toolbox->is_post()) {
                if ($email = $this->toolbox->get_input('email')) {
                    $this->toolbox->send_password($email);
                }

                $this->toolbox->redirect();
            }

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return ['message' => $message ?? null];
    }

    public function action_sign_in()
    {
        try {
            if ($this->toolbox->is_post()) {
                $email    = $this->toolbox->get_input('email');
                $password = $this->toolbox->get_input('password');

                if ($email and $password) {
                    $this->toolbox->sign_in($email, $password);
                }
            }

        } catch (Exception $exception) {
        }
    }

    public function action_sign_out()
    {
        try {
            $this->toolbox->reset_session();

        } catch (Exception $exception) {
        }

        $this->toolbox->redirect('sign_in');
    }

    /**
     *
     * @return array
     */
    public function action_undelete_book()
    {
        try {
            if ($this->toolbox->is_post()) {
                if ($book_id = $this->toolbox->get_input('id')) {
                    $this->toolbox->undelete_book($book_id);
                }

                $this->toolbox->redirect_to_booklist('undelete', $book_id);
            }

            $booklist = $this->toolbox->get_booklist(true);

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'booklist' => $booklist ?? null,
            'message'  => $message  ?? null,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_update_user()
    {
        try {
            $email = $this->toolbox->get_input('email');

            if ($this->toolbox->is_post()) {
                $admin                 = $this->toolbox->get_input('admin');
                $new_book_notification = $this->toolbox->get_input('new_book_notification');
                $new_email             = $this->toolbox->get_input('new_email');

                $this->toolbox->update_user($email, $new_email, $new_book_notification, $admin);
                $this->toolbox->redirect('get_users');
            } else {
                if (! $user = $this->toolbox->get_user($email)) {
                    // the email is invalid, silently ignores the email, redirects to the user list
                    $this->toolbox->redirect('get_users');
                }

                $admin                 = $user['admin'];
                $new_book_notification = $user['options']['new_book_notification'];
                $new_email             = $email;
            }

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return [
            'admin'                 => $admin,
            'email'                 => $email,
            'message'               => $message ?? null,
            'new_book_notification' => $new_book_notification,
            'new_email'             => $new_email,
        ];
    }

    /**
     *
     * @return array
     */
    public function action_upload_book()
    {
        try {
            if ($this->toolbox->is_post()) {
                if (! empty($_FILES['filename']['name'])) {
                    list($book_id, $bookinfo) = $this->toolbox->upload_book();
                    $this->toolbox->redirect_to_booklist('put', $book_id, $bookinfo);
                } else {
                    $this->toolbox->redirect_to_booklist();
                }
            }

        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return ['message' => $message ?? null];
    }

    /**
     *
     * @return string
     */
    public function get_action()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/ebiblio', '', $path);

        if (! $action = trim($path, '/')) {
            $action = 'get_booklist';
        }

        $method = "action_$action";

        if (! method_exists($this, $method)) {
            throw new Exception("Invalid action: $action");
        }

        return [$action, $method];
    }

    public function run_application()
    {
        session_start(['cookie_lifetime' => toolbox::SESSION_LIFE_TIME]);

        list($action, $method) = $this->get_action();

        $this->toolbox->verify_user_signed_in($action);

        if ($result = $this->$method()) {
            extract($result);
        }

        if ($is_html = ! in_array($action, ['display_cover', 'download_book'])) {
            require $this->toolbox->base_path . '/views/header.php';
        }

        if (! empty($result['message'])) {
            require $this->toolbox->base_path . '/views/message.php';
        }

        require $this->toolbox->create_action_filename($action);

        if ($is_html) {
            require $this->toolbox->base_path . '/views/footer.php';
        }
    }
}
