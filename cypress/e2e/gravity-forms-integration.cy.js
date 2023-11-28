/**
 * Cypress test for the Wired Impact Volunteer Management Gravity Forms integration.
 *
 * Assumptions:
 * - The Wired Impact Volunteer Management plugin is installed.
 * - The Gravity Forms plugin is installed.
 * - A Gravity Forms form ID for volunteer signups is set in the Cypress config file.
 */
describe('Gravity Forms Integration', () => {

	before(() => {
		cy.activateVolunteerMgmtGravityFormsPlugins();
	  	cy.deleteSavedPluginSettings();
		cy.createSingleVolunteerOpp();
	});
  
	beforeEach(() => {
		cy.login();
	});

	it('Allows Gravity Forms form to be used when the plugin is activated', () => {

		// The Gravity Forms form should show when the default setting is saved as "Custom Form"
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select').select('gravity_forms');
		cy.contains('tr', 'Select a Default Form').find('select').select(Cypress.env('gravityFormsSignupFormID'));
		cy.contains('input', 'Save Changes').click();
	
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.get('form#gform_' + Cypress.env('gravityFormsSignupFormID')).should('exist');

		// The Gravity Forms form should show when the volunteer opportunity's setting is saved as "Custom Form"
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select').select('no_form'); // To confirm the default setting isn't used
		cy.contains('input', 'Save Changes').click();
	
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('a', 'Edit Volunteer Opportunity').click();
		cy.contains('tr', 'Form Type').find('select').select('gravity_forms');
		cy.contains('tr', 'Select a Form').find('select').select(Cypress.env('gravityFormsSignupFormID'));
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').find('a').click();
	
		cy.get('form#gform_' + Cypress.env('gravityFormsSignupFormID')).should('exist');    
	});

	it('Does not show Gravity Forms settings when the plugin is deactivated', () => {
	  
		cy.visit('/wp-admin/plugins.php');
		cy.get('[data-slug="gravityforms"] .deactivate a').click();

		// The Gravity Forms option should be hidden in the default settings
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-help-settings');
		cy.contains('#wivm-tabs a', 'Opportunity Defaults').click();
		cy.contains('tr', 'Default Form Type').find('select option[value="gravity_forms"]').should('not.exist');
		cy.contains('tr', 'Select a Default Form').should('not.exist');

		// The Gravity Forms option should be hidden in the single opportunity settings
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('a', 'Edit Volunteer Opportunity').click();
		cy.contains('tr', 'Form Type').find('select option[value="gravity_forms"]').should('not.exist');
		cy.contains('tr', 'Select a Form').should('not.exist');
	});
});