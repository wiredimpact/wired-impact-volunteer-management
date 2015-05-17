<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           WI_Volunteer_Management
 *
 * @wordpress-plugin
 * Plugin Name:       Wired Impact Volunteer Management
 * Plugin URI:        http://example.com/wi-volunteer-management-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Wired Impact
 * Author URI:        http://wiredimpact.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wi-volunteer-management
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wi-volunteer-management-activator.php
 */
function activate_wi_volunteer_management() {
	require_once WIVM_DIR . 'includes/class-activator.php';
	WI_Volunteer_Management_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wi-volunteer-management-deactivator.php
 */
function deactivate_wi_volunteer_management() {
	require_once WIVM_DIR . 'includes/class-deactivator.php';
	WI_Volunteer_Management_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wi_volunteer_management' );
register_deactivation_hook( __FILE__, 'deactivate_wi_volunteer_management' );

/**
 *  Add constant to allow us to easily load files.
 */
define( 'WIVM_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WIVM_DIR . 'includes/class-wi-volunteer-management.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wivm() {

	$wivm = new WI_Volunteer_Management();
	$wivm->run();

}
run_wivm();
