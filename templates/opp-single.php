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

					<h3><?php _e( 'Sign Up to Volunteer', 'wivm' ); ?></h3>
					<div class="loading message"><?php _e( 'Please wait...', 'wivm' ); ?></div>
					<div class="success message"><?php _e( 'Thanks for signing up. You\'ll receive a confirmation email shortly.', 'wivm' ); ?></div>
					<div class="error message"><?php _e( 'Please fill in every field and make sure you entered a valid email address.', 'wivm' ); ?></div>
					<form id="wivm-sign-up-form" method="POST" url="<?php the_permalink(); ?>">
						<?php wp_nonce_field( 'wivm_sign_up_form_nonce', 'wivm_sign_up_form_nonce_field' ); ?>

						<?php do_action( 'wivm_start_sign_up_form_fields', $post ); ?>

						<label for="wivm_first_name"><?php _e( 'First Name:', 'wivm' ); ?></label>
						<input type="text" tabindex="900" id="wivm_first_name" name="wivm_first_name" value="" />

						<label for="wivm_last_name"><?php _e( 'Last Name:', 'wivm' ); ?></label>
						<input type="text" tabindex="901" id="wivm_last_name" name="wivm_last_name" value="" />

						<label for="wivm_phone"><?php _e( 'Phone:', 'wivm' ); ?></label>
						<input type="text" tabindex="902" id="wivm_phone" name="wivm_phone" value="" />

						<label for="wivm_email"><?php _e( 'Email:', 'wivm' ); ?></label>
						<input type="email" tabindex="903" id="wivm_email" name="wivm_email" value="" />

						<?php do_action( 'wivm_end_sign_up_form_fields', $post ); ?>

						<input type="hidden" id="wivm_opportunity_id" name="wivm_opportunity_id" value="<?php echo the_ID(); ?>" />
						<input type="submit" tabindex="904" value="<?php _e( 'Sign Up', 'wivm' ); ?>" />
					</form>
				</div><!-- .entry-content -->

			</article><!-- #post-## -->

		<?php endwhile;	?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>