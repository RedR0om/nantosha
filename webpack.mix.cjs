const mix = require('laravel-mix');
const path = require('path');

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
    .vue({ version: 3 })
    .postCss('resources/css/app.css', 'public/css', [
        require('@tailwindcss/postcss'),
        require('autoprefixer'),
    ])
    .options({
        processCssUrls: false
    })
    .webpackConfig({
        resolve: {
            extensions: ['.*', '.wasm', '.mjs', '.js', '.jsx', '.ts', '.tsx', '.json', '.vue'],
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),
            },
            fullySpecified: false, // Allow imports without file extensions
        },
        module: {
            rules: [
                {
                    test: /\.tsx?$/,
                    loader: 'ts-loader',
                    exclude: /node_modules/,
                    options: {
                        appendTsSuffixTo: [/\.vue$/],
                        transpileOnly: true, // Skip type checking during build
                    },
                },
            ],
        },
    });

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}

