<?php

/**
 * Utility used to work with individual volunteers.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 */

/**
 * Utility used to work with individual volunteers.
 *
 * Stores the data for an individual volunteer to be used throughout the application.
 *
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>s
 */
class WI_Volunteer_Management_Volunteer {

	/**
	 * The user ID assicated with the volunteer.
	 *
	 * @since    0.1
	 * @access   public
	 * @var      int    $ID    The user ID associated with the volunteer.
	 */
	public $ID;

	/**
	 * The metadata assicated with the volunteer.
	 *
	 * @since    0.1
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
	 * @since    0.1
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
		$user_data  = get_userdata( $this->ID );
		$this->meta = array(
			'first_name'               => $user_data->first_name,
			'last_name'                => $user_data->last_name,
			'email'                    => $user_data->user_email,
			'phone'                    => $this->format_phone_number( get_user_option( 'phone', $this->ID ) ),
			'notes'                    => esc_textarea( get_user_option( 'notes', $this->ID ) ),
			'num_volunteer_opps'       => $this->get_num_volunteer_opps(),
			'first_volunteer_opp_time' => $this->get_first_volunteer_opp_time()
		);
	}

	/**
	 * Format a phone number that's provided only in integers.
	 *
	 * @todo   Remove duplicates of this method that exist in other classes
	 *
	 * @param  int $unformatted_number Phone number in only integers.
	 * @return string Phone number formatted to look nice.
	 */
	public function format_phone_number( $unformatted_number ) {
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
	 * Get the date and time of the volunteer's first RSVP.
	 *
	 * This is used to display the year the person started volunteering within the admin. It's worth
	 * noting that this isn't the time of the opportunity, but rather when they RSVPed.
	 *
	 * @return string The date and time of the first volunteer RSVP.
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
			// All Volunteer Opportunities.
			case 'all':
				$query = "
				         SELECT post_id
				         FROM " . $wpdb->prefix  . "volunteer_rsvps
				         WHERE user_id = %d AND rsvp = %d
				         ORDER BY time DESC
				        ";

				$query_values = array( $this->ID, 1 );

				break;

			// One-Time Volunteer Opportunities.
			// For this query we joined the postmeta table on itself in order to use two meta values.
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

			// Flexible Volunteer Opportunities.
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

		// Use post id to grab a bunch info on each opportunity and store in the same variable using &.
		foreach ( $volunteer_opps as &$opp ) {
			$opp = new WI_Volunteer_Management_Opportunity( $opp->post_id );
		}

		return apply_filters( 'wivm_one_volunteers_opps', $volunteer_opps, $this->ID );
	}

	/**
	 * Remove an RSVP for a user for a specific volunteer opportunity. This is usually done through AJAX.
	 *
	 * @param  int $post_id ID of the volunteer opportunity to have its RSVP removed.
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
	 * @return string The URL needed to view this volunteer's information.
	 */
	public function get_admin_url() {

		return get_admin_url( null, 'admin.php?page=wi-volunteer-management-volunteer&user_id=' . $this->ID );
	}

	/**
	 * Create a new volunteer user or update one if the email address is already used.
	 *
	 * @param array $form_fields The submitted volunteer opportunity form info.
	 */
	public function create_update_user( $form_fields ) {

		// Prepare userdata to be added for a new user or updated for an existing user.
		$userdata = array(
			'first_name' => sanitize_text_field( $form_fields['wivm_first_name'] ),
			'last_name'  => sanitize_text_field( $form_fields['wivm_last_name'] ),
		);

		// Check if the email address is already in use and if not, create a new user.
		$wivm_email    = sanitize_email( $form_fields['wivm_email'] );
		$existing_user = email_exists( $wivm_email );

		if ( ! $existing_user ) {

			$userdata['user_login'] = $wivm_email;
			$userdata['user_email'] = $wivm_email;
			$userdata['user_pass']  = wp_generate_password();
			$userdata['role']       = 'volunteer';

			$user_id = wp_insert_user( $userdata );

			// Update phone for new users.
			update_user_option( $user_id, 'phone', preg_replace( '/[^0-9]/', '', $form_fields['wivm_phone'] ) );

		} else {

			// User already exists: only update their data if they have the volunteer role.
			// This prevents unauthenticated users from overwriting data for admins or other roles.
			$user_id       = $existing_user;
			$should_update = $this->should_update_existing_user( $existing_user );

			if ( $should_update ) {

				$userdata['ID'] = $existing_user;
				wp_update_user( $userdata );
				update_user_option( $user_id, 'phone', preg_replace( '/[^0-9]/', '', $form_fields['wivm_phone'] ) );
			}

			// On multisite we need to add the user to this site if they don't have access.
			if ( is_multisite() && ! is_user_member_of_blog( $existing_user ) ) {

				add_user_to_blog( get_current_blog_id(), $existing_user, 'volunteer' );
				update_user_option( $existing_user, 'notes', '' );
			}
		}

		$this->ID = $user_id;

		do_action( 'wivm_create_update_user', $this->ID, $form_fields );
	}

	/**
	 * Check if an existing user's data should be updated after volunteer form submission.
	 *
	 * Only users with the "volunteer" role should have their data updated.
	 * This protects admin accounts and other user types from having their
	 * data overwritten by unauthenticated form submissions.
	 *
	 * @param int $user_id The ID of the existing user.
	 * @return bool True if the user's data should be updated, false otherwise.
	 */
	private function should_update_existing_user( $user_id ) {

		$user = get_userdata( $user_id );

		if ( ! $user ) {

			return false;
		}

		// Only allow updates for users with the volunteer role.
		$should_update = in_array( 'volunteer', (array) $user->roles, true );

		/**
		 * Filter whether an existing user's data should be updated after volunteer form submission.
		 *
		 * @param bool    $should_update Whether the user's data should be updated.
		 * @param int     $user_id       The ID of the existing user.
		 * @param WP_User $user          The user object.
		 */
		return apply_filters( 'wivm_should_update_existing_volunteer', $should_update, $user_id, $user );
	}

	/**
	 * Delete RSVPs for this user.
	 *
	 * This is typically done right before the user is deleted from WordPress entirely.
	 *
	 * @return int|bool Int for number of rows updated of false on error
	 */
	public function delete_rsvps() {
		global $wpdb;

		$delete_info = $wpdb->delete(
			$wpdb->prefix . 'volunteer_rsvps',
			array( 'user_id' => $this->ID ),
			array( '%d' )
		);

		return $delete_info;
	}
} //class WI_Volunteer_Management_Volunteer
