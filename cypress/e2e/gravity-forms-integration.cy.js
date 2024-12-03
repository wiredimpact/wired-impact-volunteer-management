/**
 * Cypress test for the Wired Impact Volunteer Management Gravity Forms integration.
 *
 * Assumptions:
 * - The Wired Impact Volunteer Management plugin is installed.
 * - The Gravity Forms plugin is installed.
 * - The Gravity Forms CLI plugin is installed and activated.
 * - MailCatcher is used to catch emails sent by the plugin and is accessible via the web.
 */
describe('Gravity Forms Integration', () => {

	before(() => {
		cy.activateVolunteerMgmtGravityFormsPlugins();
	  	cy.deleteSavedPluginSettings();
		cy.createSingleVolunteerOpp();
		cy.trashAllGravityFormsForms();
	  	cy.createVolunteerSignupForm();
		cy.deleteAllMailCatcherEmails();
	});
  
	beforeEach(() => {
		cy.login();
	});

	it('Allows Gravity Forms form to be displayed when the plugin is activated', function() {

		// The Gravity Forms form should show when the default setting is saved as "Custom Form"
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select').select('gravity_forms');
		cy.contains('tr', 'Select a Default Form').find('select').select(this.volunteerSignupFormID);
		cy.contains('input', 'Save Changes').click();
	
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.get('form#gform_' + this.volunteerSignupFormID).should('exist');

		// The Gravity Forms form should show when the volunteer opportunity's setting is saved as "Custom Form"
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select').select('no_form'); // To confirm the default setting isn't used
		cy.contains('input', 'Save Changes').click();
	
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.contains('tr', 'Form Type').find('select').select('gravity_forms');
		cy.contains('tr', 'Select a Form').find('select').select(this.volunteerSignupFormID);
		cy.contains('button', 'Save').click();
		cy.contains('div', 'Post updated').find('a').click();
	
		cy.get('form#gform_' + this.volunteerSignupFormID).should('exist');    
	});

	it('Passes data from Gravity Forms to the volunteer management system, replaces custom merge tags and shows necessary errors', function() {

		// A Volunteer Management feed can be saved
		cy.visit('/wp-admin/admin.php?page=gf_edit_forms&view=settings&id=' + this.volunteerSignupFormID);
		cy.contains('nav a', 'Volunteer Mgmt').click();
		cy.contains('a.button', 'Add New').click();
		cy.get('#feed_name').type('Volunteer Management Feed');
		cy.contains('tr', 'First Name').find('select').select('Name (First)').should('have.value', '1.3');
		cy.contains('tr', 'Last Name').find('select').select('Name (Last)').should('have.value', '1.6');
		cy.contains('tr', 'Phone Number').find('select').select('Phone').should('have.value', '3');
		cy.contains('tr', 'Email').find('select').select('Email').should('have.value', '4');
		cy.contains('button','Save Settings').click();

		// Add custom merge tags to the admin notification
		cy.visit('/wp-admin/admin.php?page=gf_edit_forms&view=settings&subview=notification&id=' + this.volunteerSignupFormID);
		cy.contains('a', 'Admin Notification').click();
		cy.get('.wp-editor-wrap .gform-dropdown--merge-tags > button').click();
		cy.contains('.wp-editor-wrap button', 'Volunteer Opportunity Name').click({ force: true });
		cy.contains('.wp-editor-wrap button', 'Volunteer Opportunity Date & Time').click({ force: true });
		cy.contains('.wp-editor-wrap button', 'Volunteer Opportunity Location').click({ force: true });
		cy.contains('.wp-editor-wrap button', 'Volunteer Opportunity Contact Name').click({ force: true });
		cy.contains('.wp-editor-wrap button', 'Volunteer Opportunity Contact Phone').click({ force: true });
		cy.contains('.wp-editor-wrap button', 'Volunteer Opportunity Contact Email').click({ force: true });
		cy.contains('button','Update Notification').click();

		// Fill in some opportunity details and select to display the Gravity Forms form
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.get('input#contact_name').type('Jamie Taylor');
		cy.get('input#contact_phone').type('(123) 456-7890');
		cy.get('input#contact_email').type('jamie@volunteering.org');
		cy.get('input#location').type('Busch Stadium');
		cy.get('input#street').type('700 Clark Ave');
		cy.get('input#city').type('St. Louis');
		cy.get('input#state').type('MO');
		cy.get('input#zip').type('63102');
		cy.get('input#flexible_frequency').type('Every Wednesday');
		cy.contains('tr', 'Form Type').find('select').select('gravity_forms');
		cy.contains('tr', 'Select a Form').find('select').select(this.volunteerSignupFormID);
		cy.contains('button', 'Save').click();
		cy.contains('div', 'Post updated').find('a').click();
		
		// Errors display when the form is submitted with the name, phone and email fields blank, even though they aren't required
		cy.contains('input[type="submit"]', 'Submit').click();
		cy.contains('fieldset', 'Name').find('div.validation_message').should('have.text', 'This field is required.');
		cy.contains('div.gfield', 'Phone').find('div.validation_message').should('have.text', 'This field is required.');
		cy.contains('div.gfield', 'Email').find('div.validation_message').should('have.text', 'This field is required.');

		// Submitting the form successfully passes data to the volunteer management system, which then shows in the post's edit screen
		cy.contains('fieldset', 'Name').find('.name_first input').type('Abraham');
		cy.contains('fieldset', 'Name').find('.name_last input').type('Lincoln');
		cy.contains('div.gfield', 'Phone').find('input').type('(888) 777-6666');
		cy.contains('div.gfield', 'Email').find('input').type('abraham@usa.gov');
		cy.contains('input[type="submit"]', 'Submit').click();
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.get('.rsvp-list-table table').contains('td[data-colname="Name"]', 'Abraham Lincoln').should('exist');
		cy.get('.rsvp-list-table table').contains('td[data-colname="Email"]', 'abraham@usa.gov').should('exist');
		cy.get('.rsvp-list-table table').contains('td[data-colname="Phone"]', '(888) 777-6666').should('exist');
		
		// A note is added to the Gravity Forms entry when a volunteer signs up successfully
		cy.visit('/wp-admin/admin.php?page=gf_entries');
		cy.contains('a', 'Abraham').click();
		cy.contains('.gforms_note_wired-impact-volunteer-management', 'The volunteer successfully signed up for this opportunity.').should('exist');

		// The admin notification custom merge tags have been replaced correctly
		cy.request('GET', 'http://127.0.0.1:1080/messages/3.source').then((response) => {
			expect(response.status).to.eq(200);
			expect(response.body).to.include('Subject: New submission from Volunteer Signup');
			expect(response.body).to.include('Clean up Trash');
			expect(response.body).to.include('Every Wednesday');
			expect(response.body).to.include('Busch Stadium, 700 Clark Ave, St. Louis, MO 63102');
			expect(response.body).to.include('Jamie Taylor');
			expect(response.body).to.include('(123) 456-7890');
			expect(response.body).to.include('jamie@volunteering.org');
		});

		// An error shows in the Gravity Forms entry when the same volunteer signs up again
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('fieldset', 'Name').find('.name_first input').type('Abraham');
		cy.contains('fieldset', 'Name').find('.name_last input').type('Lincoln');
		cy.contains('div.gfield', 'Phone').find('input').type('(888) 777-6666');
		cy.contains('div.gfield', 'Email').find('input').type('abraham@usa.gov');
		cy.contains('input[type="submit"]', 'Submit').click();
		cy.visit('/wp-admin/admin.php?page=gf_entries');
		cy.contains('a', 'Abraham').click();
		cy.contains('.gforms_note_wired-impact-volunteer-management', 'Error Sending Data to the Volunteer Management System: The volunteer has already signed up for this opportunity.').should('exist');

		// The form is hidden when the max number of volunteers has been reached
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.get('#has-volunteer-limit').check();
		cy.get('#volunteer-limit').type('1');
		cy.contains('button', 'Save').click();
		cy.contains('div', 'Post updated').find('a').click();
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.get('form#gform_' + this.volunteerSignupFormID).should('not.exist');
		cy.contains('p', 'We’re sorry, but we’re no longer accepting new volunteers for this opportunity.').should('exist');
	});

	it('Does not show Gravity Forms settings when the plugin is deactivated', function() {
	  
		cy.visit('/wp-admin/plugins.php');
		cy.get('[data-slug="gravityforms"] .deactivate a').click();

		// The Gravity Forms option should be hidden in the default settings
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select option[value="gravity_forms"]').should('not.exist');
		cy.contains('tr', 'Select a Default Form').should('not.exist');

		// The Gravity Forms option should be hidden in the single opportunity settings
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.contains('tr', 'Form Type').find('select option[value="gravity_forms"]').should('not.exist');
		cy.contains('tr', 'Select a Form').should('not.exist');
	});
});