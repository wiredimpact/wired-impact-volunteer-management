<?php
/**
 * @package WI_Volunteer_Management/Admin
 *
 * Output the HTML for our individual volunteer page. Utilizes the WI_Volunteer_Management_Volunteer class to pull the volunteer's information.
 */

if ( !current_user_can( 'list_users' ) || !isset( $_REQUEST['user_id'] ) ){
	wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
}

$volunteer = new WI_Volunteer_Management_Volunteer( absint( $_REQUEST['user_id'] ) );
?>

<div class="wrap">

	<?php echo get_avatar( $volunteer->ID ); ?>
	<h2><?php echo $volunteer->meta['first_name'] . ' ' . $volunteer->meta['last_name']; ?></h2>
	<span><?php _e( 'E-mail:', 'wivm' ); echo ' ' . $volunteer->meta['email']; ?></span>
	<span><?php _e( 'Phone:', 'wivm' ); echo ' ' . $volunteer->meta['phone']; ?></span>
	<span><?php _e( 'Volunteer since', 'wivm' ); echo ' YEAR GOES HERE!' ?></span>

	<h3><?php _e( 'Notes', 'wivm' ); ?></h3>
	<textarea id="notes" name="notes" rows="5" cols="30"><?php echo $volunteer->meta['notes']; ?></textarea>

</div>