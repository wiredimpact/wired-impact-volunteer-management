/**
 * Cypress test for the Wired Impact Volunteer Management plugin settings.
 *
 * Assumptions:
 * - The Wired Impact Volunteer Management plugin is installed and activated.
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
		cy.contains('tr', 'Volunteer Signup Email Subject').find('input').should('have.value', 'Thanks for Signing Up to Volunteer');
		cy.contains('tr', 'Admin Signup Email Subject').find('input').should('have.value', 'Volunteer Signup Submission');
		cy.contains('tr', 'Number of Days Prior to Opportunity to Send Reminder').find('input').should('have.value', '4');
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

	it('Stores and uses Opportunity Details settings', () => {

		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');

		// Turn CSS styles back on so we can see the form more easily
		cy.contains('#wivm-tabs a', 'General').click();
		cy.contains('Yes, please provide basic styling.').find('input').check();

		// Add default contact information and a location
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Contact Name').find('input').type('FirstName LastName');
		cy.contains('tr', 'Default Contact Phone').find('input').type('(888) 444-7777');
		cy.contains('tr', 'Default Contact Email').find('input').type('testing@example.org');

		cy.contains('tr', 'Default Location Name').find('input').type('Busch Stadium');
		cy.contains('tr', 'Default Street').find('input').type('700 Clark Ave');
		cy.contains('tr', 'Default City').find('input').type('St. Louis');
		cy.contains('tr', 'Default State').find('input').type('MO');
		cy.contains('tr', 'Default Zip').find('input').type('63102');

		cy.contains('input', 'Save Changes').click();

		// Check that the settings were saved
		cy.contains('tr', 'Default Contact Name').find('input').should('have.value', 'FirstName LastName');
		cy.contains('tr', 'Default Contact Phone').find('input').should('have.value', '(888) 444-7777');
		cy.contains('tr', 'Default Contact Email').find('input').should('have.value', 'testing@example.org');

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
		cy.contains('#volunteer-opportunity-details tr', 'Email').find('input').should('have.value', 'testing@example.org');

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

	it('Displays the correct form based on the settings', () => {

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
		cy.contains('a', 'Edit Volunteer Opportunity').click();
		cy.contains('tr', 'Form Type').find('select').select('built_in_form');
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').find('a').click();

		cy.get('form#wivm-sign-up-form').should('exist');
		cy.contains('form label', 'First Name:').should('exist');
		cy.contains('form label', 'Last Name:').should('exist');
		cy.contains('form label', 'Phone:').should('exist');
		cy.contains('form label', 'Email:').should('exist');

		// No form should show when the volunteer opportunity's setting is saved as "No Form"
		cy.contains('a', 'Edit Volunteer Opportunity').click();
		cy.contains('tr', 'Form Type').find('select').select('no_form');
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').find('a').click();

		cy.get('.entry-content form').should('not.exist');
	});
});