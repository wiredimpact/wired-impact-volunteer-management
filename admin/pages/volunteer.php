<?php

/**
 * Output the HTML for our individual volunteer page.
 * 
 * Utilizes the WI_Volunteer_Management_Volunteer class to pull the volunteer's information.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 */

if ( !current_user_can( 'edit_others_posts' ) || !isset( $_REQUEST['user_id'] ) ){
	wp_die( __( 'Cheatin&#8217; uh?', 'wired-impact-volunteer-management' ), 403 );
}

$volunteer_id = absint( $_REQUEST['user_id'] );
$volunteer = new WI_Volunteer_Management_Volunteer( $volunteer_id );
?>

<div class="wrap wi-volunteer">

	<h1><?php echo __( apply_filters( 'wivm_submenu_page_name', 'Volunteer' ) . ': ', 'wired-impact-volunteer-management' ) . $volunteer->meta['first_name'] . ' ' . $volunteer->meta['last_name']; ?></h1>

	<div class="volunteer-info">
		
		<div class="contact-info">

			<?php echo get_avatar( $volunteer->ID, 65 ); ?>
			<span><?php echo __( 'Email:', 'wired-impact-volunteer-management' ) . ' ' . $volunteer->meta['email']; ?></span>
			<span><?php echo __( 'Phone:', 'wired-impact-volunteer-management' ) . ' ' . $volunteer->meta['phone']; ?></span>
			<span><?php echo __( 'Volunteer since', 'wired-impact-volunteer-management' ) . ' ' . mysql2date( 'Y', $volunteer->meta['first_volunteer_opp_time'] ); ?></span>
		
		</div>

		<div class="volunteer-notes">
			<h3><?php _e( 'Notes', 'wired-impact-volunteer-management' ); ?></h3>
			<?php
			$default_volunteer_note = __( 'There aren\'t any notes for this volunteer. You can add some by clicking the Edit Volunteer Info button and filling out the Notes field.', 'wired-impact-volunteer-management' );

			// On multisite, only super admins can edit users by default.
			if ( ! current_user_can( 'edit_user', $volunteer_id ) ) {

				$default_note_required_role = ( is_multisite() ) ? __( 'Super Admin', 'wired-impact-volunteer-management' ) : __( 'Administrator', 'wired-impact-volunteer-management' );
				$default_volunteer_note     = sprintf( __( 'There aren\'t any notes for this volunteer. Unfortunately, you can\'t edit the notes. Only users with the %s role can edit volunteer notes.', 'wired-impact-volunteer-management' ), $default_note_required_role );
			}

			$default_volunteer_note_html = '<p><em>' . $default_volunteer_note . '</em></p>';
			$volunteer_notes = ( $volunteer->meta['notes'] != '' ) ? $volunteer->meta['notes'] : $default_volunteer_note_html;
			echo apply_filters( 'the_content', $volunteer_notes );
			?>
		</div>

		<div class="contact-footer clear">
			<?php if ( current_user_can( 'edit_user', $volunteer_id ) ) : ?>
			<a href="<?php echo admin_url( 'user-edit.php?user_id=' . $volunteer_id ); ?>" class="button edit-volunteer">
				<?php _e( 'Edit Volunteer Info', 'wired-impact-volunteer-management' ); ?>
			</a>
			<?php endif; ?>
		</div>
	
	</div><!-- .volunteer-info -->

	<div class="volunteer-opps-wrapper">

		<h1><?php _e( 'Volunteer Opportunities', 'wired-impact-volunteer-management' ); ?> <span>(<?php echo $volunteer->meta['num_volunteer_opps']; ?>)</span></h1>
		
		<div class="opps one-time">

			<?php
			$opps = $volunteer->get_volunteer_opps( 'one-time' );

			//Allow us to add in a line showing today so users can see opps that have and haven't happened yet.
			$today_timestamp = strtotime( 'today' ) + ( get_option('gmt_offset') * HOUR_IN_SECONDS );
			$last_opp_start = 0; //Start at zero to "Today" doesn't show at the top
			?>

			<h2><?php echo __( 'One-Time Volunteer Opportunities', 'wired-impact-volunteer-management' ) . ' (' . count( $opps ) . ')'; ?></h2>

			<?php foreach ( $opps as $opp ){ ?>

				<?php if( $last_opp_start >= $today_timestamp && $opp->opp_meta	['start_date_time'] < $today_timestamp ): ?>
					<div class="today"><span><?php _e( 'Today', 'wired-impact-volunteer-management' ); ?></span></div>
				<?php endif; $last_opp_start = $opp->opp_meta['start_date_time']; ?>

				<div class="opp">
					<div class="circle"></div>
					<h3><a href="<?php echo get_edit_post_link( $opp->ID ); ?>"><?php echo get_the_title( $opp->ID ); ?></a></h3>
					<div class="opp-time"><?php echo ( $opp->opp_meta['one_time_opp'] == 1 ) ? $opp->format_opp_times() : $opp->opp_meta['flexible_frequency']; ?></div>
					<a class="button button-small remove-rsvp" data-post-id="<?php echo $opp->ID; ?>" data-user-id="<?php echo $volunteer_id; ?>" href="#remove_rsvp"><?php _e( 'Remove RSVP', 'wired-impact-volunteer-management' ); ?></a>
				</div>

			<?php } ?>

		</div><!-- .opps one-time -->

		<div class="opps flexible">

			<?php $opps = $volunteer->get_volunteer_opps( 'flexible' ); ?>

			<h2><?php echo __( 'Flexible Volunteer Opportunities', 'wired-impact-volunteer-management' ) . ' (' . count( $opps ) . ')'; ?></h2>

			<?php foreach ( $opps as $opp ){ ?>

				<div class="opp">
					<div class="circle"></div>
					<h3><a href="<?php echo get_edit_post_link( $opp->ID ); ?>"><?php echo get_the_title( $opp->ID ); ?></a></h3>
					<div class="opp-time"><?php echo ( $opp->opp_meta['one_time_opp'] == 1 ) ? $opp->format_opp_times() : $opp->opp_meta['flexible_frequency']; ?></div>
					<a class="button button-small remove-rsvp" data-post-id="<?php echo $opp->ID; ?>" data-user-id="<?php echo $volunteer_id; ?>" href="#remove_rsvp"><?php _e( 'Remove RSVP', 'wired-impact-volunteer-management' ); ?></a>
				</div>

			<?php } ?>

		</div><!-- .opps .flexible -->

	</div><!-- .volunteer-opps-wrapper -->

</div><!-- .wi-volunteer -->