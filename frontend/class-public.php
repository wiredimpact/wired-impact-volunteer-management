<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/public
 * @author     Your Name <email@example.com>
 */
class WI_Volunteer_Management_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WI_Volunteer_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WI_Volunteer_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wi-volunteer-management-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WI_Volunteer_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WI_Volunteer_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wi-volunteer-management-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register our Volunteer Opportunities post type.
	 *
	 * Register our Volunteer Opportunities post type and set the method to static so that 
	 * it can be called during activation when we need to refresh the rewrite rules.
	 */
	public static function register_post_types(){

		$labels = array(
	      'name' =>               'Volunteer Opportunities',
	      'singular_name' =>      'Volunteer Opportunity',
	      'add_new' =>            'Add Volunteer Opportunity',
	      'add_new_item' =>       'Add Volunteer Opportunity',
	      'edit_item' =>          'Edit Volunteer Opportunity',
	      'new_item' =>           'New Volunteer Opportunity',
	      'all_items' =>          'All Volunteer Opportunities',
	      'view_item' =>          'View Volunteer Opportunity',
	      'search_items' =>       'Search Volunteer Opportunities',
	      'not_found' =>          'No volunteer opportunities found',
	      'not_found_in_trash' => 'No volunteer opportunities found in trash', 
	      'parent_item_colon' =>  '',
	      'menu_name' =>          'Volunteer Mgmt',
	    );

	    $args = array(
	      'labels'            => $labels,
	      'public'            => true,
	      'show_ui'           => true,
	      'show_in_menu'      => 'wi-volunteer-management',
	      'menu_icon'         => 'dashicons-groups',
	      'capability_type'   => 'post',
	      'supports'          => array( 'title', 'editor', 'thumbnail', 'revisions'  ),
	      'rewrite'           => array( 'slug' => 'volunteer-opportunity', 'with_front' => false )
	    ); 
	    
	    register_post_type( 'volunteer_opp', $args );
	}

}
