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
	cy.exec(Cypress.env('wpCLIPrefix') + ' post create --post_type=volunteer_opp --post_title="Clean up Trash" --post_status="publish"').then((result) => {
		
		let stdOutParts = result.stdout.slice(0, -1);
		stdOutParts     = stdOutParts.split(' ');
		const volunteerOppID = stdOutParts[stdOutParts.length - 1];

		// Store the post ID so we can use it throughout our tests.
		cy.wrap(volunteerOppID).as('volunteerOppID');
	});
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

/**
 * Trash all existing Gravity Forms forms using WP-CLI.
 *
 * This command requires the Gravity Forms CLI plugin be installed
 * and activated.
 */
Cypress.Commands.add('trashAllGravityFormsForms', () => {
	
	cy.exec(Cypress.env('wpCLIPrefix') + ' gf form delete $(' + Cypress.env('wpCLIPrefix') + ' gf form list --format=ids)');
});

/**
 * Create a Gravity Forms Volunteer Signup form to use for testing.
 *
 * The JSON for the form was created by exporting the form from a website.
 *
 * This command requires the Gravity Forms CLI plugin be installed
 * and activated.
 */
Cypress.Commands.add('createVolunteerSignupForm', () => {

	const formJSON = {"fields":[{"type":"name","id":1,"formId":6,"label":"Name","adminLabel":"","isRequired":false,"size":"large","errorMessage":"","visibility":"visible","nameFormat":"advanced","inputs":[{"id":"1.2","label":"Prefix","name":"","autocompleteAttribute":"honorific-prefix","choices":[{"text":"Dr.","value":"Dr."},{"text":"Miss","value":"Miss"},{"text":"Mr.","value":"Mr."},{"text":"Mrs.","value":"Mrs."},{"text":"Ms.","value":"Ms."},{"text":"Mx.","value":"Mx."},{"text":"Prof.","value":"Prof."},{"text":"Rev.","value":"Rev."}],"isHidden":true,"inputType":"radio"},{"id":"1.3","label":"First","name":"","autocompleteAttribute":"given-name"},{"id":"1.4","label":"Middle","name":"","autocompleteAttribute":"additional-name","isHidden":true},{"id":"1.6","label":"Last","name":"","autocompleteAttribute":"family-name"},{"id":"1.8","label":"Suffix","name":"","autocompleteAttribute":"honorific-suffix","isHidden":true}],"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputMaskIsCustom":"","maxLength":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","enableAutocomplete":false,"autocompleteAttribute":"","choices":"","conditionalLogic":"","productField":"","layoutGridColumnSpan":"","enableEnhancedUI":0,"layoutGroupId":"b7373b44","fields":"","displayOnly":""},{"type":"phone","id":3,"formId":6,"label":"Phone","adminLabel":"","isRequired":false,"size":"large","errorMessage":"","visibility":"visible","inputs":null,"phoneFormat":"international","autocompleteAttribute":"tel","description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputMaskIsCustom":"","maxLength":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","enableAutocomplete":false,"choices":"","conditionalLogic":"","productField":"","layoutGridColumnSpan":12,"enableEnhancedUI":0,"layoutGroupId":"33f7781e","fields":"","displayOnly":""},{"type":"email","id":4,"formId":6,"label":"Email","adminLabel":"","isRequired":false,"size":"large","errorMessage":"","visibility":"visible","inputs":null,"autocompleteAttribute":"email","description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputMaskIsCustom":"","maxLength":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","enableAutocomplete":false,"choices":"","conditionalLogic":"","productField":"","layoutGridColumnSpan":12,"emailConfirmEnabled":"","enableEnhancedUI":0,"layoutGroupId":"9467c1ea","fields":"","displayOnly":""},{"type":"select","id":6,"formId":6,"label":"T-Shirt Size","adminLabel":"","isRequired":false,"size":"large","errorMessage":"","visibility":"visible","validateState":true,"inputs":null,"choices":[{"text":"Extra Small","value":"Extra Small","isSelected":false,"price":""},{"text":"Small","value":"Small","isSelected":false,"price":""},{"text":"Medium","value":"Medium","isSelected":false,"price":""},{"text":"Large","value":"Large","isSelected":false,"price":""},{"text":"Extra Large","value":"Extra Large","isSelected":false,"price":""}],"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputMaskIsCustom":false,"maxLength":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","enableAutocomplete":false,"autocompleteAttribute":"","conditionalLogic":"","productField":"","layoutGridColumnSpan":12,"enablePrice":"","enableEnhancedUI":0,"layoutGroupId":"73e95759","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"errors":[],"fields":""},{"type":"textarea","id":5,"formId":6,"label":"Why do you want to volunteer with us?","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","visibility":"visible","inputs":null,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputMaskIsCustom":false,"maxLength":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","cssClass":"","inputName":"","noDuplicates":false,"defaultValue":"","enableAutocomplete":false,"autocompleteAttribute":"","choices":"","conditionalLogic":"","productField":"","layoutGridColumnSpan":12,"form_id":"","useRichTextEditor":false,"enableEnhancedUI":0,"layoutGroupId":"a96865a7","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"errors":[],"fields":""}],"button":{"type":"text","text":"","imageUrl":"","width":"auto","location":"bottom","layoutGridColumnSpan":12},"title":"Volunteer Signup","description":"","version":"2.7.17.1","id":6,"markupVersion":2,"nextFieldId":7,"useCurrentUserAsAuthor":true,"postContentTemplateEnabled":false,"postTitleTemplateEnabled":false,"postTitleTemplate":"","postContentTemplate":"","lastPageButton":null,"pagination":null,"firstPageCssClass":null,"confirmations":[{"id":"65734ead4e58d","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for signing up to volunteer!","url":"","pageId":"","queryString":"","event":"","disableAutoformat":false,"page":"","conditionalLogic":[]}],"notifications":[{"id":"65734ead4e18f","isActive":true,"to":"{admin_email}","name":"Admin Notification","event":"form_submission","toType":"email","subject":"New submission from {form_title}","message":"{all_fields}"}]};
	
	cy.exec(Cypress.env('wpCLIPrefix') + ' gf form create "Volunteer Signup" "" --form-json=\'' + JSON.stringify(formJSON) + '\'').then((result) => {
		
		const stdOutParts = result.stdout.split(' ');
		const formID      = stdOutParts[stdOutParts.length - 1];

		// Store the form ID so we can use it throughout our tests.
		cy.wrap(formID).as('volunteerSignupFormID');
	});
});

/**
 * Delete all emails from the MailCatcher inbox.
 *
 * MailCatcher is included by default in WP Local Docker.
 *
 * @see https://mailcatcher.me/
 * @see https://github.com/10up/wp-local-docker-v2
 */
Cypress.Commands.add('deleteAllMailCatcherEmails', () => {
	
	cy.request('DELETE', 'http://127.0.0.1:1080/messages').then((response) => {
		expect(response.status).to.eq(204);
	});
});

/**
 * Get the WordPress iframe content editor so we can test inside of it.
 *
 * You must also set chromeWebSecurity to false in cypress.config.js
 * to work with iframes.
 * 
 * @see https://github.com/cypress-io/cypress-example-recipes/tree/master/examples/blogs__iframes
 * @see https://on.cypress.io/wrap
 */
Cypress.Commands.add('getBlockEditorIFrameBody', () => {
	
	cy.log('getBlockEditorIFrameBody');

  return cy
	  .get('iframe[name="editor-canvas"]', { log: false })
	  .its('0.contentDocument.body', { log: false }).should('not.be.empty')
	  .then((body) => cy.wrap(body, { log: false }))
});