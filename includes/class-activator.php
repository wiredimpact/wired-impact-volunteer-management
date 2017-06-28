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
	 * Run the necessary activation process for all sites or the current one.
	 *
	 * If we're working with a multisite installation and the plugin is being
	 * turned on network-wide then run the activation on all sites in the network.
	 * If not, run the activation only on the current site.
	 *
	 * @since    0.1
	 * @param 	 $network_wide Whether the plugin is being network enabled on multisite
	 */
	public static function activate( $network_wide = false ) {
		// If multisite and network activated run our activation on all sites
		if ( is_multisite() && $network_wide ) { 
			global $wpdb;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				WI_Volunteer_Management_Activator::activate_site();
				restore_current_blog();
			} 
	    }
	    else {
	        WI_Volunteer_Management_Activator::activate_site();
	    }
	}

	/**
	 * On activation flush rewrite rules, add the volunteer role and set defaults.
	 *
	 * On activation we first declare our custom post type and then flush rewrite rules
	 * so our custom url structure works how we'd want it to. Along with that we create
	 * our volunteer role to be used to easily track volunteers who sign up for volunteer
	 * opportunities on the website. Finally, we add our default options to the database.
	 *
	 * @since    1.3.3
	 */
	public static function activate_site(){
		WI_Volunteer_Management_Public::register_post_types();
		flush_rewrite_rules();

		//Add our volunteer role
		add_role(
		    'volunteer',
		    __( 'Volunteer', 'wired-impact-volunteer-management' ),
		    array(
		        'read'                  => true,
		        'serve_as_volunteer'    => true //Custom capability
		    )
		);

		//Add our default options if they don't already exist.
		$options = new WI_Volunteer_Management_Options();
		$options->set_defaults();
	}
}