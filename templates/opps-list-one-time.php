<?php
/**
 * This template is used to display the list of one-time volunteer opportunities
 * for the [one_time_volunteer_opps] shortcode.
 *
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */
$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'one-time-opp' ); ?>>

	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<div class="volunteer-opp-info">
			<span><strong><?php echo __( 'When: ', 'wivm' ); ?></strong> <?php echo $opp->format_opp_times(); ?></span>
			<span><strong><?php echo __( 'Where: ', 'wivm' ); ?></strong> <?php echo $opp->format_address(); ?></span>
			<span><strong><?php echo __( 'Open Volunteer Spots: ', 'wivm' ); ?></strong> <?php echo $opp->get_open_volunteer_spots(); ?></span>
		</div><!-- .volunteer-opp-info -->

		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->

</article><!-- .volunteer-opp one-time-opp -->