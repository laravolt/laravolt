let mix = require('laravel-mix');

mix.setPublicPath('public');

mix.scripts([
    './node_modules/jquery/dist/jquery.min.js',
    './public/semantic/semantic.min.js',
    './node_modules/simplebar/dist/simplebar.js',
    './node_modules/autonumeric/dist/autoNumeric.js',
    './node_modules/unpoly/unpoly.js',
    'resources/js/components/basictable.js',
    'resources/js/components/keymaster.js',
    'resources/js/components/fileuploader.js',
    'resources/js/components/fuse.min.js',
    'resources/js/components/modal.js',
], 'public/js/vendor.js');

mix.scripts([
    'resources/js/init/sidebar.js',
    'resources/js/init/ui.js',
    'resources/js/init/events.js',
    // Somehow, quick-switcher.js must be initialized last, after all other UI elements. Don't know why :(
    'resources/js/init/quick-switcher.js'
], 'public/js/platform.js');

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
    './public/js/vendor.js',
    './public/js/platform.js'
]);
