<?php
/**
 * This template is used to display the list of flexible or one-time volunteer opportunities
 * for the widget.
 *
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */

$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information
?>

<li>

<?php

   // Display title of opportunity
   the_title( sprintf( '<a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' );

   // Display 'When' information if option to show 'When' is checked in widget settings and
   // opportunity is a one-time opp or flexible opp with date filled out
   if ( $options['display_opp_when'] === true ) {
  //  if ( $options['display_opp_when'] === true && ( $opp->opp_meta['one_time_opp'] === 1 || ( $opp->opp_meta['flexible_frequency'] ) === 1 ) ) {

      // If one time-opportunity display formatted dates/times
      // Else we know it's a flexible opportunity so display flexible frequency
      // if( $opp->opp_meta['one_time_opp'] === 1 ) {
         $opp->display_meta( $opp->format_opp_times( '', '', true ), __( '', 'wired-impact-volunteer-management' ) );
        //  $opp->display_meta( $opp->format_address(), 			__( 'Where:', 'wired-impact-volunteer-management' ) );
        //  $opp->display_meta( $opp->get_open_volunteer_spots(), __( 'Open Spots:', 'wired-impact-volunteer-management' ) );
      // } else {
        //  $opp->display_meta( $opp->opp_meta['flexible_frequency'], __( 'Start Date:', 'wired-impact-volunteer-management' ) );
        //  $opp->display_meta( $opp->format_opp_times( '', '', true ), __( '', 'wired-impact-volunteer-management' ) );

      // }

   }

?>

</li>