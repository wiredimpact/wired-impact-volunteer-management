/**
 * Babel configuration
 */

module.exports = function (api) {
		api.cache(true);
		
		/**
		 * The "@wordpress/default" preset includes the needed plugins and configuration for WordPress blocks
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-babel-preset-default/
		 */
		const presets = ["@wordpress/default"];
		const plugins = [];
		
		return {
			presets,
			plugins
		};
}