<?php
/**
 * Loads the Gravity Forms Volunteer Management Feed Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 2.1
 */
class WI_Volunteer_Management_Gravity_Forms_Feed_AddOn_Bootstrap {
 
	/**
	 * Loads the required add-on file and registers the add-on.
	 *
	 * This method is called statically using the "gform_loaded"
	 * action.
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {

			return;
		}

		GFForms::include_feed_addon_framework();

		require_once 'class-gravity-forms-feed-addon.php';

		GFAddOn::register( 'WI_Volunteer_Management_Gravity_Forms_Feed_AddOn' );
	}
}