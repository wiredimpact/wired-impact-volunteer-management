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
	public function __construct( $user_id = null ) {

		$this->opp_meta = $this->retrieve_volunteer_opp_meta( $volunteer_opp_id );
		dbgx_trace_var( $this->opp_meta );

	}

} //class WI_Volunteer_Management_Volunteer