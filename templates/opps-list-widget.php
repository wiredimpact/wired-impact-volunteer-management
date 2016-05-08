<?php
/**
 * This template is used to display the list of flexible OR one-time volunteer opportunities
 * for the widget.
 *
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */

$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information

?>

<li>
 
 <?php

   // Display title of opportunity
   the_title( sprintf( '<h3><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );

   // Display 'When' information IF option to show 'When' is checked in widget settings &&
   // opportunity is a one-time opp OR flexible opp with date filled out
   if ( $options['display_opp_when'] === true && ( $opp->opp_meta['one_time_opp'] === 1 || strlen( $opp->opp_meta['flexible_frequency'] ) > 0 ) ) {

      // IF one time-opportunity display formatted dates/times
      // ELSE we know it's a flexible opportunity so display flexible frequency
      if( $opp->opp_meta['one_time_opp'] === 1 ) {
         $opp->display_meta( $opp->format_opp_times(), __( 'When:', 'wired-impact-volunteer-management' ) );
      } else {
         $opp->display_meta( $opp->opp_meta['flexible_frequency'], __( 'When:', 'wired-impact-volunteer-management' ) );
      }

   }

   // Display 'Where' information IF option to show 'Where' is checked in widget settings &&
   // any of the Location fields are filled out
   if ( $options['display_opp_where'] === true && strlen( $opp->format_address() ) > 0 ) {
      $opp->display_meta( $opp->format_address(),           __( 'Where:', 'wired-impact-volunteer-management' ) );
   }

   // Display number of open volunteer spots IF option to show 'Open Volunteer Spots' is checked in widget settings
   if ( $options['display_opp_spots'] === true ) {
      $opp->display_meta( $opp->get_open_volunteer_spots(),    __( 'Open Volunteer Spots:', 'wired-impact-volunteer-management' ) );
   }

?>

</li>