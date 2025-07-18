<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Public
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      0.1
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * Only load the frontend CSS if the setting is turned on to do so.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {

		$options = new WI_Volunteer_Management_Options();

		if ( $options->get_option( 'use_css' ) == 1 ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wi-volunteer-management-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Hide the honeypot field for the volunteer sign up form.
	 *
	 * We load this CSS separately to be sure the field is hidden even if the
	 * admin has turned off loading the CSS within the settings.
	 */
	public function enqueue_honeypot_styles(){

		if ( is_singular( 'volunteer_opp' ) ) : ?>

			<style>
				/* Hide the Wired Impact Volunteer Management honeypot field under all circumstances */
				.wivm_hp {
					display: none !important;
					position: absolute !important;
					left: -9000px;
				}
			</style>

			<?php
		endif;
	}

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    0.1
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
	      'name' 				=> __( 'Volunteer Opportunities', 'wired-impact-volunteer-management' ),
	      'singular_name' 		=> __( 'Volunteer Opportunity', 'wired-impact-volunteer-management' ),
	      'add_new' 			=> __( 'Add Volunteer Opportunity', 'wired-impact-volunteer-management' ),
	      'add_new_item' 		=> __( 'Add Volunteer Opportunity', 'wired-impact-volunteer-management' ),
	      'edit_item' 			=> __( 'Edit Volunteer Opportunity', 'wired-impact-volunteer-management' ),
	      'new_item' 			=> __( 'New Volunteer Opportunity', 'wired-impact-volunteer-management' ),
	      'all_items' 			=> __( 'All Volunteer Opportunities', 'wired-impact-volunteer-management' ),
	      'view_item' 			=> __( 'View Volunteer Opportunity', 'wired-impact-volunteer-management' ),
	      'search_items' 		=> __( 'Search Volunteer Opportunities', 'wired-impact-volunteer-management' ),
	      'not_found' 			=> __( 'No volunteer opportunities found', 'wired-impact-volunteer-management' ),
	      'not_found_in_trash' 	=> __( 'No volunteer opportunities found in trash', 'wired-impact-volunteer-management' ),
	      'parent_item_colon' 	=> '',
	      'menu_name' 			=> __( 'Volunteer Mgmt', 'wired-impact-volunteer-management' )
	    );

	    $args = array(
	      'labels'            => $labels,
	      'public'            => true,
	      'show_ui'           => true,
	      'show_in_menu'      => 'wi-volunteer-management',
	      'menu_icon'         => 'dashicons-groups',
	      'capability_type'   => 'post',
	      'supports'          => array( 'title', 'editor', 'thumbnail', 'revisions'  ),
	      'rewrite'           => array( 'slug' => apply_filters( 'wivm_opp_rewrite', 'volunteer-opportunity' ), 'with_front' => false ),
	      'show_in_rest'      => true,
	    );

	    register_post_type( 'volunteer_opp', $args );
	}

	/**
	 * Register our volunteer opportunities block.
	 */
	public function volunteer_opps_register_block(){

		global $pagenow;

		// Skip block registration if Gutenberg is not enabled or the user is on the Widgets.php admin page.
		if ( ! function_exists( 'register_block_type' ) || $pagenow === 'widgets.php' ) {

			return;
		}

		wp_register_script(
			'wired-impact-volunteer-management-block',
			plugin_dir_url( __DIR__ ) . 'admin/js/wi_volunteer_management_block.bundle.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-dom-ready',
				'wp-edit-post',
			),
			$this->version,
			false
		);

		register_block_type(
			plugin_dir_path( __DIR__ ) . 'admin/block/',
			array(
				'render_callback' => array( $this, 'display_volunteer_opps_block' ),
			)
		);
	}

	/**
	 * Display our volunteer opportunities block in the admin or
	 * on the frontend of the website. AJAX is used to load the
	 * volunteer opportunities in the admin.
	 *
	 * @see    https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/creating-dynamic-blocks/
	 * @param  array $attributes        Attributes saved from the block editor to display in the admin or frontend.
	 * @return string                   The final HTML for our volunteer opportunities.
	 */
	public function display_volunteer_opps_block( $attributes ) {

		if ( $attributes['showOneTime'] === true ) {
			return $this->display_one_time_volunteer_opps( $attributes );
		}

		return $this->display_flexible_volunteer_opps( $attributes );
	}

	/**
	 * Shortcode for viewing all one-time volunteer opportunities.
	 *
	 * @param array $attributes  Attributes saved from the block editor or passed as shortcode parameters.
	 */
	public function display_one_time_volunteer_opps( $attributes ) {

		$paged = $this->get_current_page_number();
		$args  = array(
			'paged'      => $paged,
			'post_type'  => 'volunteer_opp',
			'orderby'    => 'meta_value_num',
			'order'      => 'ASC',
			'meta_key'   => '_start_date_time',
			'meta_query' => array(
				array( // Only if one-time opp is true.
					'key'     => '_one_time_opp',
					'value'   => 1,
					'compare' => '==',
				),
				array( // Only if event is in the future.
					'key'     => '_start_date_time',
					'value'   => current_time( 'timestamp' ),
					'compare' => '>=',
				),
				'relation' => 'AND',
			),
		);

		return $this->display_volunteer_opp_list( 'one-time', apply_filters( $this->plugin_name . '_one_time_opp_shortcode_query', $args ), $attributes );
	}

	/**
	 * Shortcode for viewing all flexible volunteer opportunities.
	 *
	 * @param array $attributes  Attributes saved from the block editor or passed as shortcode parameters.
	 */
	public function display_flexible_volunteer_opps( $attributes ) {

		$paged = $this->get_current_page_number();
		$args  = array(
			'paged'      => $paged,
			'post_type'  => 'volunteer_opp',
			'meta_query' => array(
				array( // Only if one-time opp is not true.
					'key'     => '_one_time_opp',
					'value'   => 1,
					'compare' => '!=',
				),
			),
		);

		return $this->display_volunteer_opp_list( 'flexible', apply_filters( $this->plugin_name . '_flexible_opp_shortcode_query', $args ), $attributes );
	}

	/**
	 * Get the current page number when listing volunteer opportunities.
	 *
	 * The query var is almost always "paged", but for static front pages
	 * it's "page". If neither of those are set, then default to 1.
	 *
	 * @return int The current page number.
	 */
	private function get_current_page_number() {

		$paged = absint( get_query_var( 'paged' ) );

		if ( $paged > 0 ) {

			return $paged;
		}

		$paged = absint( get_query_var( 'page' ) );

		if ( $paged > 0 ) {

			return $paged;
		}

		return 1;
	}

	/**
	 * Always show read more text on the list of opportunities even if there isn't enough content.
	 *
	 * We do this by displaying '' as the default read more then adding our own read more to the end
	 * of the content.
	 *
	 * @see    hide_default_read_more()
	 * @param  string $text  The shortened text that makes up the excerpt.
	 * @return string        The final excerpt with the read more link included.
	 */
	public function always_show_read_more( $text ){

		if( get_post_type() == 'volunteer_opp' && !is_single() ){

			$more_text = __( 'Find Out More', 'wired-impact-volunteer-management' );
			$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
						get_permalink( get_the_ID() ),
						apply_filters( 'wivm_read_more_text', $more_text )
					);

			return $text . '&hellip;' . $link;
		}

		return $text;
	}

	/**
	 * Hide the default read more text that shows when content is longer than provided number of words.
	 *
	 * We hide this since we provide our own read more text to every item in the list instead of only
	 * the longer ones.
	 *
	 * @see    always_show_read_more()
	 * @param  string $more_text Default read more text.
	 * @return string            Empty string if volunteer opportunity or default read more if not.
	 */
	public function hide_default_read_more( $more_text ){

		if( get_post_type() == 'volunteer_opp' && !is_single() ){
			return '';
		}

		return $more_text;
	}

	/**
	 * Displays the volunteer opportunities lists.
	 *
	 * Displays the volunteer opportunities lists for both the one-time and flexible
	 * opportunities. It also calls template files to output the majority of the HTML.
	 *
	 * @param  string $list_type One-time or flexible volunteer opportunities.
	 * @param  array  $query_args The query arguments to be used in WP_Query.
	 * @param array  $attributes  Attributes saved from the block editor or passed as shortcode parameters.
	 * @return string            HTML code to be output via a shortcode.
	 */
	public function display_volunteer_opp_list( $list_type, $query_args, $attributes ) {
		// We must edit the main query in order to handle pagination.
		global $wp_query;
		$temp = $wp_query;
		$wp_query = new WP_Query( $query_args );

		ob_start();

		/**
		 * If the Additonal CSS class(es) field on the block is used, or className is passed
		 * as a shortcode parameter, include it in the container class name.
		 */
		$class_name = ( empty( $attributes['className'] ) ) ? $list_type : $list_type . ' ' . $attributes['className'];

		/**
		 * If the HTML anchor field on the block is used, or anchor is passed
		 * as a shortcode parameter, use it for the list's container ID.
		 */
		$anchor = ( empty( $attributes['anchor'] ) ) ? '' : ' id="' . esc_attr( $attributes['anchor'] ) . '"';
		?>

		<div class="volunteer-opps <?php echo esc_attr( $class_name ); ?>"<?php echo $anchor; ?>>

			<?php
			$template_loader = new WI_Volunteer_Management_Template_Loader();
			if( $wp_query->have_posts() ){

				while( $wp_query->have_posts() ){
					$wp_query->the_post();
					$template_loader->get_template_part( 'opps-list', $list_type );
				}

			}
			else { ?>

				<p class="no-opps"><?php _e( 'Sorry, there are no volunteer opportunities available right now.', 'wired-impact-volunteer-management' ); ?></p>

			<?php }

			echo $this->get_page_navigation(); ?>

		</div><!-- .volunteer-opps -->

		<?php
		//Reset to default query
		$wp_query = null;
  		$wp_query = $temp;
  		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Get the page navigation when displaying a list of volunteer opportunities.
	 *
	 * We overwrite the global $paged variable to set the page number since
	 * previous_posts_link() and next_posts_link() don't work correctly
	 * on static front pages. See our get_current_page_number() method
	 * for more information.
	 *
	 * We provide a filter so custom navigation can be utilized in place of the
	 * default WordPress functionality.
	 *
	 * @return string The HTML for the page navigation.
	 */
	public function get_page_navigation() {

		global $paged;
		$original_paged = $paged;
		$paged          = $this->get_current_page_number();

		ob_start();
		?>

		<div class="navigation volunteer-opps-navigation">
			<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Opportunities', 'wired-impact-volunteer-management' ) ); ?></div>
			<div class="alignright"><?php next_posts_link( __( 'More Opportunities &raquo;', 'wired-impact-volunteer-management' ) ); ?></div>
		</div>

		<?php
		$paged = $original_paged;

		return apply_filters( 'wivm_page_navigation', ob_get_clean() );
	}

	/**
	 * Output the meta before the content and the form after the content
	 * on a single volunteer opp.
	 *
	 * Display this info using a filter for the_content to ensure the templates will work
	 * on a number of different themes. The opp-single-meta.php and opp-single-form.php
	 * templates are used within this function.
	 *
	 * @param  string $content The content for the given post.
	 * @return string If volunteer opp then output the meta before and the form after the post's content.
	 */
	public function show_meta_form_single( $content ) {

		if ( ! is_singular( 'volunteer_opp' ) || ! in_the_loop() || ! is_main_query() ) {

			return $content;
		}

		$template_loader = new WI_Volunteer_Management_Template_Loader();
		ob_start();

		$template_loader->get_template_part( 'opp-single', 'meta' );

		echo $content;

		$this->show_volunteer_sign_up_form( $template_loader );

		return ob_get_clean();
	}

	/**
	 * Output the volunteer sign up form based on the chosen settings.
	 *
	 * This may require showing the built-in sign up form, no form at all,
	 * or a form from a third-party tool. For third-party tools, they can
	 * display their own form using the provided hook.
	 *
	 * @param object $template_loader The template loader object.
	 */
	private function show_volunteer_sign_up_form( $template_loader ) {

		$volunteer_opp = new WI_Volunteer_Management_Opportunity( get_the_ID() );

		if ( $volunteer_opp->opp_meta['form_type'] === 'built_in_form' ) {

			$template_loader->get_template_part( 'opp-single', 'form' );

		} elseif ( $volunteer_opp->opp_meta['form_type'] === 'no_form' ) {

			// Output nothing since the admin has chosen to not show the form.

		} else {

			do_action( 'wivm_show_volunteer_sign_up_form', $volunteer_opp );
		}
	}

	/**
	 * Process the AJAX request when a volunteer signs up for an opportunity
	 * using the built-in form.
	 *
	 * @return void This method echoes the response back to the user, then dies.
	 */
	public function process_builtin_form_volunteer_sign_up() {

		$form_data = array();
		parse_str( $_POST['data'], $form_data );

		// Verify our nonce.
		if ( ! wp_verify_nonce( $form_data['wivm_sign_up_form_nonce_field'], 'wivm_sign_up_form_nonce' ) ) {

			_e( 'Security Check.', 'wired-impact-volunteer-management' );
			die();
		}

		// If the honeypot field exists and is filled out then bail.
		if ( isset( $form_data['wivm_hp'] ) && $form_data['wivm_hp'] != '' ) {

			_e( 'Security Check.', 'wired-impact-volunteer-management' );
			die();
		}

		$result = self::process_volunteer_sign_up( $form_data );

		echo $result;

		die(); // Must use die() when using AJAX.
	}

	/**
	 * Process a volunteer signup.
	 *
	 * This includes updating the volunteer's information and RSVPing
	 * them for the opportunity.
	 *
	 * This is called both when a volunteer signs up using the built-in form
	 * and when a volunteer signs up using a third-party form.
	 *
	 * @param array $form_data The form fields data submitted by the volunteer.
	 * @return string The coded result of the signup process.
	 */
	public static function process_volunteer_sign_up( $form_data ) {

		$opp = new WI_Volunteer_Management_Opportunity( $form_data['wivm_opportunity_id'] );

		// If the opportunity is allowing RSVPs.
		if ( $opp->should_allow_rvsps() === true ) {

			// Add or update the new volunteer user.
			$user = new WI_Volunteer_Management_Volunteer( null, $form_data );

			// RSVP this volunteer for the opportunity.
			$rsvp = new WI_Volunteer_Management_RSVP( $user->ID, $form_data['wivm_opportunity_id'] );

			// If the person hadn't already RSVPed then send out the signup emails.
			if ( $rsvp->rsvped === true ) {

				$email = new WI_Volunteer_Management_Email( $opp, $user );

				$email->maybe_send_volunteer_signup_email();
				$email->maybe_send_admin_signup_email();
				$result = 'rsvped';

			} else {

				$result = 'already_rsvped';
			}
		} else { // If RSVPs have been closed, typically if no more spots are available.

			$result = 'rsvp_closed';
		}

		// Return a coded result which tells us what messages to show on the frontend.
		return $result;
	}

	/**
	 * Send volunteer reminder email and store it in the database.
	 *
	 * This method is called using cron and is never called in any other way. This
	 * method must be provided in the public class since the admin class is not
	 * loaded when cron is run.
	 *
	 * @param  int $opp_id Volunteer opportunity ID.
	 */
	public function maybe_send_email_reminder( $opp_id ) {

		$options = new WI_Volunteer_Management_Options();

		// Don't send if the setting to send reminder emails to volunteers is turned off.
		if ( (int) $options->get_option( 'send_reminder_email_to_volunteers' ) !== 1 ) {

			return;
		}

		$opp                         = new WI_Volunteer_Management_Opportunity( $opp_id );
		$reminder_email_already_sent = $this->has_reminder_email_already_sent( $opp );

		if ( $reminder_email_already_sent === true ) {

			return false;
		}

		$data_array = array(
			'post_id' => $opp_id,
			'user_id' => 0,
		);

		$email = new WI_Volunteer_Management_Email( $opp );
		$email->send_volunteer_reminder_email();
		$email->store_volunteer_email( $data_array );
	}

	/**
	 * Check if a reminder email has already been sent by the system within the last 5 hours.
	 *
	 * While maybe_send_email_reminder() should only run one time through cron, in certain caching
	 * situations the same cron event was run multiple times, which triggered the automated
	 * reminder email to send multiple times. Since the get_rsvp_emails() method uses
	 * $wpdb->get_results() which is not cached, this method should prevent the reminder
	 * email from being sent multiple times.
	 *
	 * @param  object  $opp The current volunteer opportunity to check.
	 * @return boolean      Whether an automated reminder email has been sent within the last 5 hours.
	 */
	public function has_reminder_email_already_sent( $opp ){

		$current_time 				= current_time( 'timestamp' );
		$five_hours_in_seconds 		= HOUR_IN_SECONDS * 5; // 18,000 seconds
		$five_hours_ago_in_seconds 	= $current_time - $five_hours_in_seconds;
		$emails_sent				= $opp->get_rsvp_emails();

		foreach( $emails_sent as $email ){

			$email_sent_time_in_seconds = strtotime( $email->time );

			// $email->user_id === 0 means this is an automated reminder email
			if( $email->user_id === '0' && $email_sent_time_in_seconds >= $five_hours_ago_in_seconds ){
				return true;
			}
		}

		return false;
	}
} //class WI_Volunteer_Management_Public