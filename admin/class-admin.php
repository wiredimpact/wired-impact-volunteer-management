<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

      	wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_style( 'jquery-ui-smoothness', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css' );
		wp_enqueue_style( 'wivm-styles', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(  'wp-pointer' );
		wp_enqueue_script(  'jquery-ui-slider' );
    	wp_enqueue_script(  'jquery-ui-datepicker' );
    	wp_enqueue_script(  'jquery-timepicker', plugin_dir_url( __FILE__ ) . 'js/jquery-ui-timepicker.js', array( 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-datepicker' ) );
		wp_enqueue_script(  'wivm-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( 'wivm-admin', 'wivm_ajax', $this->get_localized_js_data() );

	}

	/**
	 * Get all the JS data we want to display. This allows us to use PHP to include information
	 * within the JS.
	 * 
	 * @return array Data to be displayed in the admin page's JS.
	 */
	public function get_localized_js_data(){
		$data = array(
			'remove_rsvp_pointer_text' 	=> '<h3>' . __( 'Are You Sure?', 'wired-impact-volunteer-management' ) . '</h3><p>' . __( 'Are you sure you want to remove their RSVP for this opportunity?', 'wired-impact-volunteer-management' ) . '</p>',
			'remove_rsvp_cancel_text' 	=> __( 'Cancel', 'wired-impact-volunteer-management' ),
			'remove_rsvp_confirm_text' 	=> __( 'Remove RSVP', 'wired-impact-volunteer-management' ),
			'remove_rsvp_error_text' 	=> __( 'Error, try again later.', 'wired-impact-volunteer-management' ),
			'remove_user_rsvp_nonce' 	=> wp_create_nonce( 'remove_user_rsvp_nonce' ),
			'hide_notice_nonce'			=> wp_create_nonce( 'hide_notice_nonce' ),
			'volunteer_email_nonce'		=> wp_create_nonce( 'volunteer_email_nonce' )
		);

		return $data;
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
			__( 'Wired Impact Volunteer Management: ', 'wired-impact-volunteer-management' ) . ' ' . __( 'Help & Settings', 'wired-impact-volunteer-management' ),
			__( 'Volunteer Mgmt', 'wired-impact-volunteer-management' ),
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
				__( 'Volunteers', 'wired-impact-volunteer-management' ),
				'manage_options',
				'wi-volunteer-management-volunteers',
				array( $this, 'load_page' ),
			),
			array(
				'wi-volunteer-management',
				'',
				__( 'Help & Settings', 'wired-impact-volunteer-management' ),
				'manage_options',
				'wi-volunteer-management-help-settings',
				array( $this, 'load_page' ),
			),
			array(
				NULL, //Not in menu
				'',
				__( 'Volunteer', 'wired-impact-volunteer-management' ),
				'manage_options',
				'wi-volunteer-management-volunteer',
				array( $this, 'load_page' ),
			),
		);

		// Allow submenu pages manipulation
		$submenu_pages = apply_filters( 'wivm_submenu_pages', $submenu_pages );

		// Loop through submenu pages and add them
		if ( count( $submenu_pages ) ) {
			foreach ( $submenu_pages as $submenu_page ) {
				// Add submenu page
				add_submenu_page( $submenu_page[0], $submenu_page[2] . ' - ' . __( 'Wired Impact Volunteer Management', 'wired-impact-volunteer-management' ), $submenu_page[2], $submenu_page[3], $submenu_page[4], $submenu_page[5] );
			}
		}

		//Change the submenu name for the 1st item
		global $submenu;
		if ( isset( $submenu['wi-volunteer-management'] ) && current_user_can( 'manage_options' ) ) {
			$submenu['wi-volunteer-management'][0][0] = __( 'Opportunities', 'wired-impact-volunteer-management' );
		}
	}

	/**
	 * Load the appropriate admin page
	 */
	public function load_page() {
		$page = filter_input( INPUT_GET, 'page' );

		switch ( $page ) {
			case 'wi-volunteer-management-volunteers':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/volunteers.php';
				break;

			case 'wi-volunteer-management-volunteer':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/volunteer.php';
				break;

			case 'wi-volunteer-management-help-settings':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/help-settings.php';
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
		register_setting( 'wivm-settings-group', 'wivm-settings', array( $this, 'process_wivm_settings_group_save' ) );
	}

	/**
	 * Complete any additional processing that must take place before new settings are saved.
	 * 
	 * @param  array $new_options Array of new options that are about to be saved.
	 * @return array              New options that are about to be saved, possibly adjusted.
	 */
	public function process_wivm_settings_group_save( $new_options ){

		//Rebuild reminder email cron events if necessary
		$existing_options = new WI_Volunteer_Management_Options();
		if( $new_options['days_prior_reminder'] != $existing_options->get_option( 'days_prior_reminder' ) ){
			$this->rebuild_all_reminders();
		}

		//Strip all extra characters out of the default contact phone number except the numbers
		$new_options['default_contact_phone'] = preg_replace( "/[^0-9,.]/", "", $new_options['default_contact_phone'] );


		return apply_filters( 'wivm_process_settings_group_save', $new_options );
	}

	/**
	 * Show a helpful tip below the WordPress editor on what information to include there.
	 */
	public function show_opp_editor_description( $post ){
		if( $post->post_type == 'volunteer_opp' ){
			echo '<p class="editor-help"><em>' . __( 'Use the text editor above to include information such as what volunteers will be doing, any requirements or preparation needed from volunteers, how the community will benefit and other details aside from those provided below.', 'wired-impact-volunteer-management' ) . '</em></p>';
		}
	}

	/**
	 * Add meta boxes for volunteer opportunities.
	 */
	public function add_meta_boxes(){
		//Opportunity details such as location and time
		add_meta_box(
            'volunteer-opportunity-details',						// Unique ID
            __( 'Volunteer Opportunity Details', 'wired-impact-volunteer-management' ),			// Box title
            array( $this, 'display_opportunity_details_meta_box' ),	// Content callback
            'volunteer_opp',                						// Post type
            'normal'												// Location
        );

        //Opportunity RSVP details such as who signed up
		add_meta_box(
            'volunteer-opportunity-rsvps',							// Unique ID
            __( 'Volunteer Opportunity RSVPs', 'wired-impact-volunteer-management' ), // Box title
            array( $this, 'display_opportunity_rsvps_meta_box' ),	// Content callback
            'volunteer_opp',                						// Post type
            'normal'												// Location
        );

		//Volunteer RSVP Email details
		add_meta_box(
			'volunteer-opportunity-emails',                                     // Unique ID
			__( 'Volunteer RSVP Emails', 'wired-impact-volunteer-management' ), // Box title
			array( $this, 'display_opportunity_emails_meta_box' ),              // Content callback
			'volunteer_opp',                                                    // Post type
			'side'                                                              // Location
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
		    <td colspan="2"><h3><?php _e( 'Contact Information', 'wired-impact-volunteer-management' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><label for="contact_name"><?php _e( 'Name', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="contact_name" name="contact_name" tabindex="10" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['contact_name']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="contact_phone"><?php _e( 'Phone Number', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="contact_phone" name="contact_phone" tabindex="20" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['contact_formatted_phone']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="contact_email"><?php _e( 'Email', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="contact_email" name="contact_email" tabindex="30" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['contact_email']; ?>" /></td>
		  </tr>

		  
		  <tr>
		    <td colspan="2"><h3><?php _e( 'Location Information', 'wired-impact-volunteer-management' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><label for="location"><?php _e( 'Location Name', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="location" name="location" tabindex="40" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['location']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="street"><?php _e( 'Street Address', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="street" name="street" tabindex="50" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['street']; ?>" /></td>
		  </tr>
		  
		  <tr>
		    <td><label for="city"><?php _e( 'City', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="city" name="city" tabindex="60" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['city']; ?>" /></td>
		  </tr>

		  <tr>
		    <td><label for="state"><?php _e( 'State', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="state" name="state" tabindex="70" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['state']; ?>" /></td>
		  </tr>

		  <tr>
		    <td><label for="zip"><?php _e( 'Zip', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="zip" name="zip" tabindex="80" class="regular-text" value="<?php echo $volunteer_opp->opp_meta['zip']; ?>" /></td>
		  </tr>

		  
		  <tr>
		    <td colspan="2"><h3><?php _e( 'Date and Time', 'wired-impact-volunteer-management' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><?php _e( 'One-Time Opportunity?', 'wired-impact-volunteer-management' ); ?></td>
		    <td>
		    	<input type="checkbox" id="one-time-opportunity" name="one-time-opportunity" tabindex="90" value="1" <?php checked( 1, $volunteer_opp->opp_meta['one_time_opp'] ); ?> />
		    	<label for="one-time-opportunity"><?php _e( 'This is a one-time opportunity at a fixed date and time.', 'wired-impact-volunteer-management' ); ?></label>
		    </td>
		  </tr>

		  <?php $one_time_class = ( $volunteer_opp->opp_meta['one_time_opp'] == 1 ) ? 'one-time' : 'flexible'; ?>
		  <tr class="one-time-field <?php echo $one_time_class; ?>">
		    <td><label for="start-date-time"><?php _e( 'Start Date & Time', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="start-date-time" name="start-date-time" tabindex="100" class="regular-text" value="<?php if ( $volunteer_opp->opp_meta['start_date_time'] != '' ) echo $volunteer_opp->format_opp_times( $volunteer_opp->opp_meta['start_date_time'], '', true ); ?>" /></td>
		  </tr>
		  
		  <tr class="one-time-field <?php echo $one_time_class; ?>">
		    <td><label for="end-date-time"><?php _e( 'End Date & Time', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td>
		      <input type="text" id="end-date-time" name="end-date-time" tabindex="110" class="regular-text" value="<?php if( $volunteer_opp->opp_meta['end_date_time'] != '' ) echo $volunteer_opp->format_opp_times( $volunteer_opp->opp_meta['end_date_time'], '', true ); ?>" />
		      <span class="error" style="display: none;"><?php _e( 'Woops, it looks like you set your event to end before it started.', 'wired-impact-volunteer-management' ); ?></span>
		    </td>
		  </tr>

		  <tr class="flexible-field <?php echo $one_time_class; ?>">
		    <td><label for="flexible_frequency"><?php _e( 'When Will This Event Happen?', 'wired-impact-volunteer-management' ); ?></label></td>
		    <td><input type="text" id="flexible_frequency" name="flexible_frequency" tabindex="120" class="regular-text" placeholder="<?php _e( 'On your own time, All summer, etc.', 'wired-impact-volunteer-management' ); ?>" value="<?php echo $volunteer_opp->opp_meta['flexible_frequency']; ?>" /></td>
		  </tr>

		  <tr>
		    <td colspan="2"><h3><?php _e( 'Volunteer Limit', 'wired-impact-volunteer-management' ); ?></h3></td>
		  </tr>	

		  <tr>
		    <td><?php _e( 'Is There a Volunteer Limit?', 'wired-impact-volunteer-management' ); ?></td>
		    <td>
		    	<input type="checkbox" id="has-volunteer-limit" name="has-volunteer-limit" tabindex="130" value="1" <?php checked( 1, $volunteer_opp->opp_meta['has_volunteer_limit'] ); ?> />
		    	<label for="has-volunteer-limit"><?php _e( 'Only a fixed number of people can participate in this volunteer opportunity.', 'wired-impact-volunteer-management' ); ?></label>
		    </td>
		  </tr>

		  <?php $volunteer_limit_class = ( $volunteer_opp->opp_meta['has_volunteer_limit'] == 1 ) ? 'has-volunteer-limit' : 'no-volunteer-limit'; ?>
		  <tr class="volunteer-limit-field <?php echo $volunteer_limit_class; ?>">
		    <td><label for="volunteer-limit"><?php _e( 'Max Number of Volunteers', 'wired-impact-volunteer-management' ); ?></label></td>
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

		do_action( 'wivm_save_volunteer_opp_meta', $volunteer_opp_id, $volunteer_opp );
	}

	/**
	 * Display the meta box for each volunteer that's signed up for the specific opportunity being viewed.
	 * 
	 * @todo   Use WI_Volunteer_Users_List_Table() object to display this information.
	 * 
	 * @param  object $post The volunteer opportunity object.
	 */
	public function display_opportunity_rsvps_meta_box( $post ){

		$volunteer_opp  = new WI_Volunteer_Management_Opportunity( $post->ID );
		$num_rsvped     = $volunteer_opp->get_number_rsvps();
		$open_spots     = $volunteer_opp->get_open_volunteer_spots();
		$volunteers     = $volunteer_opp->get_all_rsvped_volunteers();

		// Set the editor ID
		$editor_id      = 'volunteer-email-editor';
		$content        = get_option( $editor_id );

		// Set the editor options array
		$editor_options = array(
			'media_buttons' => false,
			'textarea_name' => $editor_id,
			'textarea_rows' => 10
		);

		?>

		<a class="open-volunteer-email"><?php _e( 'Email the Volunteers', 'wired-impact-volunteer-management' ); ?></a>
		<span class="num">| <?php echo __( 'Number of Open Spots:', 'wired-impact-volunteer-management' ) . ' ' . $open_spots; ?></span>
		<span class="num"><?php echo __( 'Number RSVPed:', 'wired-impact-volunteer-management' ) . ' ' . $num_rsvped; ?></span>

		<div class="volunteer-email-editor clear">
			<label for="volunteer-email-subject">Subject</label>
			<input type="text" name="volunteer-email-subject" id="volunteer-email-subject" class="regular-text" />
			<?php wp_editor( $content, $editor_id, $editor_options ); ?>
			<?php _e( '{volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone}, {contact_email}.', 'wired-impact-volunteer-management' ) ?>
			<button type="button" class="button button-primary button-large wivm-send-email" data-post-id="<?php echo $post->ID; ?>" data-user-id="<?php echo get_current_user_id(); ?>"><?php _e( 'Send Email', 'wired-impact-volunteer-management' ); ?></button>
		</div>

		<div class="rsvp-list-table clear">
			<table class="wp-list-table widefat fixed striped users">
				<thead>
					<th><?php _e( 'Name', 'wired-impact-volunteer-management' ); ?></th>
					<th><?php _e( 'E-mail', 'wired-impact-volunteer-management' ); ?></th>
					<th><?php _e( 'Phone', 'wired-impact-volunteer-management' ); ?></th>
					<th><?php _e( 'Remove RSVP', 'wired-impact-volunteer-management' ); ?></th>
				</thead>

				<?php if( !empty( $volunteers ) ): foreach( $volunteers as $volunteer ): ?>

					<tr>
						<td><a href="<?php echo $volunteer->get_admin_url(); ?>"><?php echo $volunteer->meta['first_name'] . ' ' . $volunteer->meta['last_name']; ?></a></td>
						<td><?php echo $volunteer->meta['email']; ?></td>
						<td><?php echo $volunteer->meta['phone']; ?></td>
						<td><a href="#remove-rsvp" class="button remove-rsvp" data-post-id="<?php echo $post->ID; ?>" data-user-id="<?php echo $volunteer->ID; ?>"><?php _e( 'Remove RSVP', 'wired-impact-volunteer-management' ); ?></a></td>
					</tr>

				<?php endforeach; else: ?>

					<tr>
						<td colspan="4"><?php _e( 'No one has signed up for this opportunity yet.', 'wired-impact-volunteer-management' ); ?></td>
					</tr>

				<?php endif; ?>

				<tfoot>
					<th><?php _e( 'Name', 'wired-impact-volunteer-management' ); ?></th>
					<th><?php _e( 'E-mail', 'wired-impact-volunteer-management' ); ?></th>
					<th><?php _e( 'Phone', 'wired-impact-volunteer-management' ); ?></th>
					<th><?php _e( 'Remove RSVP', 'wired-impact-volunteer-management' ); ?></th>
				</tfoot>
			</table>
		</div>
		<?php
	}


	/**
	 * @todo description
	 */
	public function process_volunteer_email() {

		$post_id  = absint( $_POST['data']['post_id'] );
		$user_id  = absint( $_POST['data']['user_id'] );
		$nonce    = $_POST['data']['nonce'];
		$subject  = $_POST['data']['subject'];
		$message  = $_POST['data']['message'];

		$data_array = array(
			'post_id' => $post_id,
			'user_id' => $user_id,
			'subject' => $subject,
			'message' => $message
		);

		// Verify our nonce
		if ( ! wp_verify_nonce( $nonce, 'volunteer_email_nonce' ) ) {
			_e( 'Security Check.', 'wired-impact-volunteer-management' );
			die();
		}

		// Get the opportunity data
		$opp   = new WI_Volunteer_Management_Opportunity( $post_id );

		$email = new WI_Volunteer_Management_Email( $opp );
		$email->send_volunteer_email( $data_array );
		$email->store_volunteer_email( $data_array );

		// Return 1 if it worked, false it not.
		echo '1';

		die();
	}


	/**
	 * Display the meta box to output the list of volunteer emails for this opportunity.
	 * 
	 * @param object $post The volunteer opportunity object.
	 */
	public function display_opportunity_emails_meta_box( $post ) {
		$volunteer_opp  = new WI_Volunteer_Management_Opportunity( $post->ID );
		$emails         = $volunteer_opp->get_rsvp_emails();
		$email_count    = count( $emails );

		// If this opportunity has any sent emails
		if ( ! empty( $emails ) ) {
			printf( _nx( '1 email has been sent', '%d emails have been sent', $email_count, 'email count', 'wired-impact-volunteer-management' ), $email_count );

			echo '<ul class="volunteer-email-list">';

			foreach ( $emails as $email ) {
				$user_data = get_userdata( $email->user_id );
				printf( '<li>%s by %s</li>', $email->time, $user_data->user_nicename );
			}

			echo '</ul>';
		} else {
			_e( 'No emails have been sent yet.', 'wired-impact-volunteer-management' );
		}
	}

	/**
	 * Display the additional profile fields we want to include on the user profile edit screen.
	 * 
	 * @param  object $user The WP_User object for the user who is going to be edited.
	 */
	public function show_extra_profile_fields( $user ){ 
    	$volunteer = new WI_Volunteer_Management_Volunteer( $user->ID );
    	?>
	    <table class="form-table">
	    	<tr scope="row">
			    <th><label for="phone"><?php _e( 'Phone Number', 'wired-impact-volunteer-management' ); ?></label></th>
			    <td>
			        <input type="text" name="phone" id="phone" value="<?php echo $volunteer->meta['phone']; ?>" class="regular-text" /><br />
			        <p class="description"><?php _e( 'Please enter your phone number in the format (000) 000-0000.', 'wired-impact-volunteer-management' ); ?></p>
			    </td>
			</tr>
			<tr scope="row">
			    <th><label for="notes"><?php _e( 'Notes', 'wired-impact-volunteer-management' ); ?></label></th>
			    <td>
			        <textarea name="notes" id="notes" rows="5" cols="30"><?php echo $volunteer->meta['notes']; ?></textarea><br />
			        <p class="description"><?php _e( 'Please enter any notes about this user.', 'wired-impact-volunteer-management' ); ?></p>
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
	    update_user_meta( absint( $user_id ), 'phone', preg_replace( "/[^0-9,.]/", "", $_POST['phone'] ) );
	    //Notes
	    update_user_meta( absint( $user_id ), 'notes', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['notes'] ) ) ) );

	}

	/**
	 * Add custom columns the volunteer opportunities content type list.
	 * 
	 * @param array $columns The default columns for volunteer opps admin list view.
	 * @return array Custom columns we want to use on the opps list.
	 */
	public function manage_opp_columns( $columns ) {
		$columns = array(
			'cb' 			=> '<input type="checkbox" />',
			'title' 		=> __( 'Title', 'wired-impact-volunteer-management' ),
			'location' 		=> __( 'Location', 'wired-impact-volunteer-management' ),
			'date_time' 	=> __( 'When', 'wired-impact-volunteer-management' ),
			'num_rsvped' 	=> __( 'Number of RSVPs', 'wired-impact-volunteer-management' ),
			'num_open_spots'=> __( 'Number of Open Spots', 'wired-impact-volunteer-management' )
		);

		return apply_filters( 'wivm_opp_columns', $columns );
	}

	/**
	 * Add When column as a sortable field.
	 * 
	 * @param array $columns List of sortable columns.
	 * @return array List of sortable columns with date_time included.
	 */
	public function sort_opp_columns( $columns ) {
		// Bail if the request is a flexible opportunity
		if ( isset( $_GET['opportunities'] ) && 'flexible' == $_GET['opportunities'] ) {
			return $columns;
		}

		$columns['date_time'] = 'date_time';
		return $columns;
	}

	/**
  	 * Display content for each custom column for volunteer opps.
  	 * 
  	 * @param string $column Column to be displayed.
  	 * @param int $post_id ID of the volunteer opp to be displayed.
  	 */
	public function show_opp_columns( $column, $post_id ){

		$opp = new WI_Volunteer_Management_Opportunity( $post_id );

		switch( $column ){

			case 'location':

				echo $opp->format_address();
				break;

			case 'date_time':

				echo $opp->get_one_date_time();
				break;

			case 'num_rsvped':

				echo $opp->get_number_rsvps();
				break;

			case 'num_open_spots':

				echo $opp->get_open_volunteer_spots();
				break;
		}

	}

	/**
	 * Query to construct the Opportunity filter views.
	 *
	 * This filtering is used for the Opportunities list view within the admin. The views being
	 * built here are Upcoming One-Time Opportunities, Past One-Time Opportunities and Flexible Opportunities.
	 * 
	 * @param object $query Post type WP Query
	 */
	public function edit_opps_query( $query ) {
		global $pagenow;

		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && 'volunteer_opp' == $_GET['post_type'] ) {

			// Set the filter queries
			if ( isset( $_GET['opportunities'] ) && 'all' != $_GET['opportunities'] ) {
				$query_args = array(
					'key' 		=> '_one_time_opp',
					'value' 	=> 1,
					'compare' 	=> 'flexible' == $_GET['opportunities'] ? '!=' : '=='
				);

				$query->query_vars['meta_query'] = array( $query_args );

				// Set one-time opp date based queries
				if ( 'flexible' != $_GET['opportunities'] ) {
					$date_args = array(
						'key' 		=> '_end_date_time',
						'value' 	=> current_time( 'timestamp' ),
						'compare' 	=> 'past_one_time' == $_GET['opportunities'] ? '<' : '>'
					);

					$query->query_vars['meta_query'] = array( $query_args, $date_args );
				}
			}

			// Set the initial sort order
			if ( isset( $_GET['opportunities'] ) && ! isset( $_GET['orderby'] ) ) {
				$query->query_vars['meta_key'] 	= '_start_date_time';
				$query->query_vars['orderby'] 	= 'meta_value_num';

				/*
				 * Set the order of opportunities with the closest opportunity at the top when
				 * viewing upcoming opportunities.
				 */
				$query->query_vars['order'] 	= 'upcoming_one_time' == $_GET['opportunities'] ? 'asc' : 'desc';
			} 

			do_action( 'wivm_after_opps_query', $query );
		}
	}

	/**
	 * Adds view filters links for Upcoming One-Time Opportunities, Past One-Time opportunities
	 * and Flexible opportunities.
	 * 
	 * @param array $views Existing views array
	 * @return array $views Reordered array
	 */
	public function set_opp_views( $views ) {

		// Store our new views in an array
		$new_views = array();

		// Removed args to allow a clean query for each view
		$stripped_query_args = esc_url( remove_query_arg( array( 'opportunities', 'orderby', 'order', 'post_status' ) ) );

		// All opportunities
		$class = ( isset( $_GET['opportunities'] ) && 'all' == $_GET['opportunities'] || ! isset( $_GET['opportunities'] ) ) ? 'current' : '';
		$all_query = esc_url( add_query_arg( 'opportunities', urlencode( 'all' ), $stripped_query_args ) ); 
		$new_views['all_opportunities'] = sprintf( '<a href="%s" class="%s">%s</a>', $all_query, $class, __( 'All Opportunities', 'wired-impact-volunteer-management' ) );

		// Upcoming one-time opportunities
		$class = ( isset( $_GET['opportunities'] ) && 'upcoming_one_time' == $_GET['opportunities'] ) ? 'current' : '';
		$upcoming_one_time_query = esc_url( add_query_arg( 'opportunities', urlencode( 'upcoming_one_time' ), $stripped_query_args ) );  
		$new_views['upcoming_one_time'] = sprintf( '<a href="%s" class="%s">%s</a>', $upcoming_one_time_query, $class, __( 'Upcoming One-time Opportunities', 'wired-impact-volunteer-management' ) );

		// Past one-time opportunities
		$class = ( isset( $_GET['opportunities'] ) && 'past_one_time' == $_GET['opportunities'] ) ? 'current' : '';
		$past_one_time_query = esc_url( add_query_arg( 'opportunities', urlencode( 'past_one_time' ), $stripped_query_args ) );  
		$new_views['past_one_time'] = sprintf( '<a href="%s" class="%s">%s</a>', $past_one_time_query, $class, __( 'Past One-time Opportunities', 'wired-impact-volunteer-management' ) );

		// Flexible opportunities
		$class = ( isset( $_GET['opportunities'] ) && 'flexible' == $_GET['opportunities'] ) ? 'current' : '';
		$flexible_query = esc_url( add_query_arg( 'opportunities', urlencode( 'flexible' ), $stripped_query_args ) );  
		$new_views['flexible'] = sprintf( '<a href="%s" class="%s">%s</a>', $flexible_query, $class, __( 'Flexible Opportunities', 'wired-impact-volunteer-management' ) );

		// Remove and replace the default views array with the new views array
		array_splice( $views, 0, 1, $new_views );

		return apply_filters( 'wivm_opportunities_views', $views );
	}

	/**
	 * Load the column sort method when the admin menu page is loaded.
	 */
	public function load_opp_sort() {
		add_filter( 'request', array( $this, 'sort_opportunities' ) );
	}

	/**
	 * Method to handle sorting the "When" column by _start_date_time.
	 * 
	 * @param array $vars All variables needed to handle sorting.
	 * @return array $vars Adjusted variables needed to handle sorting.
	 */
	public function sort_opportunities( $vars ) {
		if ( isset( $vars['post_type'] ) && 'volunteer_opp' == $vars['post_type'] && isset( $vars['orderby'] ) && 'date_time' == $vars['orderby'] ) {
			$vars = array_merge(
				$vars,
				array(
					'meta_key'	=> '_start_date_time',
					'orderby'	=> 'meta_value_num'
				)
			);
		}

		return $vars;
	}

	/**
	 * Process the AJAX request from the remove RSVP button on the individual volunteer page.
	 *
	 * This turns a volunteer's RSVP for a specific opportunity from 1 to 0 (yes to no) in the 
	 * volunteer_rsvps table. Much of this functionality happens within admin.js.
	 *
	 * @return  post_id|bool The post ID if everything worked, false otherwise
	 */
	public function remove_user_opp_rsvp(){
		$post_id 	= absint( $_POST['data']['post_id'] );
		$user_id 	= absint( $_POST['data']['user_id'] );
		$nonce 		= $_POST['data']['nonce'];

		//Verify our nonce
		if( !wp_verify_nonce( $nonce, 'remove_user_rsvp_nonce' ) ) {
			_e( 'Security Check.', 'wired-impact-volunteer-management' );
			die();
		}

		//Remove the user's RSVP from this opportunity.
		$user   = new WI_Volunteer_Management_Volunteer( $user_id );
		$status = $user->remove_rsvp_user_opp( $post_id );

		//Return 1 if it worked, false it not.
 		echo $status;
 		
 		die(); //Must use die() when using AJAX
	}

	/**
	 * Set up an auto email reminder for a specific opportunity when that opportunity is saved.
	 * 
	 * @param int $opp_id Post ID of the volunteer opportunity we're creating a reminder for.
	 * @param object $post The post object for the volunteer opportunity we're creating a reminder for.
	 * @return bool Returns false if we aren't going to schedule an opportunity reminder.
	 */
	public function schedule_auto_email_reminder( $opp_id, $post ){

		//Check autosave, post type, user caps
		if( wp_is_post_autosave( $opp_id ) || wp_is_post_revision( $opp_id ) ) {
		  return false;
		}
		if( $post->post_type != 'volunteer_opp' ){
		  return false;
		}
		if( !current_user_can( 'manage_options' ) ){
		  return false;
		}

		//Pull event information
		$opp = new WI_Volunteer_Management_Opportunity( $opp_id );

		//Gather cron info.  We have to convert everything to GMT since WP Cron sends based on GMT.
		$cron_hook = 'send_auto_email_reminders';
		$cron_args = array( $opp_id );
		if( $opp->opp_meta['one_time_opp'] == 1 && $opp->opp_meta['start_date_time'] != '' ){
		  $start_date_time_gmt = strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $opp->opp_meta['start_date_time'] ) ) . ' GMT' );

		  $options = new WI_Volunteer_Management_Options();
		  $days_prior_reminder = $options->get_option( 'days_prior_reminder' );
		  $new_reminder_time = $start_date_time_gmt - ( $days_prior_reminder * 86400 ); //86400 is one day in seconds
		}
		$current_time = current_time( 'timestamp', 1 );

		//Remove existing cron event for this volunteer opportunity if one exists
		wp_clear_scheduled_hook( $cron_hook, $cron_args );

		//Don't schedule the reminder under certain circumstances
		if( 
		  $post->post_status != 'publish' || //If opportunity isn't published
		  $opp->opp_meta['one_time_opp'] == 0 || //If opportunity is not at a specific date and time
		  $opp->opp_meta['start_date_time'] == '' || //If there is no start date for the opportunity
		  $current_time > $new_reminder_time //If the current time is passed the new reminder time
		  ){
		  return false;
		}

		//If we passed all the conditions then schedule the auto reminder
		wp_schedule_single_event( $new_reminder_time, $cron_hook, $cron_args );
		do_action( 'wivm_after_email_reminder_scheduled', $opp, $new_reminder_time );
	}

	/**
	 * Send volunteer reminder email.
	 *
	 * This method is called using cron and is never called in any other way.
	 * 
	 * @param  int $opp_id Volunteer opportunity ID.
	 */
	public function send_email_reminder( $opp_id ){

		$opp 	= new WI_Volunteer_Management_Opportunity( $opp_id );
		$email 	= new WI_Volunteer_Management_Email( $opp );
		$email->send_volunteer_reminder_email();

	}

	/**
	 * Loop through all opportunities and create or remove all auto email reminders.
	 *
	 * This is run when the number of days prior to send reminder emails changes.
	 */
	public function rebuild_all_reminders(){

		$opps = get_posts( array( 
		    'post_type' => 'volunteer_opp',
		    'post_status' => array( 'publish', 'pending', 'draft', 'future', 'trash' ),
		    'numberposts' => -1
		) );

		foreach ( $opps as $opp ){
			$this->schedule_auto_email_reminder( $opp->ID, $opp );
		}

	}

	/**
	 * Delete volunteer RSVPs. 
	 *
	 * This is called during the "delete_user" hook when a user is deleted.
	 * 
	 * @param int      $user_id  ID of the user who is being deleted.
	 * @param int|null $reassign ID of the user to reassign posts and links to.
	 */
	public function delete_volunteer_rsvps( $user_id, $reassign ){
		$volunteer = new WI_Volunteer_Management_Volunteer( $user_id );
		$volunteer->delete_rsvps();
	}

	/**
	 * Show a notice after the plugin is activated pushing people to info on how to get started.
	 *
	 * The notice only shows if the 'show_getting_started_notice' option in the database is set to 1.
	 * If users click the link or dismiss the notice using the 'x' we don't show it again.
	 *
	 * @todo  Create a class to allow for easy adding and hiding of notices.
	 */
	public function show_getting_started_notice(){
		$options = new WI_Volunteer_Management_Options();
		$show_notice = $options->get_option( 'show_getting_started_notice' );

		if( $show_notice == true ){

			$id = 'show_getting_started_notice';
			$classes = 'updated notice is-dismissible wivm-notice';
			$message = sprintf( __( 'We\'re excited for you to try Wired Impact Volunteer Management. <a href="%s">Learn how to get started.</a>' ), admin_url( 'admin.php?page=wi-volunteer-management-help-settings' ) );

			echo '<div id="' . $id . '" class="' . $classes . ' "><p>' .  $message . '</p></div>';
			
		}

	}

	/**
	 * Use AJAX to hide an admin notice by adjusting a setting.
	 *
	 * The notice must have an id that is named the same as the option to update.
	 */
	public function hide_notice(){
		$notice_id 	= sanitize_text_field( $_POST['data']['notice_id'] );
		$nonce 		= $_POST['data']['nonce'];

		//Verify our nonce
		if( !wp_verify_nonce( $nonce, 'hide_notice_nonce' ) ) {
			_e( 'Security Check.', 'wired-impact-volunteer-management' );
			die();
		}

		//Hide the notice so it's never shown again.
		$options = new WI_Volunteer_Management_Options();
		$options->set_option( $notice_id, 0 );
 		
 		die(); //Must use die() when using AJAX
	}

} //class WI_Volunteer_Management_Admin