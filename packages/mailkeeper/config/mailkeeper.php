<?php

return [
    // Enable/disable mailkeeper.
    // If enable, every outgoing mail will be intercepted.
    // So, instead of send it via SMTP or other mail driver, mailkeeper will store it to database for further use.
    'enabled' => env('MAILKEEPER_ENABLED', false),

    // How many rows to take for each "laravolt:send-mail" command
    'take'    => 100,
];
