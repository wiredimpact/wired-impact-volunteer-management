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

		//Create our volunteer opportunity table if it doesn't already exist.
		WI_Volunteer_Management_Activator::create_rsvp_db_table();
	}

	/*
     * Create the database table that will hold our volunteer opportunity RSVP information.
     * 
     * We create a database table that will hold our volunteer opportunity RSVP information.
     * We check first to make sure the table doesn't exist by seeing if the
     * version exists in the options table.
     */
    public static function create_rsvp_db_table(){
		//Only create table if it doesn't exist.
		if( get_option( 'volunteer_opp_rsvp_db_version' ) == false ){
			global $wpdb;

			$table_name =  $wpdb->prefix . 'volunteer_rsvps';

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				post_id bigint(20) NOT NULL,
				rsvp tinyint(2) NOT NULL,
				time datetime NOT NULL,
				PRIMARY  KEY  (id),
				UNIQUE KEY (user_id, post_id)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			//We set a variable in options in case we need to update the database in the future.
			add_option('volunteer_opp_rsvp_db_version', 1.0);
		}
	}

}
