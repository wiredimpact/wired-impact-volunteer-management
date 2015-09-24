<?php

/**
 * Utility used to retrieve all the settings and options for the plugin.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 */

/**
 * Utility used to retrieve all the settings and options for the plugin.
 *
 * We use this to not only retrieve sendings, but also to load the defaults
 * when the plugin is first actvated.
 *
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Options {

	/**
	 * Name of option saved in the WordPres options database table
	 *
	 * @since 0.1
	 * @var   string  
	 */
	public $option_name = 'wivm-settings';

	/**
	 * Array of defaults for all of our settings
	 *
	 * @since 0.1
	 * @var   array  
	 */
	public $defaults = array(
		//General
		'use_css'              				=> 1,

		//Defaults
		'default_contact_name'				=> '',
		'default_contact_phone' 			=> '',
		'default_contact_email' 			=> '',
		'default_contact_email' 			=> '',

		'default_location'      			=> '',
		'default_street'    				=> '',
		'default_city'          			=> '',
		'default_state'         			=> '',
		'default_zip'           			=> '',

		//Email
		'from_email_address'        		=> '',
		'from_email_name'					=> '',
		'volunteer_signup_email_subject'	=> 'Thanks for Signing Up to Volunteer',
		'volunteer_signup_email'      		=> 'Hi {volunteer_first_name},

Thanks for signing up to volunteer. Here are the details of the opportunity:

<strong>{opportunity_name}</strong>
When: {opportunity_date_time}
Location: {opportunity_location}

If you have any questions please reach out to {contact_name} at {contact_email} or by phone at {contact_phone}.',

		'admin_email_address'         		=> '',
		'admin_signup_email_subject'		=> 'Volunteer Signup Submission',
		'admin_signup_email'          		=> 'Hi, someone just signed up to volunteer! Here are the details:

Volunteer Name: {volunteer_first_name} {volunteer_last_name}
Email: {volunteer_email}
Phone: {volunteer_phone}

Opportunity Name: {opportunity_name}
When: {opportunity_date_time}
Location: {opportunity_location}',
		'days_prior_reminder'         		=> 4,
		'volunteer_reminder_email_subject' 	=> 'Your Volunteer Opportunity is Coming Up',
		'volunteer_reminder_email'    		=> 'Hi,

Thanks again for signing up to volunteer. Your opportunity is coming up soon. Here are the details:

{opportunity_name}
When: {opportunity_date_time}
Location: {opportunity_location}

If you have any questions please reach out to {contact_name} at {contact_email} or by phone at {contact_phone}.',
	);

	/**
	 * All settings saved into an easy to grab array
	 * 
	 * @var array
	 */
	protected $all_options = array();

	/**
	 * Retrieve all the settings for the plugin.
	 */
	public function __construct(){

		$this->all_options = $this->get_options();

	}

	/**
	 * Retrieve all setting from the database or use defaults if necessary.
	 * 
	 * @return array All of our options from the database
	 */
	public function get_options(){
		return get_option( $this->option_name, $this->defaults );
	}

	/**
	 * Get a single setting from the settings.
	 * 
	 * @param  mixed $option_name Name of option to retrieve
	 * @return mixed Value of option
	 */
	public function get_option( $option_name ){
		return $this->all_options[$option_name];
	}


	/**
	 * Add our default settings to the database via the options table.
	 *
	 * This method is called during activation, but will only add the settings if they don't already
	 * exist. If the settings are added then the defaults will show and can be changed on the Settings page.
	 */
	public function set_defaults(){
		add_option( $this->option_name, $this->defaults );
	}
}