<?php
/**
 * @package WI_Volunteer_Management/Admin
 *
 * Output the HTML for our individual volunteer page. Utilizes the WI_Volunteer_Management_Volunteer class to pull the volunteer's information.
 */

if ( !current_user_can( 'list_users' ) || !isset( $_REQUEST['user_id'] ) ){
	wp_die( __( 'Cheatin&#8217; uh?', 'wivm' ), 403 );
}

$volunteer_id = absint( $_REQUEST['user_id'] );
$volunteer = new WI_Volunteer_Management_Volunteer( $volunteer_id );
?>

<div class="wrap wi-volunteer">

	<div class="volunteer-info">
		
		<div class="contact-info">

			<div class="avatar">
				<?php echo get_avatar( $volunteer->ID ); ?>
			</div>
			<h1><?php echo $volunteer->meta['first_name'] . ' ' . $volunteer->meta['last_name']; ?></h1>
			<span><?php echo __( 'E-mail:', 'wivm' ) . ' ' . $volunteer->meta['email']; ?></span>
			<span><?php echo __( 'Phone:', 'wivm' ) . ' ' . $volunteer->meta['phone']; ?></span>
			<span><?php echo __( 'Volunteer since', 'wivm' ) . ' ' . mysql2date( 'Y', $volunteer->meta['first_volunteer_opp_time'] ); ?></span>
		
		</div>

		<div class="volunteer-notes">
			<h3><?php _e( 'Notes', 'wivm' ); ?></h3>
			<?php echo apply_filters( 'the_content', $volunteer->meta['notes'] ); ?>
		</div>
		
		<a href="<?php echo admin_url( 'user-edit.php?user_id=' . $volunteer_id ); ?>" class="button edit-volunteer">
			<?php _e( 'Edit Volunteer Info', 'wivm' ); ?>
		</a>
	
	</div><!-- .volunteer-info -->

	<div class="volunteer-opps-wrapper">

		<h1><?php _e( 'Volunteer Opportunities', 'wivm' ); ?> <span>(<?php echo $volunteer->meta['num_volunteer_opps']; ?>)</span></h1>
		
		<div class="opps one-time">

			<?php
			$opps = $volunteer->get_volunteer_opps( 'one-time' );

			//Allow us to add in a line showing today so users can see opps that have and haven't happened yet.
			$today_timestamp = strtotime( 'today' ) + ( get_option('gmt_offset') * HOUR_IN_SECONDS );
			$last_opp_start = 0; //Start at zero to "Today" doesn't show at the top
			?>

			<h2><?php echo __( 'One-Time Volunteer Opportunities', 'wivm' ) . ' (' . count( $opps ) . ')'; ?></h2>

			<?php foreach ( $opps as $opp ){ ?>

				<?php if( $last_opp_start >= $today_timestamp && $opp->opp_meta	['start_date_time'] < $today_timestamp ): ?>
					<div class="today"><span><?php _e( 'Today', 'wivm' ); ?></span></div>
				<?php endif; $last_opp_start = $opp->opp_meta['start_date_time']; ?>

				<div class="opp">
					<div class="circle"></div>
					<h3><a href="<?php echo get_permalink( $opp->ID ); ?>"><?php echo get_the_title( $opp->ID ); ?></a></h3>
					<div class="opp-time"><?php echo ( $opp->opp_meta['one_time_opp'] == 1 ) ? $opp->format_opp_times() : $opp->opp_meta['flexible_frequency']; ?></div>
					<a class="button button-small remove-rsvp" data-post-id="<?php echo $opp->ID; ?>" data-user-id="<?php echo $volunteer_id; ?>" href="#remove_rsvp"><?php _e( 'Remove RSVP', 'wivm' ); ?></a>
				</div>

			<?php } ?>

		</div><!-- .opps one-time -->

		<div class="opps flexible">

			<?php $opps = $volunteer->get_volunteer_opps( 'flexible' ); ?>

			<h2><?php echo __( 'Flexible Volunteer Opportunities', 'wivm' ) . ' (' . count( $opps ) . ')'; ?></h2>

			<?php foreach ( $opps as $opp ){ ?>

				<div class="opp">
					<div class="circle"></div>
					<h3><a href="<?php echo get_permalink( $opp->ID ); ?>"><?php echo get_the_title( $opp->ID ); ?></a></h3>
					<div class="opp-time"><?php echo ( $opp->opp_meta['one_time_opp'] == 1 ) ? $opp->format_opp_times() : $opp->opp_meta['flexible_frequency']; ?></div>
					<a class="button button-small remove-rsvp" data-post-id="<?php echo $opp->ID; ?>" data-user-id="<?php echo $volunteer_id; ?>" href="#remove_rsvp"><?php _e( 'Remove RSVP', 'wivm' ); ?></a>
				</div>

			<?php } ?>

		</div><!-- .opps .flexible -->

	</div><!-- .volunteer-opps-wrapper -->

</div><!-- .wi-volunteer -->