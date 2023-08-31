let mix = require('laravel-mix');


mix.webpackConfig({
   module: {
     rules: [
       {
         test: /\.vue$/,
         loader: 'vue-loader',
         options: {
           loaders: {
             // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
             // the "scss" and "sass" values for the lang attribute to the right configs here.
             // other preprocessors should work out of the box, no loader config like this necessary.
             'scss': [
               'vue-style-loader',
               'css-loader',
               'sass-loader'
             ],
             'sass': [
               'vue-style-loader',
               'css-loader',
               'sass-loader?indentedSyntax'
             ],
           }
           // other vue-loader options go here
         }
       },
       {
         test: /\.js$/,
         loader: 'babel-loader',
         exclude: /node_modules/
       },
       {
         test: /\.(png|jpg|gif|svg)$/,
         loader: 'file-loader',
         options: {
           name: '[name].[ext]?[hash]'
         }
       },
         {
            test: /\.css$/, // Update this to match your CSS file extensions
            use: [
              'vue-style-loader',
              'css-loader'
            ]
         },
     ],
   },
   resolve: {
     alias: {
       'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
     },
     extensions: ['*', '.js', '.vue', '.json']
   },
}); 

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
mix.options({
	processCssUrls: false,
});

//Public
// mix.js('resources/assets/js/app.js', 'public/js');	//Archivos Vue
// mix.sass('resources/assets/sass/app.scss', 'public/css');	//Codigo SASS
// mix.less('resources/assets/less/main.less', 'public/css');	// Codigo LESS

// //Dashboard (Panel de administracion)
mix.js('resources/assets/js/admin.js', 'public/js')	//Archivos Vue
   .version();
// mix.sass('resources/assets/sass/admin.scss', 'public/css');	//Archivos SASS
