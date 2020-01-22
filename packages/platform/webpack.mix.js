let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.setPublicPath('public')

mix.scripts([
    './node_modules/jquery/dist/jquery.min.js',
    './public/semantic/semantic.min.js',
    './node_modules/simplebar/dist/simplebar.js',
    'resources/js/components/basictable.js',
    'resources/js/components/keymaster.js',
    'resources/js/components/fileuploader.js',
    'resources/js/components/fuse.min.js',
    'resources/js/init/sidebar.js',
    'resources/js/init/ui.js',
    // Somehow, quick-switcher.js must be initialized last, after all other UI elements. Don't know why :(
    'resources/js/init/quick-switcher.js'
], 'public/js/all.js');

mix.sass('resources/sass/app.scss', 'public/css')
    .options({
        processCssUrls: false
    });

mix.styles([
    './node_modules/simplebar/dist/simplebar.css',
    './public/css/app.css',
], 'public/css/all.css');

mix.copyDirectory('resources/img', 'public/img');

mix.version([
    './public/semantic/semantic.min.css',
    './public/css/all.css',
    './public/js/all.js'
]);
