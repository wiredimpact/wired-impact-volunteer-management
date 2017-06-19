<?php

/**
 * Output the HTML for our Volunteers page.
 * 
 * Utilizes the WI_Volunteer_Users_List_Table class to generate the necessary HTML.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 */

if ( !current_user_can( 'edit_others_posts' ) ){
	wp_die( __( 'Cheatin&#8217; uh?', 'wired-impact-volunteer-management' ), 403 );
}

require_once ABSPATH . 'wp-admin/includes/class-wp-users-list-table.php';
require_once WIVM_DIR . 'includes/class-wp-volunteer-list-table.php';

$wp_list_table = new WI_Volunteer_Users_List_Table();
$pagenum = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
if ( $pagenum > $total_pages && $total_pages > 0 ) {
	wp_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}
?>

<div class="wrap">
	<h1>
	<?php _e( 'Wired Impact Volunteer Management: Volunteers', 'wired-impact-volunteer-management' );
	global $usersearch;
	if ( $usersearch ){
		printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;', 'wired-impact-volunteer-management' ) . '</span>', esc_html( $usersearch ) );
	}
	?>
	</h1>

	<form method="get" action="">
	<input type="hidden" name="page" value="<?php echo ( isset( $_REQUEST['page'] ) ) ? esc_attr( $_REQUEST['page'] ) : ''; ?>" />

	<?php $wp_list_table->search_box( __( 'Search Volunteers', 'wired-impact-volunteer-management' ), 'volunteer' ); ?>

	<?php $wp_list_table->display(); ?>

	</form>

	<br class="clear" />
</div>