<?php
/**
 * @package WI_Volunteer_Management/Admin
 */

/**
 * Output the HTML for our settings page. Utilizes the WI_Volunteer_Management_Form class to generate
 * the necessary HTML. Every setting added here needs a default in the WI_Volunteer_Management_Options() class.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-form.php';

$wi_form = new WI_Volunteer_Management_Form();
$wi_form->admin_header();
?>
	
	<h2 class="nav-tab-wrapper" id="wivm-tabs">
		<a class="nav-tab" id="general-tab" href="#top#general"><span class="dashicons dashicons-admin-tools"></span> <?php _e( 'General', 'wivm' ); ?></a>
		<a class="nav-tab" id="defaults-tab" href="#top#defaults"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Opportunity Defaults', 'wivm' ); ?></a>
		<a class="nav-tab" id="email-tab" href="#top#email"><span class="dashicons dashicons-email-alt"></span> <?php _e( 'Email', 'wivm' ); ?></a>
	</h2>

	<?php
	//Display hidden fields and nonces
	settings_fields( 'wivm-settings-group' );

	//Display General settings tab
	$wi_form->form_table_start( 'general' );

		$wi_form->radio( 		'use_css', array( 1 => __( 'Yes, please provide basic styling.', 'wivm' ), 0 => __( 'No, I\'ll code my own styling.', 'wivm' ) ), 'Load Plugin CSS?' );
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

		$wi_form->textinput( 		'from_email_address', 				__( 'From Email Address', 'wivm'), 									array( 'description' => __( 'The email address you\'d like to send from.', 'wivm') ) );
		$wi_form->textinput( 		'from_email_name', 					__( 'From Email Name', 'wivm'), 									array( 'description' => __( 'The name of the person you\'d like the emails to be sent from.', 'wivm') ) );
		$wi_form->textinput( 		'volunteer_signup_email_subject', 	__( 'Volunteer Signup Email Subject', 'wivm'), 						array( 'description' => __( 'The subject of the email to a volunteer after they sign up.', 'wivm') ) );
    	$wi_form->wysiwyg_editor( 	'volunteer_signup_email', 			__( 'Volunteer Signup Email', 'wivm'), 								array( 'description' => __( 'The email to a volunteer who just RSVPed. You can use the variables {volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone} and {contact_email} which will be replaced when the email is sent.', 'wivm') ) );
		$wi_form->textinput( 		'admin_email_address', 				__( 'Admin Email Address', 'wivm'), 								array( 'description' => __( 'The person to notify when volunteers sign up. The contact for the volunteer opportunity will also be notified. If this field is blank then only the contact will be notified.', 'wivm') ) );
    	$wi_form->textinput( 		'admin_signup_email_subject', 		__( 'Admin Signup Email Subject', 'wivm'), 							array( 'description' => __( 'The subject of the email to the admin after someone RSVPs.', 'wivm') ) );
    	$wi_form->wysiwyg_editor( 	'admin_signup_email', 				__( 'Admin Signup Email', 'wivm'), 									array( 'description' => __( 'The email to the admin after someone RSVPs. You can use the variables {volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone} and {contact_email} which will be replaced when the email is sent.', 'wivm') ) );
		$wi_form->textinput( 		'days_prior_reminder', 				__( 'Number of Days Prior to Opportunity to Send Reminder', 'wivm'),array( 'description' => __( 'The number of days prior to a one-time volunteer opportunity to send a reminder. Flexible opportunities do not send a reminder email.', 'wivm') ) );
		$wi_form->textinput( 		'volunteer_reminder_email_subject',	__( 'Volunteer Reminder Email Subject', 'wivm'), 					array( 'description' => __( 'The subject of the reminder email sent to volunteers prior to their opportunity.', 'wivm') ) );
    	$wi_form->wysiwyg_editor( 	'volunteer_reminder_email', 		__( 'Volunteer Reminder Email', 'wivm'), 							array( 'description' => __( 'The reminder email to volunteers before their opportunity arrives. This is sent to the admins with the volunteers BCC\'ed. That way you know when the email has gone out. You may use all the opportunity variables listed for the other emails, but since only one email is sent out do not use any of the volunteer variables.', 'wivm') ) );

	$wi_form->form_table_end();


$wi_form->admin_footer();