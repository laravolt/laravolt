<?php

return [

    // Configuration for the default group. Feel free to add more groups.
    // Each group can have different settings.
    'default' => [

        /**
         * Regex to match against a filename/url to determine if it is an asset.
         *
         * @var string
         */
        'asset_regex' => '/.\.(css|js)$/i',

        /**
         * Absolute path to the public directory of your App (WEBROOT).
         * Required if you enable the pipeline.
         * No trailing slash!.
         *
         * @var string
         */
        'public_dir' => public_path(),

        /**
         * Directory for local CSS assets.
         * Relative to your public directory ('public_dir').
         * No trailing slash!.
         *
         * @var string
         */
        'css_dir' => 'css',

        /**
         * Directory for local JavaScript assets.
         * Relative to your public directory ('public_dir').
         * No trailing slash!.
         *
         * @var string
         */
        'js_dir' => 'js',

        /**
         * Available collections.
         * Each collection is an array of assets.
         * Collections may also contain other collections.
         *
         * @var array
         */
        'collections' => [

            // jQuery (CDN)
            // 'jquery-cdn' => array('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'),

            // jQuery UI (CDN)
            // 'jquery-ui-cdn' => array(
            //     'jquery-cdn',
            //     '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js',
            // ),

            // Twitter Bootstrap (CDN)
            // 'bootstrap-cdn' => array(
            //     'jquery-cdn',
            //     '//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css',
            //     '//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css',
            //     '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'
            // ),

            // Redactor
            'redactor' => [
                '/laravolt/css/redactor.min.css',
                '/laravolt/js/redactor.min.js',
            ],
        ],

        /**
         * Preload assets.
         * Here you may set which assets (CSS files, JavaScript files or collections)
         * should be loaded by default even if you don't explicitly add them on run time.
         *
         * @var array
         */
        'autoload' => [
            // 'jquery-cdn',
        ],

    ], // End of default group
];
