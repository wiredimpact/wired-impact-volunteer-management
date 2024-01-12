/**
 * Cypress test for the Wired Impact Volunteer Management plugin settings.
 *
 * Assumptions:
 * - The Wired Impact Volunteer Management plugin is installed and activated.
 * - MailCatcher is used to catch emails sent by the plugin and is accessible via the web.
 */
describe('Plugin and Volunteer Opportunity Settings', () => {

	before(() => {
		cy.deleteSavedPluginSettings();
		cy.createSingleVolunteerOpp();
	});

	beforeEach(() => {
		cy.login();
	});

	it('Removes the admin notice successfully', () => {

		cy.visit('/wp-admin/');
		cy.contains('.notice a', 'Learn how to get started.').click();
		cy.visit('/wp-admin/');
		cy.contains('Learn how to get started.').should('not.exist');
	});

	it('Loads correct defaults for some settings', () => {

		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');

		// General settings
		cy.contains('#wivm-tabs a', 'General').click();
		cy.contains('Yes, please provide basic styling.').find('input').should('be.checked');
		cy.contains('Yes, please use a honeypot to prevent spam.').find('input').should('be.checked');

		// Opportunity Defaults settings
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select').should('have.value', 'built_in_form');

		// Email settings
		cy.contains('#wivm-tabs a', 'Email').click();
		cy.contains('tr', 'Send Volunteer Signup Email?').find('input[type="checkbox"]').should('be.checked');
		cy.contains('tr', 'Volunteer Signup Email Subject').find('input').should('have.value', 'Thanks for Signing Up to Volunteer');
		cy.contains('tr', 'Send Admin Signup Email?').find('input[type="checkbox"]').should('be.checked');
		cy.contains('tr', 'Admin Signup Email Subject').find('input').should('have.value', 'New Volunteer Signup');
		cy.contains('tr', 'Send Volunteer Reminder Email?').find('input[type="checkbox"]').should('be.checked');
		cy.contains('tr', 'Number of Days Before Opportunity to Send Reminder').find('input').should('have.value', '4');
		cy.contains('tr', 'Volunteer Reminder Email Subject').find('input').should('have.value', 'Your Volunteer Opportunity is Coming Up');
	});

	it('Stores and uses General settings', () => {

		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');

		// Deactivate loading plugin styles and the honeypot field
		cy.contains('#wivm-tabs a', 'General').click();
		cy.contains('No, I\'ll code my own styling.').find('input').check();
		cy.contains('No, I\'ll handle spam in my own way.').find('input').check();
		cy.contains('input', 'Save Changes').click();

		// Check that the settings were saved
		cy.contains('No, I\'ll code my own styling.').find('input').should('be.checked');
		cy.contains('No, I\'ll handle spam in my own way.').find('input').should('be.checked');

		// CSS styles should be turned off on the frontend
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('First Name:').should('not.have.css', 'display', 'block');
		cy.get('#wivm_first_name').should('not.have.css', 'display', 'block');

		// Honeypot should be turned off on the frontend
		cy.get('input#wivm_hp').should('not.exist');
	});

	it('Stores and uses Opportunity Defaults settings', () => {

		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');

		// Turn CSS styles back on so we can see the form more easily
		cy.contains('#wivm-tabs a', 'General').click();
		cy.contains('Yes, please provide basic styling.').find('input').check();

		// Add default contact information and a location
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Contact Name').find('input').type('FirstName LastName');
		cy.contains('tr', 'Default Contact Phone Number').find('input').type('(888) 444-7777');
		cy.contains('tr', 'Default Contact Email Address').find('input').type('testing@example.org');

		cy.contains('tr', 'Default Location Name').find('input').type('Busch Stadium');
		cy.contains('tr', 'Default Street').find('input').type('700 Clark Ave');
		cy.contains('tr', 'Default City').find('input').type('St. Louis');
		cy.contains('tr', 'Default State').find('input').type('MO');
		cy.contains('tr', 'Default Zip').find('input').type('63102');

		cy.contains('input', 'Save Changes').click();

		// Check that the settings were saved
		cy.contains('tr', 'Default Contact Name').find('input').should('have.value', 'FirstName LastName');
		cy.contains('tr', 'Default Contact Phone Number').find('input').should('have.value', '(888) 444-7777');
		cy.contains('tr', 'Default Contact Email Address').find('input').should('have.value', 'testing@example.org');

		cy.contains('tr', 'Default Location Name').find('input').should('have.value', 'Busch Stadium');
		cy.contains('tr', 'Default Street').find('input').should('have.value', '700 Clark Ave');
		cy.contains('tr', 'Default City').find('input').should('have.value', 'St. Louis');
		cy.contains('tr', 'Default State').find('input').should('have.value', 'MO');
		cy.contains('tr', 'Default Zip').find('input').should('have.value', '63102');

		// The default contact information and location should automatically populate when creating a new volunteer opportunity
		cy.visit('/wp-admin/edit.php?post_type=volunteer_opp');
		cy.contains('a', 'Add Volunteer Opportunity').click();
		cy.contains('#volunteer-opportunity-details tr', 'Name').find('input').should('have.value', 'FirstName LastName');
		cy.contains('#volunteer-opportunity-details tr', 'Phone Number').find('input').should('have.value', '(888) 444-7777');
		cy.contains('#volunteer-opportunity-details tr', 'Email Address').find('input').should('have.value', 'testing@example.org');

		cy.contains('#volunteer-opportunity-details tr', 'Location Name').find('input').should('have.value', 'Busch Stadium');
		cy.contains('#volunteer-opportunity-details tr', 'Street Address').find('input').should('have.value', '700 Clark Ave');
		cy.contains('#volunteer-opportunity-details tr', 'City').find('input').should('have.value', 'St. Louis');
		cy.contains('#volunteer-opportunity-details tr', 'State').find('input').should('have.value', 'MO');
		cy.contains('#volunteer-opportunity-details tr', 'Zip').find('input').should('have.value', '63102');

		// The default contact information and location should save and show on the frontend when the opportunity is published
		cy.get('h1.wp-block-post-title').click().type('Serve Food to Our Community');
		cy.contains('button', 'Publish').click();
		cy.contains('.editor-post-publish-panel button', 'Publish').click();
		cy.contains('a', 'View Volunteer Opportunity').click();

		cy.contains('span strong', 'Where:').siblings('a').should('have.text', 'Busch Stadium, 700 Clark Ave, St. Louis, MO 63102');
		cy.contains('span strong', 'Contact:').closest('span').should('include.text', 'FirstName LastName');
		cy.contains('span strong', 'Contact Email:').closest('span').should('include.text', 'testing@example.org');
		cy.contains('span strong', 'Contact Phone:').closest('span').should('include.text', '(888) 444-7777');
	});

	it('Displays the correct form based on the settings', function() {

		// The built-in form should show by default when the plugin is first activated
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.get('form#wivm-sign-up-form').should('exist');
		cy.contains('form label', 'First Name:').should('exist');
		cy.contains('form label', 'Last Name:').should('exist');
		cy.contains('form label', 'Phone:').should('exist');
		cy.contains('form label', 'Email:').should('exist');

		// No form should show when the default setting is saved as "No Form"
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select').select('no_form');
		cy.contains('input', 'Save Changes').click();

		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.get('.entry-content form').should('not.exist');

		// The built-in form should show when the volunteer opportunity's setting is saved as "Built-In Signup Form"
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.contains('tr', 'Form Type').find('select').select('built_in_form');
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').find('a').click();

		cy.get('form#wivm-sign-up-form').should('exist');
		cy.contains('form label', 'First Name:').should('exist');
		cy.contains('form label', 'Last Name:').should('exist');
		cy.contains('form label', 'Phone:').should('exist');
		cy.contains('form label', 'Email:').should('exist');

		// No form should show when the volunteer opportunity's setting is saved as "No Form"
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.contains('tr', 'Form Type').find('select').select('no_form');
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').find('a').click();

		cy.get('.entry-content form').should('not.exist');
	});

	it('Doesn\'t send volunteer and admin signup emails if the setting is off', function() {

		// Set the built-in form to show
		cy.visit('/wp-admin/post.php?post=' + this.volunteerOppID + '&action=edit');
		cy.contains('tr', 'Form Type').find('select').select('built_in_form');
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').should('be.visible');

		// Set up an admin email address to receive emails about new signups
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Email').click();
		cy.contains('tr', 'Admin Email Address').find('input').type('volunteer-admin@wiredimpact.com');
		cy.contains('input', 'Save Changes').click();

		// Sign up a volunteer for an opportunity
		cy.deleteAllMailCatcherEmails();
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('form label', 'First Name:').next('input').type('Abraham');
		cy.contains('form label', 'Last Name:').next('input').type('Lincoln');
		cy.contains('form label', 'Phone:').next('input').type('(888) 777-6666');
		cy.contains('form label', 'Email:').next('input').type('abraham@usa.gov');
		cy.contains('form#wivm-sign-up-form input[type="submit"]', 'Express Interest').click();
		cy.contains('div', 'Thanks for signing up. You’ll receive a confirmation email shortly.').should('be.visible');

		cy.request('GET', 'http://127.0.0.1:1080/messages/1.source').then((response) => {
			// The email to the volunteer should be sent
			expect(response.status).to.eq(200);
			expect(response.body).to.include('To: abraham@usa.gov');
			expect(response.body).to.include('Subject: Thanks for Signing Up to Volunteer');
		});
		cy.request('GET', 'http://127.0.0.1:1080/messages/2.source').then((response) => {
			// The email to the admin should be sent
			expect(response.status).to.eq(200);
			expect(response.body).to.include('To: volunteer-admin@wiredimpact.com');
			expect(response.body).to.include('Subject: New Volunteer Signup');
		});

		// Turn off the volunteer and admin signup emails
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Email').click();
		cy.contains('tr', 'Send Volunteer Signup Email?').find('input[type="checkbox"]').uncheck();
		cy.contains('tr', 'Send Admin Signup Email?').find('input[type="checkbox"]').uncheck();
		cy.contains('tr', 'Send Volunteer Reminder Email?').find('input[type="checkbox"]').uncheck();
		cy.contains('input', 'Save Changes').click();

		cy.contains('tr', 'Send Volunteer Signup Email?').find('input[type="checkbox"]').should('not.be.checked');
		cy.contains('tr', 'Send Admin Signup Email?').find('input[type="checkbox"]').should('not.be.checked');
		cy.contains('tr', 'Send Volunteer Reminder Email?').find('input[type="checkbox"]').should('not.be.checked');

		// Sign up a volunteer for an opportunity
		cy.deleteAllMailCatcherEmails();
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('form label', 'First Name:').next('input').type('George');
		cy.contains('form label', 'Last Name:').next('input').type('Washington');
		cy.contains('form label', 'Phone:').next('input').type('(888) 777-6666');
		cy.contains('form label', 'Email:').next('input').type('george@usa.gov');
		cy.contains('form#wivm-sign-up-form input[type="submit"]', 'Express Interest').click();
		cy.contains('div', 'Thanks for signing up. You’ll receive a confirmation email shortly.').should('be.visible');
		cy.request('GET', 'http://127.0.0.1:1080/messages').then((response) => {
			// There should have been no emails sent, so no emails at all stored in MailCatcher
			expect(response.status).to.eq(200);
			expect(response.body).to.have.length(0);
		});
	});
});