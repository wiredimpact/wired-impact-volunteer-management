/**
 * Webpack configuration
 * 
 * Run "npm install" to install all needed dependencies.
 * Run "npm run build" to build the needed files and watch for changes.
 * 
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/javascript/js-build-setup/
 */

const path                     = require('path');
const MiniCssExtractPlugin     = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

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
	target: [ 'web', 'es5' ],
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@wordpress/babel-preset-default'],
						cacheDirectory: true,
					}
				}
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
		new RemoveEmptyScriptsPlugin(), // Removes extra JS files created when .scss files are used as input
	]
};