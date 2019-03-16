/**
 * Webpack configuration
 * 
 * Run "npm install" to install all needed dependencies.
 * Run "npm run build" to build the needed files and watch for changes.
 * 
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/javascript/js-build-setup/
 */

const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries"); 

module.exports = {
  	entry: {
		wi_volunteer_management_block: './admin/js/wi-volunteer-management-block.js',
	},
	output: {
    	filename: '[name].bundle.js'.replace( '_', '-' ),
    	path: path.resolve( __dirname, 'admin/js' )
	},
	mode: 'production',
	watch: true, // Causes webpack to keep watching for changes to bundle
	optimization: {
		minimize: true
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: "babel-loader",
			},
			{
				test: /\.scss$/,
				use: [
					MiniCssExtractPlugin.loader,
					"css-loader",
					"sass-loader"
				]
			}
		],
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: '../css/[name].bundle.css', // The name of the CSS file to output instead of putting CSS in the JS files
		}),
		new OptimizeCssAssetsPlugin({
			cssProcessorOptions: { // options passed to cssnano
				autoprefixer: {
					discardComments: { removeAll: true },
					safe: true,
					autoprefixer: {
						add: true,
						browsers: [ 'last 10 versions' ]     
					} 
				}
			}
		}), // Optimize and minify CSS
		new FixStyleOnlyEntriesPlugin(), // Removes extra JS files created when .scss files are used as input
	]
};