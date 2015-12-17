<?php
/**
 * This template is used to display the list of flexible volunteer opportunities
 * for the [flexible_volunteer_opps] shortcode.
 *
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */
$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'flexible-opp' ); ?>>

	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<div class="volunteer-opp-info">
			<?php $opp->display_meta( $opp->opp_meta['flexible_frequency'], __( 'When:', 'wired-impact-volunteer-management' ) ); ?>
			<?php $opp->display_meta( $opp->format_address(), 				__( 'Where:', 'wired-impact-volunteer-management' ) ); ?>
			<?php $opp->display_meta( $opp->get_open_volunteer_spots(), 	__( 'Open Volunteer Spots:', 'wired-impact-volunteer-management' ) ); ?>
		</div><!-- .volunteer-opp-info -->

		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->

</article><!-- .volunteer-opp flexible-opp -->