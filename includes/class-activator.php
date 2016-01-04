<?php

/**
 * Fired during plugin activation
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Activator {

	/**
	 * On activation flush rewrite rules.
	 *
	 * On activation we first declare our custom post type and then
	 * flush rewrite rules so our custom url structure works how we'd want it to.
	 * Along with that we create our volunteer role to be used to easily track volunteers
	 * who sign up for volunteer opportunities on the website.
	 *
	 * @since    0.1
	 */
	public static function activate() {
		WI_Volunteer_Management_Public::register_post_types();
		flush_rewrite_rules();

		//Add our volunteer role
		add_role(
		    'volunteer',
		    __( 'Volunteer', 'wired-impact-volunteer-management' ),
		    array(
		        'read'         			=> true,
		        'serve_as_volunteer' 	=> true //Custom capability
		    )
		);

		//Add our default options if they don't already exist.
		$options = new WI_Volunteer_Management_Options();
		$options->set_defaults();
	}
}
