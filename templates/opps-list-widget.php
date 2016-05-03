<?php
/**
 * This template is used to display the list of flexible OR one-time volunteer opportunities
 * for the widget.
 *
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */

// Global variables that are set in widget/class-widget.php via the widget settings in admin 
global $display_opp_excerpt;
global $display_opp_when;
global $display_opp_where;
global $display_opp_spots;

$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information

?>

<li>
 
 <?php

   // Display title of opportunity
   the_title( sprintf( '<a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' );

   // Display 'When' information IF option to show 'When' is checked in widget settings &&
   // IF one time opp OR flexible opp with date filled out
   ( $display_opp_when && ( $opp->opp_meta['one_time_opp'] || strlen( $opp->opp_meta['flexible_frequency'] ) > 0 ) ) ? $opp->display_meta( ( $opp->opp_meta['one_time_opp'] == 1 ) ? $opp->format_opp_times() : $opp->opp_meta['flexible_frequency'], __( 'When:', 'wired-impact-volunteer-management' ) ) : null;

   // Display 'Where' information IF option to show 'Where' is checked in widget settings &&
   // IF any of the Location fields are filled out
   ( $display_opp_where && strlen( $opp->format_address() ) > 0 ) ? $opp->display_meta( $opp->format_address(),           __( 'Where:', 'wired-impact-volunteer-management' ) ) : null;

   // Display number of open volunteer spots IF option to show 'Open Volunteer Spots' is checked in widget settings
   $display_opp_spots ? $opp->display_meta( $opp->get_open_volunteer_spots(),    __( 'Open Volunteer Spots:', 'wired-impact-volunteer-management' ) ) : null;

?>

</li>