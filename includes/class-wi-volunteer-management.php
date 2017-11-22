<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
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
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      WI_Volunteer_Management_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1
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
	 * @since    0.1
	 */
	public function __construct() {

		$this->plugin_name = 'wired-impact-volunteer-management';
		$this->version = '1.3.7';

		$this->load_dependencies();
		$this->set_locale();

		// Quick admin check and load if needed
		if ( is_admin() ) {
			$this->define_admin_hooks();
		}

		// Load the public hooks
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
	 * @since    0.1
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
		 * The class responsible for handling template loading functionality
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
		 * The class responsible for dealing with emails sent to volunteers and admins.
		 */
		require_once WIVM_DIR . 'includes/class-email.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		if ( is_admin() ) {
			require_once WIVM_DIR . 'admin/class-admin.php';
		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WIVM_DIR . 'frontend/class-public.php';

      /**
       * The class responsible for defining all actions associated with the widget portion of the plugin.
       */
      require_once WIVM_DIR . 'widget/class-widget.php';

		$this->loader = new WI_Volunteer_Management_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WI_Volunteer_Management_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WI_Volunteer_Management_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action(   'plugins_loaded',                             $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WI_Volunteer_Management_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action(   'plugins_loaded',                             $plugin_admin, 'do_upgrades' );
		$this->loader->add_action(   'admin_enqueue_scripts',                      $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action(   'admin_enqueue_scripts',                      $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action(   'admin_menu',                                 $plugin_admin, 'do_menu_changes' );
		$this->loader->add_action(   'admin_init',                                 $plugin_admin, 'register_settings' );
		$this->loader->add_action(   'edit_form_after_editor',                     $plugin_admin, 'show_opp_editor_description' );
		$this->loader->add_action(   'add_meta_boxes',                             $plugin_admin, 'add_meta_boxes' );
		$this->loader->add_action(   'save_post',                                  $plugin_admin, 'save_volunteer_opp_meta', 10, 2 );
		$this->loader->add_action(   'show_user_profile',                          $plugin_admin, 'show_extra_profile_fields' );
		$this->loader->add_action(   'edit_user_profile',                          $plugin_admin, 'show_extra_profile_fields' );
		$this->loader->add_action(   'personal_options_update',                    $plugin_admin, 'save_extra_profile_fields' );
		$this->loader->add_action(   'edit_user_profile_update',                   $plugin_admin, 'save_extra_profile_fields' );
		$this->loader->add_filter(   'manage_edit-volunteer_opp_columns',          $plugin_admin, 'manage_opp_columns' );
		$this->loader->add_filter(   'manage_edit-volunteer_opp_sortable_columns', $plugin_admin, 'sort_opp_columns' );
		$this->loader->add_action(   'manage_volunteer_opp_posts_custom_column',   $plugin_admin, 'show_opp_columns', 10, 2 );
		$this->loader->add_filter(   'parse_query',                                $plugin_admin, 'edit_opps_query' );
		$this->loader->add_action(   'views_edit-volunteer_opp',                   $plugin_admin, 'set_opp_views' );
		$this->loader->add_action(   'load-edit.php',                              $plugin_admin, 'load_opp_sort' );
		$this->loader->add_action(   'wp_ajax_wivm_remove_rsvp',                   $plugin_admin, 'remove_user_opp_rsvp' );
		$this->loader->add_action(   'save_post',                                  $plugin_admin, 'schedule_auto_email_reminder', 99, 2 );
		$this->loader->add_action(   'delete_user',                                $plugin_admin, 'delete_volunteer_rsvps', 10, 2 );
		$this->loader->add_action(   'admin_notices',                              $plugin_admin, 'show_getting_started_notice' );
		$this->loader->add_action(   'wp_ajax_wivm_hide_notice',                   $plugin_admin, 'hide_notice' );
		$this->loader->add_action(   'wp_ajax_wivm_process_email',                 $plugin_admin, 'process_custom_volunteer_email' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality and that are used in both public-facing and the admin
	 * of the plugin.
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WI_Volunteer_Management_Public( $this->get_plugin_name(), $this->get_version() );
      $plugin_widget = new WI_Volunteer_Management_Widget( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action(      'wp_enqueue_scripts',            $plugin_public, 'enqueue_styles' );
		$this->loader->add_action(      'wp_head',            			 $plugin_public, 'enqueue_honeypot_styles' );
		$this->loader->add_action(      'wp_enqueue_scripts',            $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action(      'init',                          $plugin_public, 'register_post_types' );
		$this->loader->add_shortcode(   'one_time_volunteer_opps',       $plugin_public, 'display_one_time_volunteer_opps' );
		$this->loader->add_shortcode(   'flexible_volunteer_opps',       $plugin_public, 'display_flexible_volunteer_opps' );
		$this->loader->add_filter(      'wp_trim_words',                 $plugin_public, 'always_show_read_more' );
		$this->loader->add_filter(      'excerpt_more',                  $plugin_public, 'hide_default_read_more', 11 );
		$this->loader->add_filter(      'the_content',                   $plugin_public, 'show_meta_form_single' );
		$this->loader->add_action(      'wp_ajax_wivm_sign_up',          $plugin_public, 'process_volunteer_sign_up' );
		$this->loader->add_action(      'wp_ajax_nopriv_wivm_sign_up',   $plugin_public, 'process_volunteer_sign_up' );
		$this->loader->add_action(   	'send_auto_email_reminders',     $plugin_public, 'send_email_reminder' );
      $this->loader->add_action(      'widgets_init',                  $plugin_widget, 'register_widget' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1
	 * @return    WI_Volunteer_management_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

} //class WI_Volunteer_Management