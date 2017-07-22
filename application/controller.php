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

    public function action_change_email()
    {
        try {
            if ($this->is_post()) {
                $old_email = $this->get_input('old_email');
                $password  = $this->get_input('password');
                $new_email = $this->get_input('new_email');

                $this->change_email($old_email, $password, $new_email);
                $this->redirect();
            }

        } catch (Exception $exception) {
            $this->display_exception($exception);
        }
    }

    public function action_change_options()
    {
        try {
            if ($this->is_post()) {
                $new_book_notification = $this->get_input('new_book_notification');
                $this->change_options($new_book_notification);
                $this->redirect('change_options');
            }

            $options = $this->get_options();

        } catch (Exception $exception) {
            $this->display_exception($exception);
        }
    }

    public function action_change_password()
    {
        try {
            // captures the password passed as url param if any, that is the new password sent by email
            $password = $this->get_input('password');

            if ($this->is_post()) {
                $email         = $this->get_input('email');
                $old_password  = $this->get_input('old_password');
                $new_password  = $this->get_input('new_password');

                $this->change_password($email, $old_password, $new_password);
                $this->redirect();
            }

            $email = $this->get_input('email');

        } catch (Exception $exception) {
            $this->display_exception($exception);
        }
    }

    public function action_delete_book()
    {
        try {
            if ($this->is_post()) {
                if ($book_id = $this->get_input('id')) {
                    $this->delete_book($book_id);
                }

                $this->redirect_to_booklist('delete', $book_id);
            }

            $booklist = $this->get_booklist(false);

        } catch (Exception $exception) {
            $this->display_exception($exception);
        }
    }

    public function action_delete_user()
    {
        try {
            if ($this->is_post()) {
                $email    = $this->get_input('email');
                $password = $this->get_input('password');

                $this->delete_user($email, $password);
                $this->redirect();
            }

        } catch (Exception $exception) {
            $this->display_exception($exception);
        }
    }
}
