<?php
/**
 * This template is used to display the sign up form for a single volunteer opportunity.
 *
 * This template is displayed immediately after the_content() is called within your theme file.
 * To adjust this template copy it into your current theme within a folder called "wivm".
 */
$opp = new WI_Volunteer_Management_Opportunity( $post->ID ); //Get volunteer opportunity information
?>

<h3><?php ( $opp->opp_meta['one_time_opp'] == 1 ) ? _e( 'Sign Up to Volunteer', 'wired-impact-volunteer-management' ) : _e( 'Express Interest in Volunteering', 'wired-impact-volunteer-management' ) ; ?></h3>
					
<?php if( $opp->should_allow_rvsps() ): ?>
<div class="loading message"><?php _e( 'Please wait...', 'wired-impact-volunteer-management' ); ?></div>
<div class="success message"><?php _e( 'Thanks for signing up. You\'ll receive a confirmation email shortly.', 'wired-impact-volunteer-management' ); ?></div>
<div class="already-rsvped message"><?php _e( 'It looks like you already signed up for this opportunity.', 'wired-impact-volunteer-management' ); ?></div>
<div class="rsvp-closed message"><?php _e( 'We\'re sorry, but we weren\'t able to sign you up. We have no more open spots.', 'wired-impact-volunteer-management' ); ?></div>
<div class="error message"><?php _e( 'Please fill in every field and make sure you entered a valid email address.', 'wired-impact-volunteer-management' ); ?></div>

<form id="wivm-sign-up-form" method="POST" url="<?php the_permalink(); ?>">
	<?php wp_nonce_field( 'wivm_sign_up_form_nonce', 'wivm_sign_up_form_nonce_field' ); ?>

	<?php do_action( 'wivm_start_sign_up_form_fields', $post ); ?>

	<label for="wivm_first_name"><?php _e( 'First Name:', 'wired-impact-volunteer-management' ); ?></label>
	<input type="text" tabindex="900" id="wivm_first_name" name="wivm_first_name" value="" />

	<label for="wivm_last_name"><?php _e( 'Last Name:', 'wired-impact-volunteer-management' ); ?></label>
	<input type="text" tabindex="910" id="wivm_last_name" name="wivm_last_name" value="" />

	<label for="wivm_phone"><?php _e( 'Phone:', 'wired-impact-volunteer-management' ); ?></label>
	<input type="text" tabindex="920" id="wivm_phone" name="wivm_phone" value="" />

	<label for="wivm_email"><?php _e( 'Email:', 'wired-impact-volunteer-management' ); ?></label>
	<input type="email" tabindex="930" id="wivm_email" name="wivm_email" value="" />

	<?php do_action( 'wivm_end_sign_up_form_fields', $post ); ?>

	<input type="hidden" id="wivm_opportunity_id" name="wivm_opportunity_id" value="<?php echo the_ID(); ?>" />
	<input type="submit" tabindex="940" value="<?php ( $opp->opp_meta['one_time_opp'] == 1 ) ? _e( 'Sign Up', 'wired-impact-volunteer-management' ) : _e( 'Express Interest', 'wired-impact-volunteer-management' ) ; ?>" />
</form>
<?php else: ?>
	<p><?php _e( 'We\'re sorry, but we\'re no longer accepting new volunteers for this opportunity.', 'wired-impact-volunteer-management' ); ?></p>
<?php endif; ?>