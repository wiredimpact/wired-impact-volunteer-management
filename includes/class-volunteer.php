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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    int $volunteer_opp_id The post ID for the volunteer opportunity.
	 */
	public function __construct( $user_id = null ) {

		if( $user_id != null ){
			$this->ID = $user_id;
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

} //class WI_Volunteer_Management_Volunteer