(function( $ ) {
	'use strict';

	//Document ready
	$(function() {

		/**
		 * Handle submission of volunteer opportunity sign up form inclding validation and AJAX processing.
		 */
		$( '#wivm-sign-up-form input[type=submit]' ).click(function(e){
			e.preventDefault();
			var form_valid;

			form_valid = validate_sign_up_form();
			if( form_valid === true ){
				submit_sign_up_form();
			}
		});

	});

	/**
	 * Validate the volunteer opportunity sign up form.
	 * @return {bool} Whether the form is valid.
	 */
	function validate_sign_up_form(){
		var has_errors = false;
		$( '#wivm-sign-up-form input[type=text], #wivm-sign-up-form input[type=email]' ).each(function() {
            if( this.value === '' ) {
                console.log( 'Field left blank' );
                $( this ).addClass( 'error' );
                has_errors = true;
            }
            else if ( this.type === 'email' && !validate_email( this.value ) ){
            	console.log( 'Invalid Email address' );
            	$( this ).addClass( 'error' );
                has_errors = true;
            }
            else {
            	$( this ).removeClass( 'error' );
            }
        });

		//If not valid return false.
        if( has_errors === true ){
        	console.log( 'Form has errors!' );
        	$( '.volunteer_opp .error-message' ).slideDown();
        	return false;
        }
        else {
        	console.log( 'Form valid!' );
        	$( '.volunteer_opp .error-message' ).slideUp();
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
	 * 
	 * @return {[type]} [description]
	 */
	function submit_sign_up_form(){
		console.log( 'Submitting sign up form to server!' );
		jQuery.post( wivm_ajax.ajaxurl,
			{
				action: 'wivm_sign_up',
				data: $( '#wivm-sign-up-form' ).serialize()
			},
			function( response_from_the_action_function ){
				console.log( response_from_the_action_function );
			}
		);
	}

})( jQuery );