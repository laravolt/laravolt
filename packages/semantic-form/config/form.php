<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Form Builder
    |--------------------------------------------------------------------------
    |
    | This option controls the default form builder that will be used
    | throughout your application. You can switch between different
    | form builders based on your UI framework preference.
    |
    | Supported: "semantic", "preline"
    |
    */
    'default' => env('FORM_BUILDER', 'semantic'),

    /*
    |--------------------------------------------------------------------------
    | Form Builders Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the form builders that are available for your
    | application. Each form builder has its own configuration options
    | and UI framework styling.
    |
    */
    'builders' => [
        'semantic' => [
            'driver' => 'semantic',
            'class' => \Laravolt\SemanticForm\SemanticForm::class,
            'facade' => \Laravolt\SemanticForm\Facade::class,
            'service_provider' => \Laravolt\SemanticForm\ServiceProvider::class,
            'ui_framework' => 'semantic-ui',
            'css_framework' => 'semantic-ui',
            'description' => 'Semantic UI form builder with jQuery components',
        ],

        'preline' => [
            'driver' => 'preline',
            'class' => \Laravolt\PrelineForm\PrelineForm::class,
            'facade' => \Laravolt\PrelineForm\Facade::class,
            'service_provider' => \Laravolt\PrelineForm\ServiceProvider::class,
            'ui_framework' => 'preline-ui',
            'css_framework' => 'tailwindcss',
            'description' => 'Preline UI form builder with Tailwind CSS styling',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Detection Settings
    |--------------------------------------------------------------------------
    |
    | When enabled, the system will try to auto-detect the best form builder
    | based on the detected CSS framework in your application.
    |
    */
    'auto_detect' => [
        'enabled' => env('FORM_AUTO_DETECT', false),
        'detection_method' => 'css_scan', // 'css_scan', 'config', 'manual'
    ],

    /*
    |--------------------------------------------------------------------------
    | Runtime Switching
    |--------------------------------------------------------------------------
    |
    | Allow switching form builders at runtime within the same request.
    | This is useful for applications that need different form styles
    | in different sections.
    |
    */
    'runtime_switching' => [
        'enabled' => env('FORM_RUNTIME_SWITCHING', true),
        'cache_builder_instances' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Compatibility Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, provides backward compatibility for applications
    | that were using the old form builder methods.
    |
    */
    'compatibility_mode' => [
        'enabled' => true,
        'legacy_facade_support' => true,
        'legacy_helper_support' => true,
    ],
];