const mix = require('laravel-mix');
require('mix-env-file');

mix.env('./.env');

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

mix.js('resources/js/app.js', 'public/js')
    .postCss(
        'resources/css/app.css',
        'public/css',
        [
            require('postcss-import'),
            require('tailwindcss'),
            require('autoprefixer'),
        ]
    );

mix.browserSync({
    proxy: process.env.APP_URL,

    /**
     * Simple tweak to make Browsersync work smoothly with Turbolinks
     * See https://github.com/turbolinks/turbolinks/issues/147
     */
    snippetOptions: {
        rule: {
            match: /<\/head>/i,
            fn: function (snippet, match) {
                return snippet + match;
            }
        }
    }
});

if (mix.inProduction()) {
    mix.version();
}
