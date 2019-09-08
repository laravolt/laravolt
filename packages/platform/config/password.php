<?php

/*
 * Set specific configuration variables here
 */
return [
    // email view
    'emails' => [
        'reset' => 'laravolt::emails.password.reset',
        'new' => 'laravolt::emails.password.new',
    ],

    // ask user to change their password periodically
    // leave null to skip checking or fill with integer (in days)
    'duration' => null,

    // where to redirect user to change their current password, if CheckPassword middleware applied
    'redirect' => 'epicentrum/my/password',

    // don't apply CheckPassword middleware to following url pattern
    'except' => [
        'auth/logout',
        '_debugbar/*',
    ],
];
