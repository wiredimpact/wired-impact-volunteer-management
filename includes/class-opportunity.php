<?php
/**
 * Utility used to work with individual volunteer opportunities.
 *
 * Stores the meta data for a single volunteer opportunity to be used within
 * the admin or on the frontend. Also handles display of opportunity information
 * such as phone numbers, times and addresses.
 *
 * @since      1.0.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */
class WI_Volunteer_Management_Opportunity {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $opp_meta    The metadata associated with the volunteer opportunity.
	 */
	public $opp_meta;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    int $volunteer_opp_id The post ID for the volunteer opportunity.
	 */
	public function __construct( $volunteer_opp_id ) {

		$this->opp_meta = $this->retrieve_volunteer_opp_meta( $volunteer_opp_id );

	}

	/**
	 * Retrieve all the board event meta data.
	 * 
	 * @param int $volunteer_opp_id The ID of the volunteer opportunity we're referencing.
	 * @return array Associative array of all meta data for the board event.
	 */
	public function retrieve_volunteer_opp_meta( $volunteer_opp_id ){
		$volunteer_opp_meta_raw = get_post_custom( $volunteer_opp_id );
		$volunteer_opp_meta = array();

		//Contact Information
		$volunteer_opp_meta['contact_name'] 		= ( isset( $volunteer_opp_meta_raw['_contact_name'] ) ) ? $volunteer_opp_meta_raw['_contact_name'][0] : '';
		$volunteer_opp_meta['contact_phone']		= ( isset( $volunteer_opp_meta_raw['_contact_phone'] ) ) ? $volunteer_opp_meta_raw['_contact_phone'][0] : '';
		$volunteer_opp_meta['contact_email']		= ( isset( $volunteer_opp_meta_raw['_contact_email'] ) ) ? $volunteer_opp_meta_raw['_contact_email'][0] : '';

		//Location Information
		$volunteer_opp_meta['location'] 			= ( isset( $volunteer_opp_meta_raw['_location'] ) ) ? $volunteer_opp_meta_raw['_location'][0] : '';
		$volunteer_opp_meta['street'] 				= ( isset( $volunteer_opp_meta_raw['_street'] ) ) ? $volunteer_opp_meta_raw['_street'][0] : '';
		$volunteer_opp_meta['city'] 				= ( isset( $volunteer_opp_meta_raw['_city'] ) ) ? $volunteer_opp_meta_raw['_city'][0] : '';
		$volunteer_opp_meta['state'] 				= ( isset( $volunteer_opp_meta_raw['_state'] ) ) ? $volunteer_opp_meta_raw['_state'][0] : '';
		$volunteer_opp_meta['zip'] 					= ( isset( $volunteer_opp_meta_raw['_zip'] ) ) ? $volunteer_opp_meta_raw['_zip'][0] : '';

		//Date and Time Information
		$volunteer_opp_meta['one_time_opp'] 		= ( isset( $volunteer_opp_meta_raw['_one_time_opp'] ) ) ? $volunteer_opp_meta_raw['_one_time_opp'][0] : 0;
		$volunteer_opp_meta['start_date_time'] 		= ( isset( $volunteer_opp_meta_raw['_start_date_time'] ) && $volunteer_opp_meta_raw['_start_date_time'][0] != '' ) ? (int)$volunteer_opp_meta_raw['_start_date_time'][0] : '';
		$volunteer_opp_meta['end_date_time'] 		= ( isset( $volunteer_opp_meta_raw['_end_date_time']  ) && $volunteer_opp_meta_raw['_end_date_time'][0] != '' ) ? (int)$volunteer_opp_meta_raw['_end_date_time'][0] : '';
		$volunteer_opp_meta['frequency']			= ( isset( $volunteer_opp_meta_raw['_frequency'] ) ) ? $volunteer_opp_meta_raw['_frequency'][0] : '';

		//Volunteer Limit Information
		$volunteer_opp_meta['has_volunteer_limit'] 	= ( isset( $volunteer_opp_meta_raw['_has_volunteer_limit'] ) ) ? $volunteer_opp_meta_raw['_has_volunteer_limit'][0] : 0;
		$volunteer_opp_meta['volunteer_limit']		= ( isset( $volunteer_opp_meta_raw['_volunteer_limit'] ) ) ? $volunteer_opp_meta_raw['_volunteer_limit'][0] : 0;

		return apply_filters( 'wivm_volunteer_opp_meta', $volunteer_opp_meta, $volunteer_opp_id );
	}

	/**
	 * Format the opportunity times to be displayed.
	 * 
	 * @param int $start_date_time Timestamp of the start of the opportunity.
	 * @param int $end_date_time Timestamp of the end of the opportunity.
	 * @param bool $start_only True if we want only the start of the opportunity.
	 * @return string The formatted opportunity times to be displayed.
	 */
	public function format_opp_times( $start_date_time, $end_date_time, $start_only = false ){
		//Return an empty string if the start date time is blank.
		if( $start_date_time == '' ) return '';

		//If they want the start date and time only
		if( $start_only == true ){
		  $opp_time = date( __( 'D, F d, Y \&#64; g:i a', 'wivm' ), $start_date_time);
		  
		  return apply_filters( 'wivm_opp_time', $opp_time, $start_date_time, $end_date_time, $start_only );
		}

		//If dates are the same then only show date on first date, with time on both
		if( date( 'Ymd', $start_date_time ) == date( 'Ymd', $end_date_time ) ){
		  $opp_time = date( __( 'D, F d, Y', 'wivm' ), $start_date_time) . '<br />';
		  $opp_time .= date( __( 'g:i a', 'wivm' ), $start_date_time);
		  $opp_time .= ' - ';
		  $opp_time .= date( __( 'g:i a', 'wivm' ), $end_date_time);
		}
		//If dates are different then show dates for start and end
		else{
		  $opp_time = date( __( 'D, F d, Y g:i a', 'wivm' ), $start_date_time);
		  $opp_time .= ' - <br />';
		  $opp_time .= date( __( 'D, F d, Y g:i a', 'wivm' ), $end_date_time);
		}

		return apply_filters( 'wivm_opp_time', $opp_time, $start_date_time, $end_date_time, $start_only );
	}

	/**
	 * Format a phone number that's provided only in integers.
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

} //class WI_Volunteer_Management_Opportunity