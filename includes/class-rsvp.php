<?php
/**
 * Utility used to work with an RSVP to a volunteer opportunity.
 *
 * Handles saving and bringing up information about a single RSVP for a specific opportunity.
 *
 * @since      1.0.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */
class WI_Volunteer_Management_RSVP {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $user_id = null, $opportunity_id = null ) {

		if( $user_id != null && $opportunity_id != null ){
			$this->save_rsvp( $user_id, $opportunity_id );
		}

	}

	/**
	 * Via ajax save the RSVP for this volunteer.
	 * 
	 * Via ajax we save the RSVP for this volunteer to our volunteer_rsvps table. We only
	 * update the table if the user is RSVPing for the first time.
	 *
	 * @param  int $user_id ID of user we want to sign up for this opportunity.
	 * @param  int $opportunity_id Post ID of this volunteer opportunity.
	 * @return bool true means successfully RSVPed, false means they already signed up.
	 */
	public function save_rsvp( $user_id, $opportunity_id ){

		$user_id = absint( $user_id );
		$opportunity_id = absint( $opportunity_id );
		$rsvp = 1; //We always set this to 1 for going, 0 for not going.

		global $wpdb;
		$table_name = $wpdb->prefix . 'volunteer_rsvps';
		$volunteer_rsvp_status = $this->volunteer_rsvp_status( $user_id, $opportunity_id );
		$time = current_time( 'mysql' );

		//Insert data into database
		if( $volunteer_rsvp_status === false ){
			$wpdb->insert(
		        $table_name,
		        array(
		        	'user_id' 	=> $user_id,
		        	'post_id' 	=> $opportunity_id,
		        	'rsvp' 		=> $rsvp,
		        	'time' 		=> $time
		        ),
		        array( '%d', '%d', '%d', '%s' ) //All of these should be saved as integers except for the current date-time
			);

			$result = true; //Successfully RSVPed
		}
		else { //This person was already RSVPed for this opportunity
			$result = false;
		}
		
		do_action( 'wivm_after_opp_rsvp', $user_id, $opportunity_id );

		return $result;
	}

	/**
	 * Provides the status of the RSVP for this volunteer for this opportunity.
	 *
	 * @param  int $user_id ID of user we want to sign up for this opportunity.
	 * @param  int $opportunity_id Post ID of this volunteer opportunity.
	 * @return bool|int False means hasn't RSVPed yes or no.  1 means going, 0 means not going, but signed up previously.
	 */
	public function volunteer_rsvp_status( $user_id, $opportunity_id ){
		global $wpdb;

		//Check if this user has already RSVPed for this opportunity.
		//NULL means they haven't yet.
		$rsvp = $wpdb->get_var( $wpdb->prepare(
		        "
		         SELECT rsvp
		         FROM " . $wpdb->prefix  . "volunteer_rsvps
		         WHERE post_id = %d
		         AND user_id = %d
		        ",
		        $opportunity_id,
		        $user_id
		      ) );

		$volunteer_rsvp_status = ( $rsvp == NULL ) ? false : (int)$rsvp;

		return $volunteer_rsvp_status;
	}

} //class WI_Volunteer_Management_RSVP