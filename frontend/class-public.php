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
	 * Only load the frontend CSS if the setting is turned on to do so.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$options = new WI_Volunteer_Management_Options();
		if( $options->get_option( 'use_css' ) == 1 ){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wi-volunteer-management-public.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wi-volunteer-management-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'wivm_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

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

	/**
	 * Shortcode for viewing all one-time volunteer opportunities.
	 */
	public function display_one_time_volunteer_opps(){
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args  = array(
			'post_type' => 'volunteer_opp',
			'meta_key' => '_start_date_time',
          	'orderby' => 'meta_value_num',
          	'order'   => 'ASC',
          	'meta_query' => array(
				array( //Only if one-time opp is true
					'key'     => '_one_time_opp',
					'value'   => 1, 
					'compare' => '==',
				),
				array( //Only if event is in the future
					'key'     => '_start_date_time',
					'value'   => current_time( 'timestamp' ), 
					'compare' => '>=',
				),
				'relation' => 'AND'
			),
			'paged' => $paged
		);

		return $this->display_volunteer_opp_list( 'one-time', apply_filters( $this->plugin_name . '_one_time_opp_shortcode_query', $args ) );		
	}

	/**
	 * Shortcode for viewing all flexible volunteer opportunities.
	 */
	public function display_flexible_volunteer_opps(){
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args = array(
			'post_type' => 'volunteer_opp',
          	'meta_query' => array(
				array( //Only if one-time opp is not true
					'key'     => '_one_time_opp',
					'value'   => 1, 
					'compare' => '!=',
				),
			),
			'paged' => $paged
		);

		return $this->display_volunteer_opp_list( 'flexible', apply_filters( $this->plugin_name . '_flexible_opp_shortcode_query', $args ) );
	}

	/**
	 * Displays the volunteer opportunities lists.
	 *
	 * Displays the volunteer opportunities lists for both the one-time and flexible
	 * opportunities. It also calls template files to output the majority of the HTML.
	 * 
	 * @param  string $list_type One-time or flexible volunteer opportunities
	 * @param  array $query_args The query arguments to be used in WP_Query 
	 * @return string            HTML code to be output via a shortcode.
	 */
	public function display_volunteer_opp_list( $list_type, $query_args ){
		//We must edit the main query in order to handle pagination.
		global $wp_query;
		$temp = $wp_query;
		$wp_query = new WP_Query( $query_args );

		ob_start(); ?>
		
		<div class="volunteer-opps <?php echo $list_type; ?>">

			<?php 
			$template_loader = new WI_Volunteer_Management_Template_Loader();
			if( $wp_query->have_posts() ){

				while( $wp_query->have_posts() ){
					$wp_query->the_post();
					$template_loader->get_template_part( 'opps-list', $list_type );
				}

				wp_reset_postdata();
			} 
			else { ?>

				<p class="no-opps"><?php _e( 'Sorry, there are no volunteer opportunities available right now.', 'wivm' ); ?></p>

			<?php } ?>

			<div class="navigation volunteer-opps-navigation">
        		<div class="alignleft"><?php previous_posts_link('&laquo; Previous Opportunities') ?></div>
        		<div class="alignright"><?php next_posts_link('More Opportunities &raquo;') ?></div>
        	</div>

		</div><!-- .volunteer-opps -->

		<?php
		//Reset to default query 
		$wp_query = null; 
  		$wp_query = $temp; 

		return ob_get_clean();
	}

	/**
	 * Loads the single volunteer opportunity template using our template loader.
	 *
	 * Instead of loading the single opportunity template from the current theme, 
	 * we load it using our template loader. 
	 *
	 * @see  https://codex.wordpress.org/Plugin_API/Filter_Reference/single_template
	 * @param  string The default template file for our custom post type.
	 * @return string The location of the correct template file.
	 */
	public function get_single_opp_template( $single_template ){
		global $post;

		if( $post->post_type == 'volunteer_opp' ){
			$template_loader = new WI_Volunteer_Management_Template_Loader();
			$single_template = $template_loader->get_template_part( 'opp-single', null, false );
		}
		
		return $single_template;
	}

	/**
	 * Process the AJAX request from the volunteer opportunity sign up form.
	 *
	 * @return  int|bool The user ID if everything worked, false otherwise
	 */
	public function process_volunteer_sign_up(){
		$form_fields = array();
		parse_str( $_POST['data'], $form_fields );

		//Verify our nonce.
		if( !wp_verify_nonce( $form_fields['wivm_sign_up_form_nonce_field'], 'wivm_sign_up_form_nonce' ) ) {
			_e( 'Security Check.', 'wivm' );
			die();
		}

		//Add or update the new volunteer user
		$user = new WI_Volunteer_Management_Volunteer( null, $form_fields );

		//RSVP this volunteer for the opportunity
		$rsvp = new WI_Volunteer_Management_RSVP( $user->ID, $form_fields['wivm_opportunity_id'] );

		//If the person hadn't already RSVPed then send out the signup emails.
		if( $rsvp->rsvped == true ){
			$opp 	= new WI_Volunteer_Management_Opportunity( $form_fields['wivm_opportunity_id'] );
			$email 	= new WI_Volunteer_Management_Email( $user, $opp );
			$email->send_volunteer_signup_email();
			$email->send_admin_signup_email();
		}

		//Return the user ID to the js or false if something broke.
 		echo $user->ID; 
 		
 		die(); //Must use die() when using AJAX
	}

} //class WI_Volunteer_Management_Public