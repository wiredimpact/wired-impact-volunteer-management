<?php
/**
 * Utility used to retrieve all the settings and options for the plugin.
 *
 * @since      1.0.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */
class WI_Volunteer_Management_Options {

	/**
	 * Name of option saved in the WordPres options database table
	 * 
	 * @var  string  
	 */
	public $option_name = 'wivm-settings';

	/**
	 * Array of defaults for all of our settings
	 * 
	 * @var  array  
	 */
	protected $defaults = array(
		'food'              => 'Text field default', //text field
		'color'				=> 'Text field default', //text field
		'muchcontent'		=> 'Default textarea', //textarea field
		'checkbox_1'		=> true //textarea field
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
}