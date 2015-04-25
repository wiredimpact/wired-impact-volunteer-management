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
		<a class="nav-tab" id="general-tab" href="#top#general"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'General', 'wivm' ); ?></a>
		<a class="nav-tab" id="email-tab" href="#top#email"><span class="dashicons dashicons-email-alt"></span> <?php _e( 'Email', 'wivm' ); ?></a>
		<a class="nav-tab" id="more-stuff-tab" href="#top#more-stuff"><span class="dashicons dashicons-hammer"></span> <?php _e( 'More Stuff', 'wivm' ); ?></a>
	</h2>

	<?php
	//Display hidden fields and nonces
	settings_fields( 'wivm-settings-group' );

	//Display General settings tab
	$wi_form->form_table_start( 'general' );

		$wi_form->textinput( 	'food', 		'Favorite Food', 	array( 'description' => 'Description for input field.' ) );
    	$wi_form->textinput( 	'color', 		'Favorite Color' );
    	$wi_form->textarea( 	'muchcontent', 	'Lots of Content', 	array( 'rows' => 10, 'description' => 'You can use info here.' ) );
    	$wi_form->checkbox( 	'checkbox_1', 		__( 'Checkbox 1', 'wivm' ), 'Check this box and you\'ll Win' );
    	$wi_form->checkbox( 	'checkbox_2', 		__( 'Checkbox 2', 'wivm' ), 'This box is awesome' );
    	$wi_form->select( 		'select_1', 	'Awesome Select',	array( 'option 1', 'option 2', 'option 2' ), array( 'description' => 'This is a select you want to pick.' ) );

	$wi_form->form_table_end();

	//Display email settings tab
	$wi_form->form_table_start( 'email' );

		$wi_form->textinput( 		'stuff', 			'Stuff', 				array( 'description' => 'Description for input field.' ) );
    	$wi_form->textinput( 		'other_stuff', 	'Other Stuff' );
    	$wi_form->wysiwyg_editor( 	'email_one', 	'Test Email Setting', 	array( 'description' => 'You can use variables here.' ) );
    	$wi_form->wysiwyg_editor( 	'email_two', 	'Test Email Setting 2', array( 'description' => 'You can use info here.' ) );

	$wi_form->form_table_end();

	//Display more-stuff settings tab
	$wi_form->form_table_start( 'more-stuff' );

		$wi_form->textinput( 		'more_stuff', 		'More Stuff', 				array( 'description' => 'Description for input field.' ) );
    	$wi_form->textinput( 		'more_stuff_2', 	'More Stuff 2' );
    	$wi_form->radio( 'radio_options', array( 'Option 1', 'Option 2', 'Option 3' ), 'Awesome Radio Label' );

	$wi_form->form_table_end();


$wi_form->admin_footer();