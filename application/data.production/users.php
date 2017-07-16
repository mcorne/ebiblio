<?php
// the admin password is set by default to 123456
// it MUST be changed after installing the application on a production machine
return array(
    'admin' => [
        'admin' => true,
        'end_date' => null,
        'options' => [
            'new_book_notification' => true,
        ],
        'password' => '$2y$10$F.A1uRs7AY313k13FCgWPu2/SqX55iUT2hw4RWw9WahPhl09tBKHe',
        'password_end_date' => null,
        'start_date' => '2017-01-01 00:00:00',
    ],
);
