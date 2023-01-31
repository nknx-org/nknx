const mix = require("laravel-mix");
require("laravel-mix-svg-vue");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js")
    .vue()
    .sass("resources/scss/main.scss", "public/css")
    .postCss("resources/css/app.css", "public/css", [
        require("postcss-import"),
        require("tailwindcss")
    ])
    .postCss("resources/css/reset.css", "public/css")
    .postCss("resources/css/grid.css", "public/css")
    .postCss("resources/css/feather.css", "public/css")
    .copyDirectory("resources/assets", "public/assets")
    .copyDirectory("resources/themes", "public/themes")
    .copyDirectory("resources/root", "public")
    .svgVue({
        svgPath: "resources/assets/icons",
        extract: false,
        svgoSettings: [
            { removeTitle: false },
            { removeViewBox: false },
            { removeDimensions: false }
        ]
    })
    .webpackConfig(require("./webpack.config"));

if (mix.inProduction()) {
    mix.version();
}
