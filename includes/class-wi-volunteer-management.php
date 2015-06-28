<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 * @author     Your Name <email@example.com>
 */
class WI_Volunteer_Management {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WI_Volunteer_Management_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wivm';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WI_Volunteer_Management_Loader. Orchestrates the hooks of the plugin.
	 * - WI_Volunteer_Management_i18n. Defines internationalization functionality.
	 * - WI_Volunteer_Management_Admin. Defines all hooks for the admin area.
	 * - WI_Volunteer_Management_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once WIVM_DIR . 'includes/class-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WIVM_DIR . 'includes/class-i18n.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WIVM_DIR . 'includes/class-template-loader.php';

		/**
		 * The class responsible for retrieving the options for our plugin.
		 */
		require_once WIVM_DIR . 'includes/class-options.php';

		/**
		 * The class responsible for dealing with individual opportunities.
		 */
		require_once WIVM_DIR . 'includes/class-opportunity.php';

		/**
		 * The class responsible for dealing with individual volunteers.
		 */
		require_once WIVM_DIR . 'includes/class-volunteer.php';

		/**
		 * The class responsible for dealing with RSVPs to volunteer opportunities.
		 */
		require_once WIVM_DIR . 'includes/class-rsvp.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once WIVM_DIR . 'admin/class-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WIVM_DIR . 'frontend/class-public.php';

		$this->loader = new WI_Volunteer_Management_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Plugin_Name_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 		'plugins_loaded', 			$plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WI_Volunteer_Management_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 		'admin_enqueue_scripts', 	$plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 		'admin_enqueue_scripts', 	$plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 		'admin_menu', 				$plugin_admin, 'register_settings_page' );
		$this->loader->add_action( 		'admin_init', 				$plugin_admin, 'register_settings' );
		$this->loader->add_action( 		'add_meta_boxes', 			$plugin_admin, 'add_meta_boxes' );
		$this->loader->add_action( 		'save_post', 				$plugin_admin, 'save_volunteer_opp_meta', 10, 2 );
		$this->loader->add_action(		'show_user_profile', 		$plugin_admin, 'show_extra_profile_fields' );
		$this->loader->add_action(		'edit_user_profile', 		$plugin_admin, 'show_extra_profile_fields' );
		$this->loader->add_action(		'personal_options_update', 	$plugin_admin, 'save_extra_profile_fields' );
		$this->loader->add_action(		'edit_user_profile_update', $plugin_admin, 'save_extra_profile_fields' );
		$this->loader->add_action( 		'wp_ajax_wivm_remove_rsvp',	$plugin_admin, 'remove_user_opp_rsvp' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality and that are used in both public-facing and the admin
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WI_Volunteer_Management_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 		'wp_enqueue_scripts', 			$plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 		'wp_enqueue_scripts', 			$plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 		'init', 						$plugin_public, 'register_post_types' );
		$this->loader->add_shortcode( 	'one_time_volunteer_opps', 		$plugin_public, 'display_one_time_volunteer_opps' );
		$this->loader->add_shortcode( 	'flexible_volunteer_opps', 		$plugin_public, 'display_flexible_volunteer_opps' );
		$this->loader->add_filter( 		'single_template', 				$plugin_public, 'get_single_opp_template' );
		$this->loader->add_action( 		'wp_ajax_wivm_sign_up', 		$plugin_public, 'process_volunteer_sign_up' );
 		$this->loader->add_action( 		'wp_ajax_nopriv_wivm_sign_up', 	$plugin_public, 'remove_user_opp_rsvp' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	
} //class WI_Volunteer_Management