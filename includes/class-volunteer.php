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
			'first_name'			=> $user_data->first_name,
			'last_name'				=> $user_data->last_name,
			'email'					=> $user_data->user_email,
			'phone' 				=> $this->format_phone_number( get_user_meta( $this->ID, 'phone', true ) ),
			'notes'					=> esc_textarea( get_user_meta( $this->ID, 'notes', true ) ),
			'num_volunteer_opps' 	=> $this->get_num_volunteer_opps()
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
		         WHERE user_id = %d
		        ",
		        $this->ID
		) );

		return $num_volunteer_opps;
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

} //class WI_Volunteer_Management_Volunteer