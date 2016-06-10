<?php

// Block direct requests
if ( !defined('ABSPATH') )
   die('-1');

/**
 * Volunteer Management Widget Class
 */
class WI_Volunteer_Management_Widget extends WP_Widget {

   /**
   * Set up widget information
   */
   function __construct() {

      // WP_Widget constructor accepts Base ID (string), Name (string), Widget Options (array), Control Options (array)
      parent::__construct(
         'WI_Volunteer_Management_Widget', // Base ID
         __('Volunteer Management Opportunities', 'wired-impact-volunteer-management'), // Name
         array( 'description' => __( 'A list of volunteer opportunities.', 'wired-impact-volunteer-management' ), ) // Widget Options
      );

   }

   /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
   public function widget( $args, $instance ) {

      // Array of widget options to be used in templates/opps-list-widget.php
      $wivm_widget_options = array();
      $wivm_widget_options['display_opp_when']  = isset( $instance['opp_info_when'] ) ? true : false;

      // Store number of opps to show in $num_of_opps
      if ( isset( $instance['number_of_opps_input'] ) ) {
         $num_of_opps = (int) esc_attr( $instance['number_of_opps_input'] );
      }

      $list_type = $instance['list_type_radio_btn'];

      // If list type is flexible query for flexible opps else query for one-time opps
      if( $list_type === 'flexible' ) {

         // Query for flexible volunteer opportunities
         $posts_args = array(
            'posts_per_page' => $num_of_opps,
            'post_type' => 'volunteer_opp',
            'post_status' => 'publish',
            'meta_query' => array(
               array(
               'key' => '_one_time_opp',
               'value' => 1,
               'compare' => '!='
               )
            )
         );

         // Set default title if empty
         $instance['title'] = empty( $instance['title'] ) ? 'Flexible Volunteer Opportunities' : $instance['title'];

         // Get URL of page that [flexible_volunteer_opps] shortcode was used
         $all_opps_page_link = $this->get_all_opps_link('[flexible_volunteer_opps]');

      } else {

         // Query for one-time volunteer opportunities
         $posts_args = array(
            'posts_per_page' => $num_of_opps,
            'post_type' => 'volunteer_opp',
            'post_status' => 'publish',
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
            )
         );

         // Set default title if empty
         $instance['title'] = empty( $instance['title'] ) ? 'One-Time Volunteer Opportunities' : $instance['title'];

         // Get URL of page that [one_time_volunteer_opps] shortcode was used
         $all_opps_page_link = $this->get_all_opps_link('[one_time_volunteer_opps]');

      }

      $opps_query = new WP_Query( $posts_args );

      $template_loader = new WI_Volunteer_Management_Template_Loader(); ?>

      <!-- Title of widget on front-end is pulled from the title field in the admin -->
      <?php

      echo $args['before_widget'];

      if ( ! empty( $instance['title'] ) ) {

         if ( $all_opps_page_link !== false ) { ?>
            <a href="<?php echo $all_opps_page_link; ?>"> <?php
         }
         
         echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
         
         if ( $all_opps_page_link ) { ?>
            </a> <?php
         }
      }

      if ( $opps_query->have_posts() ) { ?>
         <ul>

         <?php while( $opps_query->have_posts() ) {
            $opps_query->the_post();
            $template_loader->get_template_part( 'opps-list', 'widget', true, $wivm_widget_options );
         } ?>

         </ul>

         <?php if( $all_opps_page_link !== false ) { ?>

            <p><a href="<?php echo $all_opps_page_link; ?>">View All</a></p>

         <?php } 

      } else { ?>

         <p class="no-opps"><?php _e( 'Sorry, there are no ' . $list_type . ' volunteer opportunities available right now.', 'wired-impact-volunteer-management' ); ?></p>

      <?php }

      echo $args['after_widget'];

      /* Restore original Post Data 
       * NB: Because we are using new WP_Query we aren't stomping on the 
       * original $wp_query and it does not need to be reset with 
       * wp_reset_query(). We just need to set the post data back up with
       * wp_reset_postdata().
       */
      wp_reset_postdata();

   }

   /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
   public function form( $instance ) {

      // Default title to 'Volunteer Opportunities'
      $title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'wired-impact-volunteer-management' );

      // Default radio button to 'flexible' opportunities or set to selection
      if ( isset( $instance[ 'list_type_radio_btn' ] ) ) {
         $list_type = esc_attr( $instance['list_type_radio_btn'] );
      } else {
         $list_type = 'flexible';
      }

      // Default number input to 1 or set to input
      if ( isset( $instance['number_of_opps_input'] ) && strlen( $instance['number_of_opps_input'] ) > 0 ) {
         $num_of_opps = esc_attr( $instance['number_of_opps_input'] );
      } else {
         $num_of_opps = 1;
      }   

      ?>

      <!-- Markup for widget options in admin -->
      <p>
         <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
      </p>
      <p>
         <label class="wi-widget-block-label"><?php _e( 'Opportunity type to show:', 'wired-impact-volunteer-management'); ?></label>
         <input id="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_type_radio_btn' ) ); ?>" type="radio" value="flexible" <?php if( $list_type === 'flexible' ) { echo 'checked'; }; ?>/>
         <label for="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_1' ) ); ?>" class="wi-widget-block-label"><?php _e( 'Flexible', 'wired-impact-volunteer-management' ); ?></label>
         <input id="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_type_radio_btn' ) ); ?>" type="radio" value="one-time" <?php if( $list_type === 'one-time' ) { echo 'checked'; } ; ?>/>
         <label for="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_2' ) ); ?>"><?php _e( 'One-Time', 'wired-impact-volunteer-management' ); ?></label>
      </p>

      <p>
         <label class="wi-widget-block-label"><?php _e( 'Number of opportunites to show:', 'wired-impact-volunteer-management'); ?></label>
         <input id="<?php echo esc_attr( $this->get_field_id( 'number_of_opps_input_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_opps_input' ) ); ?>" type="number" value="<?php echo $num_of_opps; ?>"/>
      </p>

      <p>
         <input id="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_when' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'opp_info_when' ) ); ?>" type="checkbox" value="when" <?php if( ! isset( $instance['opp_info_when'] ) || $instance['opp_info_when'] === 'when' ) { echo 'checked'; }; ?>/>
         <label for="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_when' ) ); ?>" class="wi-widget-block-label"><?php _e( 'Show when the opportunity occurs', 'wired-impact-volunteer-management' ); ?></label>
      </p>


      <?php
   }

   /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
   public function update( $new_instance, $old_instance ) {

      $instance = array();

      // Update title
      $instance['title'] = ( ! empty( $new_instance['title'] ) && ! ctype_space( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

      // Update list type
      $instance['list_type_radio_btn'] = ( ! empty( $new_instance['list_type_radio_btn'] ) ) ? strip_tags( $new_instance['list_type_radio_btn'] ) : '';

      // Update number of opps to show - abs accounts for negative numbers and floor accounts for decimals
      $instance['number_of_opps_input'] = ( ! empty( $new_instance['number_of_opps_input'] ) ) ? floor(abs(strip_tags( $new_instance['number_of_opps_input'] ))) : '';

      // Update info to show for each opp
      $instance['opp_info_when']  = ( ! empty( $new_instance['opp_info_when'] ) ) ? strip_tags( $new_instance['opp_info_when'] ) : false;

      return $instance;
   }

   public function get_all_opps_link( $shortcode ) {

      global $wpdb;

      // Get id of post that shortcode was used
      $all_opps_post_id = $wpdb->get_var( 'SELECT ID FROM ' . $wpdb->base_prefix . 'posts WHERE post_content LIKE "%' . $shortcode . '%" AND post_status = "publish"');

      // Get page from id
      if( ! is_null( $all_opps_post_id ) ) {
         return  get_permalink( $all_opps_post_id );
      }

      return false;


   }

   /**
    * Register our Volunteer Management widget.
    */
   public static function register_widget() {
      register_widget( 'WI_Volunteer_Management_Widget' );
   }

} // class WI_Volunteer_Management_Widgets