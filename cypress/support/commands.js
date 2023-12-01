// ***********************************************
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************

/**
 * Log in to the WordPress admin.
 */
Cypress.Commands.add('login', () => {

	cy.request({
		method: 'POST',
		url: Cypress.config('baseUrl') + '/wp-login.php',
		form: true, // indicates the body should be form urlencoded and sets Content-Type: application/x-www-form-urlencoded headers
		body: {
			log: Cypress.env('username'),
			pwd: Cypress.env('password'),
			rememberme: 'forever'
		}
	});
});

/**
 * Log out of the WordPress admin.
 */
Cypress.Commands.add('logout', () => {

	cy.get('#wp-admin-bar-logout > a').click({ force: true });
	cy.contains('p.message', 'You are now logged out').should('exist');
});

/**
 * Delete the plugin's stored settings using WP-CLI.
 */
Cypress.Commands.add('deleteSavedPluginSettings', () => {

	cy.exec(Cypress.env('wpCLIPrefix') + ' option delete wivm-settings');
});

/**
 * Delete existing volunteer opportunities and create one for testing using WP-CLI.
 */
Cypress.Commands.add('createSingleVolunteerOpp', () => {

	cy.exec(Cypress.env('wpCLIPrefix') + ' post delete $(' + Cypress.env('wpCLIPrefix') + ' post list --post_type=volunteer_opp --format=ids) --force');
	cy.exec(Cypress.env('wpCLIPrefix') + ' post create --post_type=volunteer_opp --post_title="Clean up Trash" --post_status="publish"');
});

/**
 * Activate the Wired Impact Volunteer Management and Gravity Forms plugins using WP-CLI.
 */
Cypress.Commands.add('activateVolunteerMgmtGravityFormsPlugins', () => {

	cy.exec(Cypress.env('wpCLIPrefix') + ' plugin activate wired-impact-volunteer-management gravityforms');
});

/**
 * Delete the test volunteer user using WP-CLI.
 */
Cypress.Commands.add('deleteTestVolunteerUser', () => {

	cy.exec(Cypress.env('wpCLIPrefix') + ' user delete abraham@usa.gov --yes');
});