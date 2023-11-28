/**
 * Cypress test for the Wired Impact Volunteer Management volunteer sign up.
 *
 * Assumptions:
 * - The Wired Impact Volunteer Management plugin is installed and activated.
 */
describe('Volunteer Sign Up', () => {

	before(() => {
	  	cy.deleteSavedPluginSettings();
		cy.deleteTestVolunteerUser();
		cy.createSingleVolunteerOpp();
	});

	it('Allows volunteers to sign up, be viewed in the admin, and removed from opportunities', () => {

		// Submit the form to sign up as a volunteer
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('form label', 'First Name:').next('input').type('Abraham');
		cy.contains('form label', 'Last Name:').next('input').type('Lincoln');
		cy.contains('form label', 'Phone:').next('input').type('(888) 777-6666');
		cy.contains('form label', 'Email:').next('input').type('abraham@usa.gov');

		cy.contains('form#wivm-sign-up-form input[type="submit"]', 'Express Interest').click();
		cy.contains('div', 'Thanks for signing up. You’ll receive a confirmation email shortly.').should('exist');

		// Save the volunteer opportunity that was created with WP-CLI to ensure all meta is stored
		cy.login();
		cy.visit('/volunteer-opportunity/clean-up-trash/');
		cy.contains('a', 'Edit Volunteer Opportunity').click();
		cy.contains('button','Update').click();
		cy.contains('div', 'Post updated').should('exist');

		// Check that the volunteer user was created and given the right role
		cy.visit('/wp-admin/users.php');
		cy.contains('td.username', 'abraham@usa.gov')
			.should('exist')
			.closest('tr')
			.contains('td.role', 'Volunteer');

		// Check that the volunteer shows on the Volunteers page and has the right information
		cy.visit('/wp-admin/admin.php?page=wi-volunteer-management-volunteers');
		cy.contains('td.name', 'Abraham Lincoln').should('exist');
		cy.contains('td.email', 'abraham@usa.gov').should('exist');
		cy.contains('td.phone', '(888) 777-6666').should('exist');
		cy.contains('tr', 'Abraham Lincoln').find('.num_volunteer_opps').contains('1').should('exist');

		// Check that the volunteer profile page shows the right information
		cy.contains('a', 'Abraham Lincoln').click();
		cy.contains('h1', 'Volunteer: Abraham Lincoln').should('exist');
		cy.contains('.contact-info span', 'E-mail: abraham@usa.gov').should('exist');
		cy.contains('.contact-info span', 'Phone: (888) 777-6666').should('exist');
		cy.get('.opps a').contains('Clean up Trash').should('exist');

		// Check that the volunteer shows on the opportunity's edit screen
		cy.get('.opps a').contains('Clean up Trash').click();
		cy.contains('a', 'Abraham Lincoln').should('exist');
		
		// Removes the volunteer from the opportunity when the "Remove RSVP" button is used
		cy.contains('a.remove-rsvp', 'Remove RSVP').click();
		cy.contains('div', 'Are you sure you want to remove their RSVP for this opportunity?').should('exist');
		cy.contains('a.button-primary', 'Remove RSVP').click();
		cy.reload();
		cy.contains('a', 'Abraham Lincoln').should('not.exist');
	});
});