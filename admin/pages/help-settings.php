<?php

/**
 * Output the HTML for our help & settings page.
 * 
 * Utilizes the WI_Volunteer_Management_Form class to generate the necessary HTML.
 * Every setting here needs a default in the WI_Volunteer_Management_Options() class and every setting needs to be listed here.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-form.php';

$wi_form = new WI_Volunteer_Management_Form();
$wi_form->admin_header();

// Filter to allow the Help & Settings menu 'Help' tab to be hidden
$wivm_show_help_tab	= apply_filters( 'wivm_show_help_tab', true );

?>

	<h2 class="nav-tab-wrapper" id="wivm-tabs">
		<?php if( $wivm_show_help_tab === true ) : ?>
			<a class="nav-tab" id="help-tab" href="#top#help"><span class="dashicons dashicons-editor-help"></span> <?php _e( apply_filters( 'wivm_help_tab_name', 'Help' ), 'wired-impact-volunteer-management' ); ?></a>
		<?php endif; ?>
		
		<?php if( current_user_can( 'manage_options' ) ): ?>
			<a class="nav-tab" id="general-tab" href="#top#general"><span class="dashicons dashicons-admin-tools"></span> <?php _e( 'General', 'wired-impact-volunteer-management' ); ?></a>
			<a class="nav-tab" id="defaults-tab" href="#top#defaults"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Opportunity Defaults', 'wired-impact-volunteer-management' ); ?></a>
			<a class="nav-tab" id="email-tab" href="#top#email"><span class="dashicons dashicons-email-alt"></span> <?php _e( 'Email', 'wired-impact-volunteer-management' ); ?></a>
		<?php endif; ?>
	</h2>

	<?php
	//Display hidden fields and nonces
	settings_fields( 'wivm-settings-group' );

	//Display a Help tab
	if( $wivm_show_help_tab === true ) :
		$wi_form->form_table_start( 'help' ); ?>

			<h2><?php _e( 'FAQs and Get Started', 'wired-impact-volunteer-management' ); ?></h2>
			<p><?php printf( __( 'Check out the <a target="_blank" href="%s">FAQs on the WordPress plugin repository</a> to get help and learn how to get started.' ), 'https://wordpress.org/plugins/wired-impact-volunteer-management/faq/' ); ?></p>

			<h2><?php _e( 'Need More Help?', 'wired-impact-volunteer-management' ); ?></h2>
			<p><?php printf( __( 'If the FAQs aren\'t cutting it and you need more help reach out to us on the <a target="_blank" href="%s">WordPress support forums.</a>' ), 'https://wordpress.org/support/plugin/wired-impact-volunteer-management' ); ?></p> 
		
		<?php do_action( 'wivm_display_help_settings', $wi_form );

		$wi_form->form_table_end();
	endif;

	//Display General settings tab
	if( current_user_can( 'manage_options' ) ):
	$wi_form->form_table_start( 'general' );

		$wi_form->radio( 'use_css', array( 1 => __( 'Yes, please provide basic styling.', 'wired-impact-volunteer-management' ), 0 => __( 'No, I\'ll code my own styling.', 'wired-impact-volunteer-management' ) ), 'Load Plugin CSS?' );
		$wi_form->radio( 'use_honeypot', array( 1 => __( 'Yes, please use a honeypot to prevent spam.', 'wired-impact-volunteer-management' ), 0 => __( 'No, I\'ll handle spam in my own way.', 'wired-impact-volunteer-management' ) ), 'Enable Honeypot?' );
		$wi_form->hidden( 'show_getting_started_notice' );

		do_action( 'wivm_display_general_settings', $wi_form );

	$wi_form->form_table_end();

	//Display Defaults settings tab
	$wi_form->form_table_start( 'defaults' );

		$wi_form->section_heading( __( 'Default Contact Information', 'wired-impact-volunteer-management' ), __( 'These contact settings will be loaded by default for all new volunteer opportunities, but you can customize each opportunity individually.', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_contact_name', 	__( 'Default Contact Name', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_contact_phone',	__( 'Default Contact Phone', 'wired-impact-volunteer-management' ), array(), "format_phone_number" );
		$wi_form->textinput( 'default_contact_email', 	__( 'Default Contact Email', 'wired-impact-volunteer-management' ) );

		$wi_form->section_heading( __( 'Default Location Information', 'wired-impact-volunteer-management' ), __( 'These location settings will be loaded by default for all new volunteer opportunities, but you can customize each opportunity individually.', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_location', 	__( 'Default Location Name', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_street', 		__( 'Default Street', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_city', 		__( 'Default City', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_state', 		__( 'Default State', 'wired-impact-volunteer-management' ) );
		$wi_form->textinput( 'default_zip', 		__( 'Default Zip', 'wired-impact-volunteer-management' ) );

		do_action( 'wivm_display_defaults_settings', $wi_form );

	$wi_form->form_table_end();

	//Display Email settings tab
	$wi_form->form_table_start( 'email' );

		$wi_form->textinput( 		'from_email_address', 				__( 'From Email Address', 'wired-impact-volunteer-management'), 									array( 'description' => sprintf( __( 'The email address you\'d like to send from. If blank "%s" will be used from the General Settings.', 'wired-impact-volunteer-management'), get_option( 'admin_email' ) ) ) );
		$wi_form->textinput( 		'from_email_name', 					__( 'From Email Name', 'wired-impact-volunteer-management'), 									array( 'description' => sprintf( __( 'The name of the person you\'d like the emails to be sent from. If blank "%s" will be used from the General Settings.', 'wired-impact-volunteer-management'), get_option( 'blogname' ) ) ) );
		$wi_form->textinput( 		'volunteer_signup_email_subject', 	__( 'Volunteer Signup Email Subject', 'wired-impact-volunteer-management'), 						array( 'description' => __( 'The subject of the email to a volunteer after they sign up.', 'wired-impact-volunteer-management') ) );
    	$wi_form->wysiwyg_editor( 	'volunteer_signup_email', 			__( 'Volunteer Signup Email', 'wired-impact-volunteer-management'), 								array( 'description' => __( 'The email to a volunteer who just RSVPed. You can use the variables {volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone} and {contact_email} which will be replaced when the email is sent.', 'wired-impact-volunteer-management') ) );
		$wi_form->textinput( 		'admin_email_address', 				__( 'Admin Email Address', 'wired-impact-volunteer-management'), 								array( 'description' => __( 'The person to notify when volunteers sign up. The contact for the volunteer opportunity will also be notified. If this field is blank then only the contact will be notified.', 'wired-impact-volunteer-management') ) );
    	$wi_form->textinput( 		'admin_signup_email_subject', 		__( 'Admin Signup Email Subject', 'wired-impact-volunteer-management'), 							array( 'description' => __( 'The subject of the email to the admin after someone RSVPs.', 'wired-impact-volunteer-management') ) );
    	$wi_form->wysiwyg_editor( 	'admin_signup_email', 				__( 'Admin Signup Email', 'wired-impact-volunteer-management'), 									array( 'description' => __( 'The email to the admin after someone RSVPs. You can use the variables {volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone} and {contact_email} which will be replaced when the email is sent.', 'wired-impact-volunteer-management') ) );
		$wi_form->textinput( 		'days_prior_reminder', 				__( 'Number of Days Prior to Opportunity to Send Reminder', 'wired-impact-volunteer-management'),array( 'description' => __( 'The number of days prior to a one-time volunteer opportunity to send a reminder. Flexible opportunities do not send a reminder email. Ex: 4', 'wired-impact-volunteer-management') ) );
		$wi_form->textinput( 		'volunteer_reminder_email_subject',	__( 'Volunteer Reminder Email Subject', 'wired-impact-volunteer-management'), 					array( 'description' => __( 'The subject of the reminder email sent to volunteers prior to their opportunity.', 'wired-impact-volunteer-management') ) );
    	$wi_form->wysiwyg_editor( 	'volunteer_reminder_email', 		__( 'Volunteer Reminder Email', 'wired-impact-volunteer-management'), 							array( 'description' => __( 'The reminder email to volunteers before their opportunity arrives. This is sent to the admins with the volunteers BCC\'ed. That way you know when the email has gone out. You can use the variables {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone} and {contact_email} which will be replaced when the email is sent. Since only one email is sent do not use any of the volunteer specific variables.', 'wired-impact-volunteer-management') ) );

    	do_action( 'wivm_display_email_settings', $wi_form );

	$wi_form->form_table_end();
	endif;

$wi_form->admin_footer();