<?php

/**
 * Bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wiredimpact.com
 * @since             0.1
 * @package           WI_Volunteer_Management
 * @author     		  Wired Impact <info@wiredimpact.com>
 *
 * @wordpress-plugin
 * Plugin Name:       Wired Impact Volunteer Management
 * Plugin URI:        https://wiredimpact.com/wordpress-plugins-for-nonprofits/volunteer-management/
 * Description:       A simple, free way to keep track of your nonprofit’s volunteers and opportunities.
 * Version:           1.3.8
 * Author:            Wired Impact
 * Author URI:        https://wiredimpact.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wired-impact-volunteer-management
 * Domain Path: 	  /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 *
 * @param $network_wide Whether the plugin is being network enabled on multisite
 */
function activate_wi_volunteer_management( $network_wide = false ) {
	require_once WIVM_DIR . 'includes/class-activator.php';
	WI_Volunteer_Management_Activator::activate( $network_wide );
}

register_activation_hook( __FILE__, 'activate_wi_volunteer_management' );

/**
 * Run the activation when a new multisite blog is created.
 *
 * This function is different than the general activation since it
 * only runs on the given subsite and only if the plugin is 
 * already activated network-wide. In this case we need to run 
 * the activation method since the plugin will never be enabled
 * through the admin.
 *
 * @since  1.3.3
 */
function multisite_activate_wi_volunteer_management( $blog_id ){
	if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
		switch_to_blog( $blog_id );
		require_once WIVM_DIR . 'includes/class-activator.php';
		WI_Volunteer_Management_Activator::activate_site();
		restore_current_blog();
	}
}

add_action( 'wpmu_new_blog', 'multisite_activate_wi_volunteer_management' );

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
 * @since    0.1
 */
function run_wivm() {

	$wivm = new WI_Volunteer_Management();
	$wivm->run();

}
run_wivm();