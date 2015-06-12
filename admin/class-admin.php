<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wiredimpact.com
 * @since      1.0.0
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/admin
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'jquery-ui-smoothness', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css' );
		wp_enqueue_style( 'wivm-styles', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'jquery-ui-slider' );
    	wp_enqueue_script( 'jquery-ui-datepicker' );
    	wp_enqueue_script( 'jquery-timepicker', plugin_dir_url( __FILE__ ) . 'js/jquery-ui-timepicker.js', array( 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-datepicker' ) );
		wp_enqueue_script( 'wivm-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register our menu and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	public function register_settings_page() {

		// Base 64 encoded SVG image
		$icon_svg = 'dashicons-groups';

		// Add main page
		$admin_page = add_menu_page(
			__( 'Wired Impact Volunteer Management: ', 'wivm' ) . ' ' . __( 'Settings', 'wivm' ),
			__( 'Volunteer Mgmt', 'wivm' ),
			'manage_options',
			'wi-volunteer-management',
			array( $this, 'load_page' ),
			$icon_svg,
			'25.5'
		);

		// Sub menu pages
		$submenu_pages = array(
			array(
				'wi-volunteer-management',
				'',
				__( 'Volunteers', 'wivm' ),
				'manage_options',
				'wi-volunteer-management-volunteers',
				array( $this, 'load_page' ),
			),
			array(
				'wi-volunteer-management',
				'',
				__( 'Settings', 'wivm' ),
				'manage_options',
				'wi-volunteer-management-settings',
				array( $this, 'load_page' ),
			),
		);

		// Allow submenu pages manipulation
		$submenu_pages = apply_filters( 'wivm_submenu_pages', $submenu_pages );

		// Loop through submenu pages and add them
		if ( count( $submenu_pages ) ) {
			foreach ( $submenu_pages as $submenu_page ) {
				// Add submenu page
				add_submenu_page( $submenu_page[0], $submenu_page[2] . ' - ' . __( 'Wired Impact Volunteer Management', 'wivm' ), $submenu_page[2], $submenu_page[3], $submenu_page[4], $submenu_page[5] );
			}
		}

		//Change the submenu name for the 1st item
		global $submenu;
		if ( isset( $submenu['wi-volunteer-management'] ) && current_user_can( 'manage_options' ) ) {
			$submenu['wi-volunteer-management'][0][0] = __( 'Opportunities', 'wivm' );
		}
	}

	/**
	 * Load the appropriate admin page
	 */
	public function load_page() {
		$page = filter_input( INPUT_GET, 'page' );

		switch ( $page ) {
			case 'wi-volunteer-management-volunteers':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/volunteer.php';
				break;

			case 'wi-volunteer-management-settings':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/settings.php';
				break;
		}
	}

	/**
	 * Register our settings with the WordPress Settings API
	 *
	 * Our setting is registered here so it will be saved in the database within options.php. We then use
	 * settings.php to load our settings page and the form fields that we need. 
	 * 
	 * @see  https://kovshenin.com/2012/the-wordpress-settings-api/ Article on how to use the Settings API
	 */
	public function register_settings(){
		register_setting( 'wivm-settings-group', 'wivm-settings' );
	}

	/**
	 * Add meta boxes for volunteer opportunities.
	 */
	public function add_meta_boxes(){
		//Opportunity details such as location and time
		add_meta_box(
            'volunteer-opportunity-details',						// Unique ID
            __( 'Volunteer Opportunity Details', 'wivm' ),			// Box title
            array( $this, 'display_opportunity_details_meta_box' ),	// Content callback
            'volunteer_opp',                						// Post type
            'normal'												// Location
        );
	}

	/**
     * Display the custom meta fields and values when editing a volunteer opportunity.
	 * 
	 * @param object $post The post object for the volunteer opportunity.
	 */
	public function display_opportunity_details_meta_box( $post ){
		//Get all the meta data
		$volunteer_opp = new WI_Volunteer_Management_Opportunity( $post->ID );
		$nonce = wp_create_nonce( 'volunteer_opp_details_nonce' );
		?>
		<input type="hidden" id="_volunteer_opp_details_nonce" name="_volunteer_opp_details_nonce" value="<?php echo $nonce ?>" />
		<table class="volunteer-opp-details-meta">
		  <?php do_action( 'wivm_before_opportunity_detail_meta_fields', $post ); ?>

		  <tr>
		    <td colspan="2"><h3><?php _e( 'Contact Information', 'wivm' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><label for="contact_name"><?php _e( 'Name', 'wivm' ); ?></label></td>
		    <td><input type="text" id="contact_name" name="contact_name" tabindex="10" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['contact_name']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="contact_phone"><?php _e( 'Phone Number', 'wivm' ); ?></label></td>
		    <td><input type="text" id="contact_phone" name="contact_phone" tabindex="20" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['contact_formatted_phone']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="contact_email"><?php _e( 'Email', 'wivm' ); ?></label></td>
		    <td><input type="text" id="contact_email" name="contact_email" tabindex="30" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['contact_email']; ?>" /></td>
		  </tr>

		  
		  <tr>
		    <td colspan="2"><h3><?php _e( 'Location Information', 'wivm' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><label for="location"><?php _e( 'Location Name', 'wivm' ); ?></label></td>
		    <td><input type="text" id="location" name="location" tabindex="40" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['location']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="street"><?php _e( 'Street Address', 'wivm' ); ?></label></td>
		    <td><input type="text" id="street" name="street" tabindex="50" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['street']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="city"><?php _e( 'City', 'wivm' ); ?></label></td>
		    <td><input type="text" id="city" name="city" tabindex="60" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['city']; ?>" /></td>
		  </tr>

		  <tr>
		    <td><label for="state"><?php _e( 'State', 'wivm' ); ?></label></td>
		    <td><input type="text" id="state" name="state" tabindex="70" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['state']; ?>" /></td>
		  </tr>

		  <tr>
		    <td><label for="zip"><?php _e( 'Zip', 'wivm' ); ?></label></td>
		    <td><input type="text" id="zip" name="zip" tabindex="80" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['zip']; ?>" /></td>
		  </tr>

		  
		  <tr>
		    <td colspan="2"><h3><?php _e( 'Date and Time', 'wivm' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><?php _e( 'One-Time Opportunity?', 'wivm' ); ?></td>
		    <td>
		    		<input type="checkbox" id="one-time-opportunity" name="one-time-opportunity" tabindex="90" value="1" <?php checked( 1, $volunteer_opp->opp_meta['one_time_opp'] ); ?> />
		    		<label for="one-time-opportunity"><?php _e( 'This is a one-time opportunity at a fixed date and time.', 'wivm' ); ?></label>
		    </td>
		  </tr>

		  <?php $one_time_class = ( $volunteer_opp->opp_meta['one_time_opp'] == 1 ) ? 'one-time' : 'flexible'; ?>
		  <tr class="one-time-field <?php echo $one_time_class; ?>">
		    <td><label for="start-date-time"><?php _e( 'Start Date & Time', 'wivm' ); ?></label></td>
		    <td><input type="text" id="start-date-time" name="start-date-time" tabindex="100" class="regular-text" value="<?php if ( $volunteer_opp->opp_meta['start_date_time'] != '' ) echo $volunteer_opp->format_opp_times( $volunteer_opp->opp_meta['start_date_time'], '', true ); ?>" /></td>
		  </tr>
		  
		  <tr class="one-time-field <?php echo $one_time_class; ?>">
		    <td><label for="end-date-time"><?php _e( 'End Date & Time', 'wivm' ); ?></label></td>
		    <td>
		      <input type="text" id="end-date-time" name="end-date-time" tabindex="110" class="regular-text" value="<?php if( $volunteer_opp->opp_meta['end_date_time'] != '' ) echo $volunteer_opp->format_opp_times( $volunteer_opp->opp_meta['end_date_time'], '', true ); ?>" />
		      <span class="error" style="display: none;"><?php _e( 'Woops, it looks like you set your event to end before it started.', 'wivm' ); ?></span>
		    </td>
		  </tr>

		  <tr class="flexible-field <?php echo $one_time_class; ?>">
		    <td><label for="flexible_frequency"><?php _e( 'When Will This Event Happen?', 'wivm' ); ?></label></td>
		    <td><input type="text" id="flexible_frequency" name="flexible_frequency" tabindex="120" class="regular-text" placeholder="<?php _e( 'On your own time, All summer, etc.', 'wivm' ); ?>" value="<?php echo $volunteer_opp->opp_meta['flexible_frequency']; ?>" /></td>
		  </tr>

		  <tr>
		    <td colspan="2"><h3><?php _e( 'Volunteer Limit', 'wivm' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><?php _e( 'Is There a Volunteer Limit?', 'wivm' ); ?></td>
		    <td>
		    		<input type="checkbox" id="has-volunteer-limit" name="has-volunteer-limit" tabindex="130" value="1" <?php checked( 1, $volunteer_opp->opp_meta['has_volunteer_limit'] ); ?> />
		    		<label for="has-volunteer-limit"><?php _e( 'Only a fixed number of people can participate in this volunteer opportunity.', 'wivm' ); ?></label>
		    </td>
		  </tr>

		  <?php $volunteer_limit_class = ( $volunteer_opp->opp_meta['has_volunteer_limit'] == 1 ) ? 'has-volunteer-limit' : 'no-volunteer-limit'; ?>
		  <tr class="volunteer-limit-field <?php echo $volunteer_limit_class; ?>">
		    <td><label for="volunteer-limit"><?php _e( 'Max Number of Volunteers', 'wivm' ); ?></label></td>
		    <td><input type="text" id="volunteer-limit" name="volunteer-limit" tabindex="140" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['volunteer_limit']; ?>" /></td>
		  </tr>

		  <?php do_action( 'wivm_after_opportunity_detail_meta_fields', $volunteer_opp ); ?>
		</table>
		<?php
	}
	
	/**
	 * Save the meta fields for volunteer opportunities when saving from the edit screen.
	 *
	 * @param int $volunteer_opp_id ID of this post.
	 * @param object $volunteer_opp The $post object for the volunteer opportunity.
	 */
	public function save_volunteer_opp_meta( $volunteer_opp_id, $volunteer_opp ){

		//Check autosave, post type, user caps, nonce
		if( wp_is_post_autosave( $volunteer_opp_id ) || wp_is_post_revision( $volunteer_opp_id ) ) {
			return false;
		}
		if( $volunteer_opp->post_type != 'volunteer_opp' ){
			return false;
		}
		if( !current_user_can( 'edit_post', $volunteer_opp_id ) ){
			return false;
		}
		if( !isset( $_REQUEST['_volunteer_opp_details_nonce'] ) || !wp_verify_nonce( $_REQUEST['_volunteer_opp_details_nonce'], 'volunteer_opp_details_nonce' ) ){
			return false;
		}

		//Save all of our fields
		//Contact Name
		if( isset($_REQUEST['contact_name'] ) ) {
			update_post_meta( $volunteer_opp_id, '_contact_name', sanitize_text_field( $_REQUEST['contact_name'] ) );
		}

		//Phone
		if( isset($_REQUEST['contact_phone'] ) ) {
			update_post_meta( $volunteer_opp_id, '_contact_phone', preg_replace( "/[^0-9,.]/", "", $_REQUEST['contact_phone'] ) );
		}

		//Email
		if( isset($_REQUEST['contact_email'] ) ) {
			update_post_meta( $volunteer_opp_id, '_contact_email', sanitize_email( $_REQUEST['contact_email'] ) );
		}

		//Location Name
		if( isset($_REQUEST['location'] ) ) {
			update_post_meta( $volunteer_opp_id, '_location', sanitize_text_field( $_REQUEST['location'] ) );
		}

		//Street
		if( isset($_REQUEST['street'] ) ) {
			update_post_meta( $volunteer_opp_id, '_street', sanitize_text_field( $_REQUEST['street'] ) );
		}

		//City
		if( isset($_REQUEST['city'] ) ) {
			update_post_meta( $volunteer_opp_id, '_city', sanitize_text_field( $_REQUEST['city'] ) );
		}

		//State
		if( isset($_REQUEST['state'] ) ) {
			update_post_meta( $volunteer_opp_id, '_state', sanitize_text_field( $_REQUEST['state'] ) );
		}

		//Zip
		if( isset($_REQUEST['zip'] ) ) {
			update_post_meta( $volunteer_opp_id, '_zip', sanitize_text_field( $_REQUEST['zip'] ) );
		}

		//One-Time Opportunity?
		//If checkbox is checked then the opp is one time and is set to 1, otherwise set to 0.
		if( isset($_REQUEST['one-time-opportunity'] ) ) {
			update_post_meta( $volunteer_opp_id, '_one_time_opp', 1 );
		}
		else {
			update_post_meta( $volunteer_opp_id, '_one_time_opp', 0 );
		}

		//Start Date & Time stored as UNIX timestamp
		if( isset($_REQUEST['start-date-time'] ) ) {
			$formatted_start = sanitize_text_field( str_replace( '@', '', $_REQUEST['start-date-time'] ) );
			$start_date_time = strtotime( $formatted_start );
			update_post_meta( $volunteer_opp_id, '_start_date_time', $start_date_time );
		}

		//End Date & Time stored as UNIX timestamp
		if( isset($_REQUEST['end-date-time'] ) ) {
			$formatted_end = sanitize_text_field( str_replace( '@', '', $_REQUEST['end-date-time'] ) );
			$end_date_time = strtotime( $formatted_end );
			update_post_meta( $volunteer_opp_id, '_end_date_time', $end_date_time );
		}

		//Flexible Event Frequency (When will this event happen?)
		if( isset($_REQUEST['flexible_frequency'] ) ) {
			update_post_meta( $volunteer_opp_id, '_flexible_frequency', sanitize_text_field( $_REQUEST['flexible_frequency'] ) );
		}

		//Volunteer Limit
		//If checkbox is checked then the opp has a volunteer limit and is set to 1, otherwise set to 0.
		if( isset($_REQUEST['has-volunteer-limit'] ) ) {
			update_post_meta( $volunteer_opp_id, '_has_volunteer_limit', 1 );
		}
		else {
			update_post_meta( $volunteer_opp_id, '_has_volunteer_limit', 0 );
		}

		//Max Number of Volunteers Allowed
		if( isset($_REQUEST['volunteer-limit'] ) ) {
			update_post_meta( $volunteer_opp_id, '_volunteer_limit', absint( $_REQUEST['volunteer-limit'] ) );
		}
	}


	/**
	 * Display the additional profile fields we want to include on the user profile edit screen.
	 * 
	 * @param  object $user The WP_User object for the user who is going to be edited.
	 */
	public function show_extra_profile_fields( $user ){ ?>
    
	    <table class="form-table">
	    	<tr scope="row">
			    <th><label for="phone"><?php _e( 'Phone Number', 'wivm' ); ?></label></th>
			    <td>
			        <input type="text" name="phone" id="phone" value="<?php echo $this->format_phone_number( get_user_meta( $user->ID, 'phone', true ) ); ?>" class="regular-text" /><br />
			        <p class="description"><?php _e( 'Please enter your phone number in the format (000) 000-0000.', 'wivm' ); ?></p>
			    </td>
			</tr>
		</table>

    <?php
	}

	/**
	 * Save any additional user profile information we've added.
	 * 
	 * @param  int $user_id The user's ID whose profile we're going to edit.
	 */
	public function save_extra_profile_fields( $user_id ) {
 
	    if ( !current_user_can( 'edit_user', $user_id ) ){
	        return false;
	    }
	 	
	 	//Phone Number
	    update_usermeta( absint( $user_id ), 'phone', preg_replace( "/[^0-9,.]/", "", $_POST['phone'] ) );
	}

	/**
	 * Format a phone number that's provided only in integers.
	 *
	 * @todo  Remove duplicate of this method that exists in class-opportunity.php
	 * 
	 * @param  int $unformmated_number Phone number in only integers
	 * @return string Phone number formatted to look nice.
	 */
	public function format_phone_number( $unformatted_number ){
		if( $unformatted_number != '' ){
			return '(' . substr( $unformatted_number, 0, 3 ) . ') '. substr( $unformatted_number, 3, 3 ) . '-' . substr( $unformatted_number, 6 );	
		}
		else {
			return '';
		}
	}

} //class WI_Volunteer_Management_Admin