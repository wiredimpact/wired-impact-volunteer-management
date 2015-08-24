<?php
/**
 * Utility used to work with individual volunteers.
 *
 * Stores the data for an individual volunteer to be used throughout the application.
 *
 * @since      1.0.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */
class WI_Volunteer_Management_Volunteer {

	/**
	 * The user ID assicated with the volunteer.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      int    $ID    The user ID associated with the volunteer.
	 */
	public $ID;

	/**
	 * The metadata assicated with the volunteer.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $meta    The metadata associated with the volunteer.
	 */
	public $meta;

	/**
	 * Populate the meta info for the volunteer, create a new volunteer or update an existing volunteer's info.
	 *
	 * If the user_id is provided then we populate the meta property with a bunch of info on the volunteer.
	 * If the user_id is not provided, but the form_fields are then we either create a new user or update an existing one.
	 *
	 * @since    1.0.0
	 * @param    int $user_id The user ID for the volunteer.
	 * @param    array $form_fields The volunteer opportunity form fields as they were submitted.
	 */
	public function __construct( $user_id = null, $form_fields = null ) {

		if( $user_id != null ){
			$this->ID = $user_id;
			$this->set_meta();
		}
		elseif( $form_fields != null ) {
			$this->create_update_user( $form_fields );
			$this->set_meta();
		}

	}

	/**
	 * Set additional meta information for the volunteer immediately when the object is instantiated.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/get_userdata
	 */
	public function set_meta(){
		$user_data = get_userdata( $this->ID );
		$this->meta = array(
			'first_name'				=> $user_data->first_name,
			'last_name'					=> $user_data->last_name,
			'email'						=> $user_data->user_email,
			'phone' 					=> $this->format_phone_number( get_user_meta( $this->ID, 'phone', true ) ),
			'notes'						=> esc_textarea( get_user_meta( $this->ID, 'notes', true ) ),
			'num_volunteer_opps' 		=> $this->get_num_volunteer_opps(),
			'first_volunteer_opp_time'	=> $this->get_first_volunteer_opp_time()
		);
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

	/**
	 * Get the number of volunteer opportunities this volunteer has signed up for.
	 * 
	 * @return int Number of volunteer opportunities signed up for
	 */
	public function get_num_volunteer_opps(){
		global $wpdb;

		$num_volunteer_opps = $wpdb->get_var( $wpdb->prepare(
		        "
		         SELECT COUNT(*)
		         FROM " . $wpdb->prefix  . "volunteer_rsvps
		         WHERE user_id = %d AND rsvp = %d
		        ",
		        array( $this->ID, 1 )
		) );

		return $num_volunteer_opps;
	}

	/**
	 * [get_first_volunteer_opp_timestamp description]
	 * @return [type] [description]
	 */
	public function get_first_volunteer_opp_time(){
		global $wpdb;

		$first_volunteer_opp_time = $wpdb->get_var( $wpdb->prepare(
		        "
		         SELECT MIN( time )
		         FROM " . $wpdb->prefix  . "volunteer_rsvps
		         WHERE user_id = %d AND rsvp = %d
		        ",
		        array( $this->ID, 1 )
		) );

		return $first_volunteer_opp_time;
	}

	/**
	 * Get an array with all of the volunteer opportunities this person has signed up for.
	 *
	 * First we pull only the IDs of the posts that have been signed up for with the most recent one they signed 
	 * up for first. Then we create a new WI_Volunteer_Management_Opportunity object for each and return it.
	 *
	 * @todo  Fix order so it pulls them by volunteer opp date, not the date they signed up.
	 *
	 * @param  string $type Whether we want 'all', 'one-time' or 'flexible' volunteer opportunities.
	 * @return array Array of all the volunteer opportunities as objects.
	 */
	public function get_volunteer_opps( $type ){
		global $wpdb;

		switch( $type ){
			//All Volunteer Opportunities
			case 'all':

				$query = "
				         SELECT post_id
				         FROM " . $wpdb->prefix  . "volunteer_rsvps
				         WHERE user_id = %d AND rsvp = %d
				         ORDER BY time DESC
				        ";

				$query_values = array( $this->ID, 1 );
				        
				break;

			//One-Time Volunteer Opportunities
			//For this query we joined the postmeta table on itself in order to use two meta values.
			case 'one-time':

				$query = "
				         SELECT rsvps.post_id
				         FROM " . $wpdb->prefix  . "volunteer_rsvps AS rsvps
				         INNER JOIN " . $wpdb->prefix  . "postmeta AS p1
				         ON rsvps.post_id = p1.post_id
				         INNER JOIN " . $wpdb->prefix  . "postmeta AS p2
						 ON rsvps.post_id = p2.post_id
				         WHERE rsvps.user_id = %d AND rsvps.rsvp = %d AND p1.meta_key = %s AND p1.meta_value = %d AND p2.meta_key = %s
				         ORDER BY p2.meta_value DESC
				        ";

				$query_values = array( $this->ID, 1, '_one_time_opp', 1, '_start_date_time' );

				break;

			//Flexible Volunteer Opportunities
			case 'flexible':

				$query = "
				         SELECT rsvps.post_id
				         FROM " . $wpdb->prefix  . "volunteer_rsvps AS rsvps
				         INNER JOIN " . $wpdb->prefix  . "postmeta AS postmeta
						 ON rsvps.post_id = postmeta.post_id
				         WHERE rsvps.user_id = %d AND rsvps.rsvp = %d AND postmeta.meta_key = %s AND postmeta.meta_value = %d
				        ";

				$query_values = array( $this->ID, 1, '_one_time_opp', 0 );

				break;
		}

		$volunteer_opps = $wpdb->get_results( $wpdb->prepare( $query, $query_values ) );

		//Use post id to grab a bunch info on each opportunity and store in the same variable using &.
		foreach( $volunteer_opps as &$opp ){
			$opp = new WI_Volunteer_Management_Opportunity( $opp->post_id );
		}

		return $volunteer_opps;
	}

	/**
	 * Remove an RSVP for a user for a specific volunteer opportunity. This is usually done through AJAX.
	 * 
	 * @param  int $post_id ID of the volunteer opportunity to have its RSVP removed
	 * @return int|bool The number of rows updated or false if error
	 */
	public function remove_rsvp_user_opp( $post_id ){
		global $wpdb;

		$status = $wpdb->update( 
			$wpdb->prefix  . 'volunteer_rsvps', 
			array( //Data to update
				'rsvp' => 0
			), 
			array( //Where
				'user_id' => $this->ID,
			 	'post_id' => $post_id
			), 
			array( '%d'	), //Data formats
			array( '%d', '%d' ) //Where formats
		);

		return $status;
	}

	/**
	 * Get the admin link to look at this specific volunteer.
	 *
	 * This is not a link to the typical user edit screen. This page includes a lot of information on the 
	 * volunteer including the contact info, notes on them and which volunteer opportunities they signed up for.
	 * 
	 * @param  int $user_id The volunteer's ID
	 * @return string       The URL needed to view this volunteer's information.
	 */
	public function get_admin_url(){

		return get_admin_url( null, 'admin.php?page=wi-volunteer-management-volunteer&user_id=' . $this->ID );		
	}

	/**
	 * Create a new volunteer user or update one if the email address is already used.
	 * 
	 * @param  array $form_fields The submitted volunteer opportunity form info
	 * @return int   The user id of the new or updated WordPress user
	 */
	public function create_update_user( $form_fields ){
		//Prepare userdata to be added for a new user or updated for an existing user.
		$userdata = array( 
			'first_name' 	=> sanitize_text_field( $form_fields['wivm_first_name'] ),
			'last_name'  	=> sanitize_text_field( $form_fields['wivm_last_name'] ),
		);

		//Check if the email address is already in use and if not, create a new user.
		$wivm_email = sanitize_email( $form_fields['wivm_email'] );
		$existing_user = email_exists( $wivm_email );
		if( !$existing_user ){
			$userdata['user_login'] 	= $wivm_email;
			$userdata['user_email']		= $wivm_email;
			$userdata['user_pass'] 		= wp_generate_password();
			$userdata['role']			= 'volunteer';

			$user_id = wp_insert_user( $userdata );
		}
		//If the user already exists, update the user based on their email address
		else {
			$userdata['ID'] = $existing_user;

			$user_id = wp_update_user( $userdata );
		}

		//Update custom user meta for new and existing volunteers.
		update_user_meta( $user_id, 'phone', preg_replace( "/[^0-9,.]/", "", $form_fields['wivm_phone'] ) );

		$this->ID = $user_id;
	}

	/**
	 * Delete RSVPs for this user.
	 *
	 * This is typically done right before the user is deleted from WordPress entirely.
	 * 
	 * @return int|bool Int for number of rows updated of false on error
	 */
	public function delete_rsvps(){
		global $wpdb;

		$delete_info = $wpdb->delete(
				$wpdb->prefix  . "volunteer_rsvps",
				array( 'user_id' => $this->ID ),
				array( '%d' )
		);

		return $delete_info;
	}

} //class WI_Volunteer_Management_Volunteer