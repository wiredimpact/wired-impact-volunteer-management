<?php
/**
 * This template is used to display a single volunteer opportunity.
 *
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */
$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'volunteer-opp' ); ?>>

				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->

				<?php if( has_post_thumbnail() ): ?>
				<div class="post-thumbnail">
					<?php the_post_thumbnail(); ?>
				</div><!-- .post-thumbnail -->
				<?php endif; ?>

				<div class="entry-content">
					<div class="volunteer-opp-info">
						<span><strong><?php echo __( 'When: ', 'wivm' ); ?></strong> <?php echo $opp->format_opp_times(); ?></span>
						<span><strong><?php echo __( 'Where: ', 'wivm' ); ?></strong> <?php echo $opp->format_address(); ?></span>

						<span><strong><?php echo __( 'Contact: ', 'wivm' ); ?></strong> <?php echo $opp->opp_meta['contact_name']; ?></span>
						<span><strong><?php echo __( 'Contact Email: ', 'wivm' ); ?></strong> <?php echo $opp->get_email_as_link( $opp->opp_meta['contact_email'] ); ?></span>
						<span><strong><?php echo __( 'Contact Phone: ', 'wivm' ); ?></strong> <?php echo $opp->opp_meta['contact_formatted_phone']; ?></span>

						<span><strong><?php echo __( 'Open Volunteer Spots: ', 'wivm' ); ?></strong> <?php echo $opp->get_open_volunteer_spots(); ?></span>
					</div><!-- .volunteer-opp-info -->

					<?php the_content(); ?>
				</div><!-- .entry-content -->

			</article><!-- #post-## -->

		<?php endwhile;	?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>