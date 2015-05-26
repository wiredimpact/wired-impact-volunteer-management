<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 * @author     Your Name <email@example.com>
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
	 * @since    1.0.0
	 */
	public static function activate() {
		WI_Volunteer_Management_Public::register_post_types();
		flush_rewrite_rules();

		//Add our volunteer role
		add_role(
		    'volunteer',
		    __( 'Volunteer', 'wivm' ),
		    array(
		        'read'         			=> true,
		        'serve_as_volunteer' 	=> true //Custom capability
		    )
		);
	}

}
