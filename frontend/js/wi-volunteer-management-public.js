( function( $ ) {

	'use strict';

	// Document ready.
	$( function() {

		/**
		 * Handle submission of volunteer opportunity sign up form including
		 * validation and AJAX processing.
		 */
		$( '#wivm-sign-up-form input[type=submit]' ).click( function( e ) {

			e.preventDefault();
			var $this = $( this ),
				form_valid;

			$( this ).prop( "disabled", true );

			form_valid = validate_sign_up_form();
			if ( form_valid === true ) {

				submit_sign_up_form( $this );

			} else { // Allow submission again if there were errors.

				$this.prop( "disabled", false );
			}
		} );

	});

	/**
	 * Validate the volunteer opportunity sign up form.
	 *
	 * @return {bool} Whether the form is valid.
	 */
	function validate_sign_up_form(){

		var has_errors = false;

		// Show an error and don't submit if the honeypot exists and is filled in.
		var hp = $( '#wivm_hp' );
		if ( hp.length && hp.val() !== '' ) {

			has_errors = true;
		}

		// Make sure each field is filled in and that email addresses are valid.
		$( '#wivm-sign-up-form input[type=text]:not(#wivm_hp), #wivm-sign-up-form input[type=email]' ).each( function() {

			if ( this.value === '' ) {

				$( this ).addClass( 'field-error' );
				has_errors = true;

			} else if ( this.type === 'email' && ! validate_email( this.value ) ) {

				$( this ).addClass( 'field-error' );
				has_errors = true;

			} else {

				$( this ).removeClass( 'field-error' );
			}
		} );

		// If not valid return false.
		if ( has_errors === true ) {

			$( '.volunteer-opp-message.loading, .volunteer-opp-message.success' ).slideUp();
			$( '.volunteer-opp-message.error' ).slideDown();

			return false;

		} else {

			$( '.volunteer-opp-message' ).slideUp();

			return true;
		}
	}

	/**
	 * Validates a provided email address.
	 * 
	 * @param  {string} email The provided email address.
	 * @return {bool}   	  Whether the provided email address is valid.
	 */
	function validate_email( email ){

		var email_regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

		if ( email_regex.test( email ) ) {

			return true;

		} else {

			return false;
		}
	}

	/**
	 * Submit the sign up form for processing on the backend.
	 */
	function submit_sign_up_form( submit_button ){

		// Show messages to user.
		$( '.volunteer-opp-message.error' ).slideUp();
		$( '.volunteer-opp-message.loading' ).slideDown();

		jQuery.post( wivm_ajax.ajaxurl,
			{
				action: 'wivm_sign_up',
				data: $( '#wivm-sign-up-form' ).serialize()
			},
			function( response ){

				$( '.volunteer-opp-message.loading' ).slideUp();
				
				if ( response === 'rsvped' ) { // If submitter was RSVPed successfully.

					$( '.volunteer-opp-message.success' ).slideDown();
					submit_button.prop( "disabled", false );
					track_google_analytics( 'Success' );

				} else if ( response === 'already_rsvped') { // If submitter had already RSVPed.

					$( '.volunteer-opp-message.already-rsvped' ).slideDown();
					submit_button.prop( "disabled", false );
					track_google_analytics( 'Failure: Already Signed Up' );

				} else if ( response === 'rsvp_closed' ) { // If submitter tried to sign up, but there are no spots left.

					$( '.volunteer-opp-message.rsvp-closed' ).slideDown();
					$( '#wivm-sign-up-form' ).slideUp();
					track_google_analytics( 'Failure: No More Open Spots' );
				}
			}
		);
	}

	/**
	 * Track an event within Google Analytics when volunteers sign up.
	 *
	 * @param {string} sign_up_result The result of the sign up attempt.
	 */
	function track_google_analytics( sign_up_result ){
		
		if ( typeof gtag === 'function' ) {

			gtag( 'event', 'volunteer_opportunity_submit', {
				'sign_up_result': sign_up_result,
			} );
		}
	}

})( jQuery );