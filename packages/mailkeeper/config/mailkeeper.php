<?php

return [
    // Enable/disable mailkeeper.
    // If enable, every outgoing mail will be intercepted.
    // So, instead of send it via SMTP or other mail driver, mailkeeper will store it to database for further use.
    'enabled' => env('MAILKEEPER_ENABLED', false),

    // How many rows to take for each "laravolt:send-mail" command
    'take'    => 100,

    // Whether to auto load migrations or not.
    // If set to false, then you must publish the migration files first before running the migrate command
    'migrations' => true,
];
