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

require_once plugin_dir_path( __DIR__ ) . 'class-form.php';

$wi_form = new WI_Volunteer_Management_Form();
$wi_form->admin_header();

// Filter to allow the Help & Settings menu 'Help' tab to be hidden.
$wivm_show_help_tab = apply_filters( 'wivm_show_help_tab', true );
?>

	<h2 class="nav-tab-wrapper" id="wivm-tabs">
		<?php if ( $wivm_show_help_tab === true ) : ?>
			<a class="nav-tab" id="help-tab" href="#top-help"><span class="dashicons dashicons-editor-help"></span> <?php _e( 'Help', 'wired-impact-volunteer-management' ); ?></a>
		<?php endif; ?>
		
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<a class="nav-tab" id="general-tab" href="#top-general"><span class="dashicons dashicons-admin-tools"></span> <?php _e( 'General', 'wired-impact-volunteer-management' ); ?></a>
			<a class="nav-tab" id="defaults-tab" href="#top-defaults"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Opportunity Defaults', 'wired-impact-volunteer-management' ); ?></a>
			<a class="nav-tab" id="email-tab" href="#top-email"><span class="dashicons dashicons-email-alt"></span> <?php _e( 'Email', 'wired-impact-volunteer-management' ); ?></a>
		<?php endif; ?>
	</h2>

	<?php
	// Display hidden fields and nonces.
	settings_fields( 'wivm-settings-group' );

	// Display a Help tab.
	if ( $wivm_show_help_tab === true ) :

		$wi_form->form_table_start( 'help' );

			$wi_form->section_heading( __( 'Getting Started', 'wired-impact-volunteer-management' ), sprintf( __( 'Check out the <a target="_blank" href="%s">WordPress plugin repository</a> for FAQs and tips for getting started with the Wired Impact Volunteer Management plugin.', 'wired-impact-volunteer-management' ), 'https://wordpress.org/plugins/wired-impact-volunteer-management/faq/' ), 1 );

			$wi_form->section_heading( __( 'Need More Help?', 'wired-impact-volunteer-management' ), sprintf( __( 'If our FAQs don\'t cover your questions, reach out to us on the <a target="_blank" href="%s">WordPress support forums</a>.', 'wired-impact-volunteer-management' ), 'https://wordpress.org/support/plugin/wired-impact-volunteer-management' ), 1 );

			do_action( 'wivm_display_help_settings', $wi_form );

		$wi_form->form_table_end();

	endif;

	// Display General settings tab.
	if ( current_user_can( 'manage_options' ) ) :
		$wi_form->form_table_start( 'general' );

			$wi_form->radio( 'use_css', array( 1 => __( 'Yes, please provide basic styling.', 'wired-impact-volunteer-management' ), 0 => __( 'No, I\'ll code my own styling.', 'wired-impact-volunteer-management' ) ), 'Load Plugin CSS?' );
			$wi_form->radio( 'use_honeypot', array( 1 => __( 'Yes, please use a honeypot to prevent spam.', 'wired-impact-volunteer-management' ), 0 => __( 'No, I\'ll handle spam in my own way.', 'wired-impact-volunteer-management' ) ), 'Enable Honeypot for Built-In Form?' );
			$wi_form->hidden( 'show_getting_started_notice' );

			do_action( 'wivm_display_general_settings', $wi_form );

		$wi_form->form_table_end();

		// Display Defaults settings tab.
		$wi_form->form_table_start( 'defaults' );

			$wi_form->section_heading( __( 'Default Contact Information', 'wired-impact-volunteer-management' ), __( 'The contact information below will be used as the default for all new volunteer opportunities. You can customize the contact information for an individual opportunity as needed.', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_contact_name', __( 'Default Contact Name', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_contact_phone', __( 'Default Contact Phone Number', 'wired-impact-volunteer-management' ), array(), 'format_phone_number' );
			$wi_form->textinput( 'default_contact_email', __( 'Default Contact Email Address', 'wired-impact-volunteer-management' ) );

			$wi_form->section_heading( __( 'Default Location Information', 'wired-impact-volunteer-management' ), __( 'The location information below will be used as the default for all new volunteer opportunities. You can customize the location for an individual opportunity as needed.', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_location', __( 'Default Location Name', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_street', __( 'Default Street', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_city', __( 'Default City', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_state', __( 'Default State', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'default_zip', __( 'Default Zip', 'wired-impact-volunteer-management' ) );

			$wi_form->section_heading( __( 'Default Volunteer Signup Form', 'wired-impact-volunteer-management' ), __( 'The signup form below will be used as the default for all new volunteer opportunities. You can customize an individual opportunity to use a different form as needed.', 'wired-impact-volunteer-management' ) );
			$wi_form->select(
				'default_form_type',
				__( 'Default Form Type', 'wired-impact-volunteer-management' ),
				apply_filters(
					'wivm_form_type_setting_options',
					array(
						'no_form'       => __( 'No Form', 'wired-impact-volunteer-management' ),
						'built_in_form' => __( 'Built-In Signup Form', 'wired-impact-volunteer-management' ),
					)
				),
			);

			do_action( 'wivm_display_defaults_settings', $wi_form );

		$wi_form->form_table_end();

		// Display Email settings tab.
		$wi_form->form_table_start( 'email' );

			$wi_form->section_heading( __( 'All Emails', 'wired-impact-volunteer-management' ), __( 'The email address and name below will be used as the sender for every email to volunteers, admins and volunteer opportunity contacts.', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'from_email_address', __( 'From Email Address', 'wired-impact-volunteer-management' ), array( 'description' => sprintf( __( 'The email address you\'d like to send from. If left blank, "%s" will be used from your website\'s General Settings.', 'wired-impact-volunteer-management' ), get_option( 'admin_email' ) ) ) );
			$wi_form->textinput( 'from_email_name', __( 'From Name', 'wired-impact-volunteer-management' ), array( 'description' => sprintf( __( 'The name of the person you\'d like the emails sent from. If left blank, "%s" will be used from your website\'s General Settings.', 'wired-impact-volunteer-management' ), get_option( 'blogname' ) ) ) );

			$wi_form->section_heading( __( 'Volunteer Signup Email', 'wired-impact-volunteer-management' ), __( 'This email will be sent to volunteers immediately after they sign up for an opportunity.', 'wired-impact-volunteer-management' ) );
			$wi_form->checkbox( 'send_signup_email_to_volunteers', __( 'Send Volunteer Signup Email?', 'wired-impact-volunteer-management' ), __( 'Send each volunteer an email immediately after they sign up for an opportunity.', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'volunteer_signup_email_subject', __( 'Volunteer Signup Email Subject', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The subject of the email to a volunteer after they sign up.', 'wired-impact-volunteer-management' ) ), null, 'send_signup_email_to_volunteers' );
			$wi_form->wysiwyg_editor( 'volunteer_signup_email', __( 'Volunteer Signup Email Message', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The email to a volunteer after they sign up. You can use these variables to personalize the email when it\'s sent: {volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone}, {contact_email}', 'wired-impact-volunteer-management' ) ), 'send_signup_email_to_volunteers' );

			$wi_form->section_heading( __( 'Admin Signup Email', 'wired-impact-volunteer-management' ), __( 'This email will be sent to the admin email address and the contact for the opportunity immediately after someone signs up to volunteer.', 'wired-impact-volunteer-management' ) );
			$wi_form->checkbox( 'send_signup_email_to_admins', __( 'Send Admin Signup Email?', 'wired-impact-volunteer-management' ), __( 'Send the admin and the contact for each opportunity an email immediately after someone signs up to volunteer.', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'admin_email_address', __( 'Admin Email Address', 'wired-impact-volunteer-management' ), array( 'description' => __( 'This person is emailed for every volunteer signup across all opportunities. If this field is blank, only the contact for the individual volunteer opportunity will be emailed.', 'wired-impact-volunteer-management' ) ), null, 'send_signup_email_to_admins' );
			$wi_form->textinput( 'admin_signup_email_subject', __( 'Admin Signup Email Subject', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The subject of the email to the admin and the contact for each opportunity after someone signs up to volunteer.', 'wired-impact-volunteer-management' ) ), null, 'send_signup_email_to_admins' );
			$wi_form->wysiwyg_editor( 'admin_signup_email', __( 'Admin Signup Email Message', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The email to the admin and the contact for each opportunity after someone signs up to volunteer. You can use these variables to personalize the email when it\'s sent: {volunteer_first_name}, {volunteer_last_name}, {volunteer_phone}, {volunteer_email}, {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone}, {contact_email}', 'wired-impact-volunteer-management' ) ), 'send_signup_email_to_admins' );

			$wi_form->section_heading( __( 'Volunteer Reminder Email', 'wired-impact-volunteer-management' ), __( 'This reminder email will be sent to volunteers a set number of days before a one-time volunteer opportunity takes place. A reminder email is never sent for flexible volunteer opportunities.', 'wired-impact-volunteer-management' ) );
			$wi_form->checkbox( 'send_reminder_email_to_volunteers', __( 'Send Volunteer Reminder Email?', 'wired-impact-volunteer-management' ), __( 'Send volunteers a reminder email before a one-time opportunity takes place.', 'wired-impact-volunteer-management' ) );
			$wi_form->textinput( 'days_prior_reminder', __( 'Number of Days Before Opportunity to Send Reminder', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The number of days before a one-time volunteer opportunity to send the reminder email. Ex: 4', 'wired-impact-volunteer-management' ) ), null, 'send_reminder_email_to_volunteers' );
			$wi_form->textinput( 'volunteer_reminder_email_subject', __( 'Volunteer Reminder Email Subject', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The subject of the reminder email sent to volunteers before their opportunity takes place.', 'wired-impact-volunteer-management' ) ), null, 'send_reminder_email_to_volunteers' );
			$wi_form->wysiwyg_editor( 'volunteer_reminder_email', __( 'Volunteer Reminder Email Message', 'wired-impact-volunteer-management' ), array( 'description' => __( 'The reminder email to volunteers before their opportunity takes place. This email is sent to the admin and the contact for each opportunity with the volunteers BCC\'ed. That way you know when the email has gone out. You can use these variables to personalize the email when it\'s sent: {opportunity_name}, {opportunity_date_time}, {opportunity_location}, {contact_name}, {contact_phone}, {contact_email}', 'wired-impact-volunteer-management' ) ), 'send_reminder_email_to_volunteers' );

			do_action( 'wivm_display_email_settings', $wi_form );

		$wi_form->form_table_end();
	endif;

	$wi_form->admin_footer();