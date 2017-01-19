<?php

/**
 * Utility used to work with individual volunteer opportunities.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 */

/**
 * Utility used to work with individual volunteer opportunities.
 *
 * Stores the meta data for a single volunteer opportunity to be used within
 * the admin or on the frontend. Also handles display of opportunity information
 * such as phone numbers, times and addresses.
 *
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Opportunity {

	/**
	 * The post ID of this particular volunteer opportunity.
	 *
	 * @since    0.1
	 * @access   public
	 * @var      array    $ID    The post ID of this volunteer opportunity.
	 */
	public $ID;

	/**
	 * The metadata associated with this particular volunteer opportunity.
	 *
	 * @since    0.1
	 * @access   public
	 * @var      array    $opp_meta    The metadata associated with the volunteer opportunity.
	 */
	public $opp_meta;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1
	 * @param    int $volunteer_opp_id The post ID for the volunteer opportunity.
	 */
	public function __construct( $volunteer_opp_id ) {

		$this->ID = $volunteer_opp_id;
		$this->opp_meta = $this->retrieve_volunteer_opp_meta( $volunteer_opp_id );

	}

	/**
	 * Retrieve all the board event meta data.
	 *
	 * @todo  Fix zip code to use (int) sanitization, but this is tough given the use of defaults.
	 * @param int $volunteer_opp_id The ID of the volunteer opportunity we're referencing.
	 * @return array Associative array of all meta data for the board event.
	 */
	public function retrieve_volunteer_opp_meta( $volunteer_opp_id ){
		$volunteer_opp_meta_raw = get_post_custom( $volunteer_opp_id );
		$volunteer_opp_meta = array();

		//Contact Information
		$volunteer_opp_meta['contact_name'] 			= ( isset( $volunteer_opp_meta_raw['_contact_name'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_contact_name'][0] ) : $this->get_default_meta( 'default_contact_name' );
		$volunteer_opp_meta['contact_phone']			= ( isset( $volunteer_opp_meta_raw['_contact_phone'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_contact_phone'][0] ) : $this->get_default_meta( 'default_contact_phone' );
		$volunteer_opp_meta['contact_formatted_phone']	= $this->format_phone_number( $volunteer_opp_meta['contact_phone'] );
		$volunteer_opp_meta['contact_email']			= ( isset( $volunteer_opp_meta_raw['_contact_email'] ) ) ? sanitize_email( $volunteer_opp_meta_raw['_contact_email'][0] ) : $this->get_default_meta( 'default_contact_email' );

		//Location Information
		$volunteer_opp_meta['location'] 				= ( isset( $volunteer_opp_meta_raw['_location'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_location'][0] ) : $this->get_default_meta( 'default_location' );
		$volunteer_opp_meta['street'] 					= ( isset( $volunteer_opp_meta_raw['_street'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_street'][0] ) : $this->get_default_meta( 'default_street' );
		$volunteer_opp_meta['city'] 					= ( isset( $volunteer_opp_meta_raw['_city'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_city'][0] ) : $this->get_default_meta( 'default_city' );
		$volunteer_opp_meta['state'] 					= ( isset( $volunteer_opp_meta_raw['_state'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_state'][0] ) : $this->get_default_meta( 'default_state' );
		$volunteer_opp_meta['zip'] 						= ( isset( $volunteer_opp_meta_raw['_zip'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_zip'][0] ) : $this->get_default_meta( 'default_zip' );

		//Date and Time Information
		$volunteer_opp_meta['one_time_opp'] 			= ( isset( $volunteer_opp_meta_raw['_one_time_opp'] ) ) ? (int)$volunteer_opp_meta_raw['_one_time_opp'][0] : 0;
		$volunteer_opp_meta['start_date_time'] 			= ( isset( $volunteer_opp_meta_raw['_start_date_time'] ) && $volunteer_opp_meta_raw['_start_date_time'][0] != '' ) ? (int)$volunteer_opp_meta_raw['_start_date_time'][0] : '';
		$volunteer_opp_meta['end_date_time'] 			= ( isset( $volunteer_opp_meta_raw['_end_date_time']  ) && $volunteer_opp_meta_raw['_end_date_time'][0] != '' ) ? (int)$volunteer_opp_meta_raw['_end_date_time'][0] : '';
		$volunteer_opp_meta['flexible_frequency']		= ( isset( $volunteer_opp_meta_raw['_flexible_frequency'] ) ) ? sanitize_text_field( $volunteer_opp_meta_raw['_flexible_frequency'][0] ) : '';

		//Volunteer Limit Information
		$volunteer_opp_meta['has_volunteer_limit'] 		= ( isset( $volunteer_opp_meta_raw['_has_volunteer_limit'] ) ) ? (int)$volunteer_opp_meta_raw['_has_volunteer_limit'][0] : 0;
		$volunteer_opp_meta['volunteer_limit']			= ( isset( $volunteer_opp_meta_raw['_volunteer_limit'] ) ) ? (int)$volunteer_opp_meta_raw['_volunteer_limit'][0] : 0;

		return apply_filters( 'wivm_volunteer_opp_meta', $volunteer_opp_meta, $volunteer_opp_id );
	}

	/**
	 * Display meta information for this opportunity including a heading.
	 * 
	 * @param  string $meta_value The meta information to display.
	 * @param  string $heading    The heading to display in front of the meta information is one exists.
	 * @return mixed 			  Returns an empty string if no meta value, or it will echo the resulting string.
	 */
	public function display_meta( $meta_value, $heading = '' ){

		if( $meta_value == '' ){
			return false;
		}

		$html = '<span>';

		if( $heading != '' ){
			$html .= '<strong>' . $heading . '</strong> ';
		}

		$html .= $meta_value . '</span>';

		echo $html;
	}

	/**
	 * Load default meta information from settings if available.
	 *
	 * This is used to load default contact and location information for new opportunities so the admin doesn't
	 * have to enter the same information every time.
	 * 
	 * @param  string $key The key for this setting in the options, not the meta box.
	 * @return string      The value of the default or a blank string if none exists.
	 */
	public function get_default_meta( $key ){
		$options = new WI_Volunteer_Management_Options();
		$default = $options->get_option( $key );

		$value = '';
		if( $default != '' ){
			$value = $default;
		}

		return $value;
	}

	/**
	 * Return a single date and time for an opp, or the frequency if it's not a one-time opportunity.
	 * 
	 * @return string A string of the date and time, or the frequency of the opportunity.
	 */
	public function get_one_date_time(){
		if( $this->opp_meta['one_time_opp'] == 1 ){
			return $this->format_opp_times( $this->opp_meta['start_date_time'], $this->opp_meta['end_date_time'] );
		}
		else {
			return $this->opp_meta['flexible_frequency'];
		}
	}

	/**
	 * Format the opportunity times to be displayed.
	 * 
	 * @param int $start_date_time Timestamp of the start of the opportunity.
	 * @param int $end_date_time Timestamp of the end of the opportunity.
	 * @param bool $start_only True if we want only the start of the opportunity.
	 * @return string The formatted opportunity times to be displayed.
	 */
	public function format_opp_times( $start_date_time = '', $end_date_time = '', $start_only = false ){
		//If left blank then use the saved start and end times.
		if( $start_date_time == '' && $end_date_time == '' ){
			$start_date_time = $this->opp_meta['start_date_time'];
			$end_date_time 	 = $this->opp_meta['end_date_time'];
		}

		//Return an empty string if the start date time is blank.
		if( $start_date_time == '' ) return '';

		//If they want the start date and time only
		if( $start_only == true ){
			//translators: date and time format for date() function, see http://php.net/manual/en/function.date.php
			$opp_time = date_i18n( __( 'D, F j, Y \&#64; g:i a', 'wired-impact-volunteer-management' ), $start_date_time);

			return apply_filters( 'wivm_opp_time', $opp_time, $start_date_time, $end_date_time, $start_only );
		}

		//If dates are the same then only show date on first date, with time on both
		if( date( 'Ymd', $start_date_time ) == date( 'Ymd', $end_date_time ) ){
			//translators: date only format for date() function, see http://php.net/manual/en/function.date.php
			$opp_time =  date_i18n( __( 'D, F j, Y', 'wired-impact-volunteer-management' ), $start_date_time );
			$opp_time .= __( ' from ', 'wired-impact-volunteer-management' );
			//translators: time only format for date() function, see http://php.net/manual/en/function.date.php
			$opp_time .= date_i18n( __( 'g:i a', 'wired-impact-volunteer-management' ), $start_date_time );
			$opp_time .= ' – ';
			$opp_time .= date_i18n( __( 'g:i a', 'wired-impact-volunteer-management' ), $end_date_time );
		}
		//If dates are different then show dates for start and end
		else{
			//translators: date and time format for date() function, see http://php.net/manual/en/function.date.php
			$opp_time = date_i18n( __( 'D, F d, Y g:i a', 'wired-impact-volunteer-management' ), $start_date_time);
			$opp_time .= ' – ';
			$opp_time .= date_i18n( __( 'D, F d, Y g:i a', 'wired-impact-volunteer-management' ), $end_date_time);
		}

		return apply_filters( 'wivm_opp_time', $opp_time, $start_date_time, $end_date_time, $start_only );
	}

	/**
	 * Format a phone number that's provided only in integers.
	 *
	 * @todo   Remove duplicates of this method that exist in other classes
	 * @param  int $unformmated_number Phone number in only integers
	 * @return string Phone number formatted to look nice.
	 */
	public function format_phone_number( $unformatted_number ){
		$formatted_number = '';

		if( $unformatted_number != '' ){
			//translators: phone number pattern for preg_replace(), see http://php.net/manual/en/function.preg-replace.php
			$pattern = __( '/^(\d{3})(\d{3})(\d{4})$/', 'wired-impact-volunteer-management' );
			//translators: phone number replacement for preg_replace(), see http://php.net/manual/en/function.preg-replace.php
			$replacement = __( '($1) $2-$3', 'wired-impact-volunteer-management' );

			$formatted_number = preg_replace( $pattern, $replacement, $unformatted_number );
		}

		return apply_filters( 'wivm_formatted_phone', $formatted_number, $unformatted_number );
	}

	/**
  	 * Get the event location with a Google Maps link if possible and requested.
  	 * 
  	 * @param bool $make_maps_link Whether to include a Google Maps link.
  	 * @return string Formatted location possibly wrapped in Google Maps link.
  	 */
	public function format_address( $make_maps_link = true ){
		$location = ''; 

		//Add location name and comma only if content will be added after
		$location .= esc_html( $this->opp_meta['location'] );
		if( $this->opp_meta['location'] != '' && ( $this->opp_meta['street'] != '' || $this->opp_meta['city'] != '' || $this->opp_meta['state'] != '' || $this->opp_meta['zip'] != '' ) ){
			$location .= __( ', ', 'wired-impact-volunteer-management' );
		}

		//Add street
		$location .= esc_html( $this->opp_meta['street'] );
		if( $this->opp_meta['street'] != '' && ( $this->opp_meta['city'] != '' || $this->opp_meta['state'] != '' || $this->opp_meta['zip'] != '' ) ) {
			$location .= __( ', ', 'wired-impact-volunteer-management' );
		}

		//Add city
		$location .= esc_html( $this->opp_meta['city'] );
		if(  $this->opp_meta['city'] != '' && ( $this->opp_meta['state'] != '' || $this->opp_meta['zip'] != '' ) ) {
			$location .= __( ', ', 'wired-impact-volunteer-management' );
		}

		//Add state
		$location .= esc_html( $this->opp_meta['state'] );

		//Add zip code
		$location .= ( $location != '' ) ? ' ' . esc_html( $this->opp_meta['zip'] ) : esc_html( $this->opp_meta['zip'] );

		//Wrap in Google Maps link if requested
		if( $make_maps_link == true && $this->opp_meta['street'] != '' && $this->opp_meta['city'] != '' ){
			$location = $this->add_google_maps_link( $location );
		}

		return apply_filters( 'wivm_address', $location, $this->opp_meta, $make_maps_link );
	}

	/**
	 * Wrap an address in a Google Maps link.
	 *
	 * @param   string $address Address formatted in string.
	 * @return  string Formatted address wrapped in Google Maps link.
	 */
	protected function add_google_maps_link( $address ){
		
		$google_maps_string = str_replace( ' ', '+', $this->opp_meta['street'] . ' ' . $this->opp_meta['city'] . ' ' . $this->opp_meta['state'] . ' ' . $this->opp_meta['zip'] );
		$google_maps_url    = 'https://maps.google.com/maps?q=' . $google_maps_string;
		$google_maps_html   = '<a href="' . $google_maps_url . '" title="Map this location on Google Maps" target="_blank">';

		return $google_maps_html . $address . '</a>';
	}

	/**
	 * Convert email address to clickable email link.
	 * 
	 * @param  string $email_address Email address that we want to change into link.
	 * @return string Email address as clickable mailto link.
	 */
	public function get_email_as_link( $email_address ){
		if( is_email( $email_address ) ){
			return '<a href="mailto:' . $email_address . '" title="Send email">' . $email_address . '</a>';
		}
		else {
			return $email_address;
		}
	}

	/**
	 * Get the open number of volunteer spots for this opportunity.
	 *
	 * @return  int|string Integer for open number of spots, string for Unlimited or Closed.
	 */
	public function get_open_volunteer_spots(){
		$num_rsvps = $this->get_number_rsvps();

		//If there is no limit
		if( $this->opp_meta['has_volunteer_limit'] == 0 ){
			return __( 'Unlimited', 'wired-impact-volunteer-management' );
		}
		//If the limit has been reached.
		else if( $num_rsvps >= $this->opp_meta['volunteer_limit'] ){
			return __( 'Closed', 'wired-impact-volunteer-management' );
		}
		//If a limit exists and it hasn't been reached.
		else {
			return $this->opp_meta['volunteer_limit'] - $num_rsvps;
		}
	}

	/**
	 * Get the number of RSVPs that have taken place for this opportunity.
	 * 
	 * @return int The number of RSVPs for this opportunity.
	 */
	public function get_number_rsvps(){
		global $wpdb;

		$num_rsvps = $wpdb->get_var( $wpdb->prepare(
		        "
		         SELECT COUNT(*)
		         FROM " . $wpdb->prefix  . "volunteer_rsvps
		         WHERE post_id = %d and rsvp = %d
		        ",
		        array( $this->ID, 1 )
		) );

		return $num_rsvps;
	}

	/**
	 * Get the object array of all emails sent for this opportunity.
	 * 
	 * @return object The array of email vars for this opportunity.
	 */
	public function get_rsvp_emails(){
		global $wpdb;

		$query = "
		         SELECT *
		         FROM " . $wpdb->prefix  . "volunteer_emails
		         WHERE post_id = %d
		         ORDER BY time DESC
		        ";

		$query_values = array( $this->ID, 1 );

		return $wpdb->get_results( $wpdb->prepare( $query, $query_values ) );
	}

	/**
	 * Return whether we should allow people to sign up for this opportunity.
	 * 
	 * @return bool true if we should allow signups, false if not
	 */
	public function should_allow_rvsps(){
		$num_rsvps = $this->get_number_rsvps();

		//If there is a limit and its been reached then return false
		if( $this->opp_meta['has_volunteer_limit'] == 1 && $num_rsvps >= $this->opp_meta['volunteer_limit'] ){
			return false;
		}
		
		return true;
	}

	/**
	 * Get array of all volunteers signed up for a specific volunteer opportunity.
	 * 
	 * @return array Array of volunteer objects for each person that signed up for this opportunity.
	 */
	public function get_all_rsvped_volunteers(){
		global $wpdb;

		$query = "
		         SELECT user_id
		         FROM " . $wpdb->prefix  . "volunteer_rsvps
		         WHERE post_id = %d AND rsvp = %d
		         ORDER BY time DESC
		        ";

		$query_values = array( $this->ID, 1 );

		$volunteers = $wpdb->get_results( $wpdb->prepare( $query, $query_values ) );

		//Use the user id to grab a bunch info on each volunteer and store in the same variable using &.
		foreach( $volunteers as &$volunteer ){
			$volunteer = new WI_Volunteer_Management_Volunteer( $volunteer->user_id );
		}

		return $volunteers;			        
	}

} //class WI_Volunteer_Management_Opportunity