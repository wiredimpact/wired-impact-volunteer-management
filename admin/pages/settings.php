<?php
/**
 * @package WI_Volunteer_Management/Admin
 */

/**
 * Output the HTML for our settings page. Utilizes the WI_Volunteer_Management_Form class to generate
 * the necessary HTML.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-form.php';

$wi_form = new WI_Volunteer_Management_Form();
$wi_form->admin_header();
?>
	
	<h2 class="nav-tab-wrapper" id="wivm-tabs">
		<a class="nav-tab" id="general-tab" href="#top#general"><span class="dashicons dashicons-admin-tools"></span> <?php _e( 'General', 'wivm' ); ?></a>
		<a class="nav-tab" id="defaults-tab" href="#top#defaults"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Defaults', 'wivm' ); ?></a>
		<a class="nav-tab" id="email-tab" href="#top#email"><span class="dashicons dashicons-email-alt"></span> <?php _e( 'Email', 'wivm' ); ?></a>
	</h2>

	<?php
	//Display hidden fields and nonces
	settings_fields( 'wivm-settings-group' );

	//Display General settings tab
	$wi_form->form_table_start( 'general' );

		$wi_form->radio( 		'use_css', array( __( 'Yes, please provide basic styling.', 'wivm' ), __( 'No, I\'ll code my own styling.', 'wivm' ) ), 'Load Plugin CSS?' );
		//$wi_form->textinput( 	'food', 		'Favorite Food', 	array( 'description' => 'Description for input field.' ) );
    	//$wi_form->textinput( 	'color', 		'Favorite Color' );
    	//$wi_form->textarea( 	'muchcontent', 	'Lots of Content', 	array( 'rows' => 10, 'description' => 'You can use info here.' ) );
    	//$wi_form->checkbox( 	'checkbox_1', 		__( 'Checkbox 1', 'wivm' ), 'Check this box and you\'ll Win' );
    	//$wi_form->checkbox( 	'checkbox_2', 		__( 'Checkbox 2', 'wivm' ), 'This box is awesome' );
    	//$wi_form->select( 		'select_1', 	'Awesome Select',	array( 'option 1', 'option 2', 'option 2' ), array( 'description' => 'This is a select you want to pick.' ) );

	$wi_form->form_table_end();

	//Display Defaults settings tab
	$wi_form->form_table_start( 'defaults' );

		$wi_form->section_heading( __( 'Default Contact Information', 'wivm' ), __( 'These contact settings will be loaded by default for all new volunteer opportunities, but you can customize each opportunity individually.', 'wivm' ) );
		$wi_form->textinput( 'default_contact_name', 	__( 'Default Contact Name', 'wivm' ) );
		$wi_form->textinput( 'default_contact_phone',	__( 'Default Contact Phone', 'wivm' ) );
		$wi_form->textinput( 'default_contact_email', 	__( 'Default Contact Email', 'wivm' ) );

		$wi_form->section_heading( __( 'Default Location Information', 'wivm' ), __( 'These location settings will be loaded by default for all new volunteer opportunities, but you can customize each opportunity individually.', 'wivm' ) );
		$wi_form->textinput( 'default_location', 	__( 'Default Location Name', 'wivm' ) );
		$wi_form->textinput( 'default_street', 		__( 'Default Street', 'wivm' ) );
		$wi_form->textinput( 'default_city', 		__( 'Default City', 'wivm' ) );
		$wi_form->textinput( 'default_state', 		__( 'Default State', 'wivm' ) );
		$wi_form->textinput( 'default_zip', 		__( 'Default Zip', 'wivm' ) );

	$wi_form->form_table_end();

	//Display Email settings tab
	$wi_form->form_table_start( 'email' );

		//$wi_form->textinput( 		'stuff', 			'Stuff', 			array( 'description' => 'Description for input field.' ) );
    	//$wi_form->textinput( 		'other_stuff', 	'Other Stuff' );
    	//$wi_form->wysiwyg_editor( 	'email_one', 	'Test Email Setting', 	array( 'description' => 'You can use variables here.' ) );
    	//$wi_form->wysiwyg_editor( 	'email_two', 	'Test Email Setting 2', array( 'description' => 'You can use info here.' ) );

	$wi_form->form_table_end();


$wi_form->admin_footer();