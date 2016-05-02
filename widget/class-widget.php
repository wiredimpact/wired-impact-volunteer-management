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
      __('Volunteer Management Widget', 'text_domain'), // Name
      array( 'description' => __( 'A list of volunteer opportunities.', 'text_domain' ), ) // Widget Options
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

   global $wp_query;

   // Global variables to  be used in templates/opps-list-widget.php
   global $display_opp_when;
   global $display_opp_where;
   global $display_opp_spots;
   
   $temp = $wp_query;

   isset( $instance['opp_info_when'] ) ? $display_opp_when = true : $display_opp_when = false;
   isset( $instance['opp_info_where'] ) ? $display_opp_where = true : $display_opp_where = false;
   isset( $instance['opp_info_spots'] ) ? $display_opp_spots = true: $display_opp_spots = false;

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

   }

   $wp_query = new WP_Query( $posts_args );

   $template_loader = new WI_Volunteer_Management_Template_Loader(); ?>

   <aside id="opportunities" class="widget widget_links volunteer-opp-info">

      <!-- If page slug widget option is filled out - link the opportunities title to list of all opportunities -->
      <?php isset( $instance['opps_page_slug'] ) ? _e( '<a href="' . $instance['opps_page_slug'] . '">' ) : null; ?>
         <h2 class="widget-title"><?php _e( $list_type . ' volunteer opportunities', 'wired-impact-volunteer-management'); ?></h2>
      <?php isset( $instance['opps_page_slug'] ) ? _e( '</a>' ) : null; ?>

   <?php if ( $wp_query->have_posts() ) { ?>
      <div class="widget-volunteer-opp-info">
         <ul>

      <?php while( $wp_query->have_posts() ) {
         $wp_query->the_post();
         $template_loader->get_template_part( 'opps-list', 'widget' );
      } ?>

         </ul>
      </div>

   <?php } else { ?>

      <p class="no-opps"><?php _e( 'Sorry, there are no ' . $list_type . ' volunteer opportunities available right now.', 'wired-impact-volunteer-management' ); ?></p>

   <?php } ?>

   </aside> <?php
}

/**
* Back-end widget form.
*
* @see WP_Widget::form()
*
* @param array $instance Previously saved values from database.
*/
public function form( $instance ) {

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
      <label class="wi-widget-block-label"><?php _e( 'Opportunity type to show:', 'wired-impact-volunteer-management'); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_type_radio_btn' ) ); ?>" type="radio" value="flexible" <?php if( $list_type === 'flexible' ) { echo 'checked'; }; ?>/>
      <label for="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_1' ) ); ?>"><?php _e( 'Flexible', 'wired-impact-volunteer-management' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_type_radio_btn' ) ); ?>" type="radio" value="one-time" <?php if( $list_type === 'one-time' ) { echo 'checked'; } ; ?>/>
      <label for="<?php echo esc_attr( $this->get_field_id( 'list_type_radio_btn_2' ) ); ?>"><?php _e( 'One-Time', 'wired-impact-volunteer-management' ); ?></label>
   </p>

   <p>
      <label class="wi-widget-block-label"><?php _e( 'Number of opportunites to show:', 'wired-impact-volunteer-management'); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'number_of_opps_input_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_opps_input' ) ); ?>" type="number" value="<?php echo $num_of_opps; ?>"/>
   </p>

   <p>
      <label class="wi-widget-block-label"><?php _e( 'Information to show for each opportunity (in addition to title):', 'wired-impact-volunteer-management'); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_when' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'opp_info_when' ) ); ?>" type="checkbox" value="when" <?php if( isset( $instance['opp_info_when'] ) ) { echo 'checked'; }; ?>/>
      <label for="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_when' ) ); ?>" class="wi-widget-block-label"><?php _e( 'When', 'wired-impact-volunteer-management' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_where' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'opp_info_where' ) ); ?>" type="checkbox" value="where" <?php if( isset( $instance['opp_info_where'] ) ) { echo 'checked'; }; ?>/>
      <label for="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_where' ) ); ?>" class="wi-widget-block-label"><?php _e( 'Where', 'wired-impact-volunteer-management' ); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_spots' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'opp_info_spots' ) ); ?>" type="checkbox" value="spots" <?php if( isset( $instance['opp_info_spots'] ) ) { echo 'checked'; }; ?>/>
      <label for="<?php echo esc_attr( $this->get_field_id( 'opp_info_cbx_spots' ) ); ?>" class="wi-widget-block-label"><?php _e( 'Open Volunteer Spots', 'wired-impact-volunteer-management' ); ?></label>
   </p>

   <p>
      <label class="wi-widget-block-label"><?php _e( 'Provide page slug to link to all opportunities of chosen type:', 'wired-impact-volunteer-management'); ?></label>
      <input id="<?php echo esc_attr( $this->get_field_id( 'opps_page_slug' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'opps_page_slug' ) ); ?>" type="text" value="<?php isset( $instance['opps_page_slug'] ) ? _e( $instance['opps_page_slug'] ) : null; ?>"/>
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

   // Update list type
   $instance['list_type_radio_btn'] = ( ! empty( $new_instance['list_type_radio_btn'] ) ) ? strip_tags( $new_instance['list_type_radio_btn'] ) : '';

   // Update number of opps to show - abs accounts for negative numbers and floor accounts for decimals
   $instance['number_of_opps_input'] = ( ! empty( $new_instance['number_of_opps_input'] ) ) ? floor(abs(strip_tags( $new_instance['number_of_opps_input'] ))) : '';

   // Update info to show for each opp
   $instance['opp_info_when']  = ( ! empty( $new_instance['opp_info_when'] ) ) ? strip_tags( $new_instance['opp_info_when'] ) : null;
   $instance['opp_info_where'] = ( ! empty( $new_instance['opp_info_where'] ) ) ? strip_tags( $new_instance['opp_info_where'] ) : null;
   $instance['opp_info_spots'] = ( ! empty( $new_instance['opp_info_spots'] ) ) ? strip_tags( $new_instance['opp_info_spots'] ) : null;
   $instance['opps_page_slug'] = ( ! empty( $new_instance['opps_page_slug'] ) ) ? strip_tags( $new_instance['opps_page_slug'] ) : null;

   return $instance;
}

   public static function register_widget() {
      register_widget( 'WI_Volunteer_Management_Widget' );
   }

} // class WI_Volunteer_Management_Widgets