(function( $ ) {
	'use strict';

	//Document ready
	$(function() {

		/**
		 * Handle submission of volunteer opportunity sign up form inclding validation and AJAX processing.
		 */
		$( '#wivm-sign-up-form input[type=submit]' ).click(function(e){
			e.preventDefault();
			var $this = $( this ),
				form_valid;

			$( this ).prop( "disabled", true );

			form_valid = validate_sign_up_form();
			if( form_valid === true ){
				submit_sign_up_form( $this );
			}
			else { //Allow submission again if there were errors
				$this.prop( "disabled", false );
			}
		});

	});

	/**
	 * Validate the volunteer opportunity sign up form.
	 * @return {bool} Whether the form is valid.
	 */
	function validate_sign_up_form(){
		var has_errors = false;

		//Show an error and don't submit if the honeypot exists and is filled in
		var hp = $( '#wivm_hp' );
		if( hp.length && hp.val() !== '' ){
			has_errors = true;
		}

		//Make sure each field is filled in and that email addresses are valid
		$( '#wivm-sign-up-form input[type=text]:not(#wivm_hp), #wivm-sign-up-form input[type=email]' ).each(function() {
            if( this.value === '' ) {
                $( this ).addClass( 'field-error' );
                has_errors = true;
            }
            else if ( this.type === 'email' && !validate_email( this.value ) ){
            	$( this ).addClass( 'field-error' );
                has_errors = true;
            }
            else {
            	$( this ).removeClass( 'field-error' );
            }
        });

		//If not valid return false.
        if( has_errors === true ){
        	$( '.volunteer-opp-message.loading, .volunteer-opp-message.success' ).slideUp();
        	$( '.volunteer-opp-message.error' ).slideDown();
        	return false;
        }
        else {
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

		if( email_regex.test( email ) ){
        	return true;
        }
        else {
        	return false;
        }
	}

	/**
	 * Submit the sign up form for processing on the backend.
	 */
	function submit_sign_up_form( submit_button ){
		//Show messages to user
		$( '.volunteer-opp-message.error' ).slideUp();
		$( '.volunteer-opp-message.loading' ).slideDown();

		jQuery.post( wivm_ajax.ajaxurl,
			{
				action: 'wivm_sign_up',
				data: $( '#wivm-sign-up-form' ).serialize()
			},
			function( response ){

				$( '.volunteer-opp-message.loading' ).slideUp();
				
				//If submitter was RSVPed successfully
				if( response === 'rsvped' ){
					$( '.volunteer-opp-message.success' ).slideDown();
					submit_button.prop( "disabled", false );
					track_google_analytics( 'RSVP Success' );
				}
				//If submitter had already RSVPed
				else if( response === 'already_rsvped'){
					$( '.volunteer-opp-message.already-rsvped' ).slideDown();
					submit_button.prop( "disabled", false );
					track_google_analytics( 'RSVP Failure: Already RSVPed' );
				}
				//If submitter tried to sign up, but there are no spots left.
				else if( response === 'rsvp_closed' ){
					$( '.volunteer-opp-message.rsvp-closed' ).slideDown();
					$( '#wivm-sign-up-form' ).slideUp();
					track_google_analytics( 'RSVP Failure: No More Open Spots' );
				}
			}
		);
	}

	/**
	 * Track volunteer opportunity action as an event within Google Analytics.
	 *
	 * This only works in Universal Analytics, and does not in Classic Analytics.
	 *
	 * @param {string} action The action that was completed (i.e. "Successful RSVP")
	 */
	function track_google_analytics( action ){
		//Determine global analytics object name
		var ga = window[window['GoogleAnalyticsObject'] || 'ga'];
		if( typeof ga == 'function' ){
			//Track as an event
			ga( 'send', {
				hitType: 		'event',
				eventCategory: 	'Volunteer Opportunity',
				eventAction: 	action
			});
		}
	}


})( jQuery );