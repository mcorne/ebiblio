<?php
// The admin password is set by default to 123456.
// It MUST be changed after installing the application on a production machine via the user interface.
// The "admin" key SHOULD IDEALLY BE changed to a valid email address via the user interface.
return array(
    'admin' => [
        'admin' => true,
        'end_date' => null,
        'options' => [
            'new_book_notification' => true,
        ],
        'password' => '$2y$10$Bl99f5ZyMdmafhgIf/ovye56q/Clj46LZ2cjpgvn4.c4veQs3Pth.',
        'password_end_date' => null,
        'start_date' => '2017-07-27 16:00:00',
    ],
);
