<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class WI_Volunteer_Management_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register our menu and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	function register_settings_page() {

		// Base 64 encoded SVG image
		$icon_svg = '';

		// Add main page
		$admin_page = add_menu_page(
			__( 'Wired Impact Volunteer Management: ', 'wivm' ) . ' ' . __( 'Settings', 'wivm' ),
			__( 'Volunteer Management', 'wivm' ),
			'manage_options',
			'wi-volunteer-management',
			array( $this, 'load_page' ),
			$icon_svg,
			'25.5'
		);

		// Sub menu pages
		$submenu_pages = array(
			array(
				'wi-volunteer-management',
				'',
				__( 'Volunteers', 'wivm' ),
				'manage_options',
				'wi-volunteer-management-volunteers',
				array( $this, 'load_page' ),
			),
			array(
				'wi-volunteer-management',
				'',
				__( 'Opportunities', 'wivm' ),
				'manage_options',
				'wi-volunteer-management-opportunities',
				array( $this, 'load_page' ),
			),
			array(
				'wi-volunteer-management',
				'',
				__( 'Settings', 'wivm' ),
				'manage_options',
				'wi-volunteer-management-settings',
				array( $this, 'load_page' ),
			),
		);

		// Allow submenu pages manipulation
		$submenu_pages = apply_filters( 'wivm_submenu_pages', $submenu_pages );

		// Loop through submenu pages and add them
		if ( count( $submenu_pages ) ) {
			foreach ( $submenu_pages as $submenu_page ) {
				// Add submenu page
				add_submenu_page( $submenu_page[0], $submenu_page[2] . ' - ' . __( 'Wired Impact Volunteer Management', 'wivm' ), $submenu_page[2], $submenu_page[3], $submenu_page[4], $submenu_page[5] );
			}
		}

		//Change the submenu name for the 1st item
		global $submenu;
		if ( isset( $submenu['wi-volunteer-management'] ) && current_user_can( 'manage_options' ) ) {
			$submenu['wi-volunteer-management'][0][0] = __( 'Dashboard', 'wivm' );
		}
	}

	/**
	 * Load the appropriate admin page
	 */
	function load_page() {
		$page = filter_input( INPUT_GET, 'page' );

		switch ( $page ) {
			case 'wi-volunteer-management-volunteers':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/volunteer.php';
				break;

			case 'wi-volunteer-management-opportunities':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/opportunities.php';
				break;

			case 'wi-volunteer-management-settings':
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/settings.php';
				break;

			default:
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/dashboard.php';
				break;
		}
	}

	/**
	 * Register our settings with the WordPress Settings API
	 * @see  https://kovshenin.com/2012/the-wordpress-settings-api/ Article on how to use the Settings API
	 */
	function register_settings(){
		register_setting( 'wivm-settings-group', 'wivm-settings' );
		//add_settings_field( 'general-setting-1', 'General Setting 1 Title', array( $this, 'setting_1_callback_function' ), 'wi-volunteer-management' );
	}

	/**
	 * Output our general setting 1 field.
	 */
	function setting_1_callback_function(){
		$setting = esc_attr( get_option( 'wivm-settings' ) );
    	echo "<input type='text' name='wivm-settings' value='$setting' />";
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

}
