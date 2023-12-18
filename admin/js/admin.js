/**
 * Wired Impact Volunteer Management
 * All javascript for admin-specific functionality for the plugin.
 */
(function( $ ) {
    'use strict';

    // Check if a selector/var exists
    $.fn.exists = function() {
        return $(this).length > 0;
    };

    /**
     * For the volunteer management Help & Settings page.
     */
    $(function() {

        if( $( '#wivm-tabs' ).exists() ){

            /**
             * Hide and show options tab content on click
             */
            $('#wivm-tabs').find('a').click(function () {
                $('#wivm-tabs').find('a').removeClass('nav-tab-active');
                $('.wivmtab').removeClass('active');

                var id = $(this).attr('id').replace('-tab', '');
                $('#' + id).addClass('active');
                $(this).addClass('nav-tab-active');

                //Hide Save Changes button on Help tab
                var submit = $( '#wivm-settings-form p.submit' );
                submit.show();
                if( id == 'help' ){
                    submit.hide();
                }
            });

            var wivm_active_tab = window.location.hash.replace('#top-', '');

            if (wivm_active_tab == '' || wivm_active_tab == '#_=_') {
                wivm_active_tab = $('.wivmtab').attr('id');
            }

            $('#' + wivm_active_tab).addClass('active');
            $('#' + wivm_active_tab + '-tab').addClass('nav-tab-active');

            $('.nav-tab-active').click();

            /**
             * Both on document.ready() and when the hash changes, update the form action to
             * include the current tab hash. This ensures the same tab is shown after the form
             * is saved and the page reloads.
             */
            wivm_set_tab_hash();
            $(window).on('hashchange', wivm_set_tab_hash);

            // Show and hide the default form selection field based on whether the user wants to show a Gravity Forms form
            $( '#default_form_type' ).change( function() {
                if ( $( this ).val() == 'gravity_forms' ) {
                    $( '.select-form-field' ).show();
                }
                else {
                    $( '.select-form-field' ).hide();
                }
            });

            // Hide fields specifying email settings if that email is disabled
            $( '#send_signup_email_to_volunteers, #send_signup_email_to_admins, #send_reminder_email_to_volunteers' ).change( function() {
                var relatedFieldRows = $( this ).closest( 'tr' ).nextUntil( 'tr.section-heading' );

                if ( $( this ).is( ':checked' ) ) {
                    relatedFieldRows.show();
                }
                else {
                    relatedFieldRows.hide();
                }
            });

        } //end if

        function wivm_set_tab_hash() {
            var settings = $('#wivm-settings-form');
            if (settings.length) {
                var currentUrl = settings.attr('action').split('#')[0];
                settings.attr('action', currentUrl + window.location.hash);
            }
        }

    }); //document.ready()

    /**
     * For volunteer opportunity edit screen including jQuery Timepicker.
     */
    $(function() {

        // Only run on WI Volunteer Management Opportunity New and Edit screens.
        if( typeof pagenow != 'undefined' && pagenow == 'volunteer_opp' ) {
        
            // Show and hide one-time volunteer opportunity fields
            $('#one-time-opportunity').change(function() {
                if( this.checked ) {
                    $( '.one-time-field' ).show();
                    $( '.flexible-field' ).hide();
                }
                else {
                    $( '.one-time-field' ).hide();
                    $( '.flexible-field' ).show();
                }
            });

            // Show and hide fields if there is a limit on the number of volunteers
            $('#has-volunteer-limit').change(function() {
                if( this.checked ) {
                    $( '.volunteer-limit-field' ).show();
                }
                else {
                    $( '.volunteer-limit-field' ).hide();
                }
            });

            // Show and hide the form selection field based on whether the user wants to show a Gravity Forms form
            $( '#form_type' ).change( function() {
                if ( $( this ).val() == 'gravity_forms' ) {
                    $( '.select-form-field' ).show();
                }
                else {
                    $( '.select-form-field' ).hide();
                }
            });

            var start_date_time = jQuery( '#volunteer-opportunity-details #start-date-time-output' ),
                start_date_time_save = jQuery( '#volunteer-opportunity-details #start-date-time' ),
                end_date_time = jQuery( '#volunteer-opportunity-details #end-date-time-output' ),
                end_date_time_save = jQuery( '#volunteer-opportunity-details #end-date-time' ),
                end_date_time_error = end_date_time.siblings( '.error' ),
                date_format = wivm_ajax.datepicker_date_format,
                time_format = wivm_ajax.datepicker_time_format,
                separator_symbol = wivm_ajax.datepicker_separator,
                show_second = false,
                alt_date_format = 'yy-mm-dd',
                alt_time_format = 'HH:mm:ss';

            //Set the end date & time field to match the start date and time if the end is empty.
            //Only do this when focusing out on start time.
            start_date_time.datetimepicker({
                controlType: 'select',
                oneLine: true,
                dateFormat: date_format,
                timeFormat: time_format,
                separator: separator_symbol,
                showSecond: show_second,
                altField: start_date_time_save,
                altFieldTimeOnly: false,
                altFormat: alt_date_format,
                altTimeFormat: alt_time_format,
                stepMinute: 5,
                onClose: function( dateText, inst ) {
                    // Get the start date and convert it to a unix timestamp
                    var start_date_unix = get_datetimepicker_timestamp( start_date_time );
                    // Set the hidden field's value with the unix timestamp
                    start_date_time_save.val( start_date_unix );

                    if ( end_date_time.val() != '' ) {
                        var end_date_unix = get_datetimepicker_timestamp( end_date_time );

                        // Compare the start date with the end date to be sure it doesn't end before it begins
                        if ( start_date_unix > end_date_unix ){
                            end_date_time.val( dateText );
                            end_date_time_save.val( start_date_unix );
                        }
                    }
                    else {
                        end_date_time.val( dateText );
                        end_date_time_save.val( start_date_unix );
                    }
                 }
            });

            end_date_time.datetimepicker({
                controlType: 'select',
                oneLine: true,
                dateFormat: date_format,
                timeFormat: time_format,
                separator: separator_symbol,
                showSecond: show_second,
                altField: end_date_time_save,
                altFieldTimeOnly: false,
                altFormat: alt_date_format,
                altTimeFormat: alt_time_format,
                stepMinute: 5,
                onClose: function( dateText, inst ) {
                    // Get the end date and convert it to a unix timestamp
                    var end_date_unix = get_datetimepicker_timestamp( end_date_time );
                    // Set the hidden field's value with the unix timestamp
                    end_date_time_save.val( end_date_unix );

                    if ( start_date_time.val() != '' ) {
                        var start_date_unix = get_datetimepicker_timestamp( start_date_time );

                        // Compare the start date with the end date to be sure it doesn't end before it begins
                        if ( start_date_unix > end_date_unix ){
                            start_date_time.val( dateText );
                            start_date_time_save.val( end_date_unix );
                        }
                    }
                    else {
                        start_date_time.val( dateText );
                        start_date_time_save.val( end_date_unix );
                    }
                 }
            });

            /**
             * Generate a unix timestamp from the datetime picker's formatted date and time
             * 
             * @param  {obj} datetime_field jQuery object for datetime picker
             * @return {int}                unix timestamp
             */
            function get_datetimepicker_timestamp( datetime_field ){
                var datetime_field_formatted = datetime_field.datetimepicker( 'getDate' );
                var datetime_unix = datetime_field_formatted.getTime() / 1000 - ( datetime_field_formatted.getTimezoneOffset() * 60 );

                return datetime_unix;
            }
            
        } //end if

        /**
         * Turn an opportunity RSVP from on to off for an individual volunteer.
         * Happens on wp-admin/admin.php?page=wi-volunteer-management-volunteer and within
         * the edit screen for individual volunteer opportunities.
         */
        $( '.opps .opp, #volunteer-opportunity-rsvps' ).on( 'click', '.remove-rsvp', function() {

            var remove_rsvp_button = $( this ),
                post_id = remove_rsvp_button.data( 'post-id' ),
                user_id = remove_rsvp_button.data( 'user-id' ),
                //button_id is set to the user_id when on an opportunity, and post_id when on the volunteer's page since each will be unique.
                button_id = ( remove_rsvp_button.closest( '#volunteer-opportunity-rsvps' ).length == 1 ) ? user_id : post_id;

            remove_rsvp_button.pointer( {
                content: wivm_ajax.remove_rsvp_pointer_text,
                position: {
                    edge: 'top',
                    align: 'right'
                },
                buttons: function (event, t) {
                            var button = $('<a id="pointer-close-' + button_id + '" style="margin:0 5px;" class="button-secondary">' + wivm_ajax.remove_rsvp_cancel_text + '</a>');
                            button.bind('click.pointer', function () {
                                t.element.pointer('close');
                            });
                            return button;
                }
            }).pointer( 'open' );

            $( '#pointer-close-' + button_id ).after( '<a id="pointer-primary-' + button_id + '" data-id="' + button_id + '" class="button-primary">' + wivm_ajax.remove_rsvp_confirm_text + '</a>' );
            $( '#pointer-primary-' + button_id ).click(function() {

                var pointer_remove_button = $( this );

                $.post( ajaxurl,
                    {
                        action: 'wivm_remove_rsvp',
                        data: {
                            post_id: post_id,
                            user_id: user_id,
                            nonce: wivm_ajax.remove_user_rsvp_nonce
                        }
                    },
                    function( response ){
                        if( response == 1 ){ //Success
                            remove_rsvp_button.fadeOut().siblings( 'h3' ).addClass( 'removed' ); //For individual volunteer page
                            remove_rsvp_button.fadeOut().parent( 'td' ).siblings().addClass( 'removed' ); //For individual opportunity page
                            pointer_remove_button.closest( '.wp-pointer' ).hide();
                        }
                        else { //Failure
                            pointer_remove_button.addClass( 'error' ).text( wivm_ajax.remove_rsvp_error_text );
                        }
                    }
                );

            }); //Pointer remove-rsvp click

        }); //.remove-rsvp click


        /**
         * Hide our admin notices for good by using AJAX to store that it has been clicked.
         */
        $( '.wivm-notice' ).on('click', '.notice-dismiss, a', function( event ){

            $.post( ajaxurl, {
                action: 'wivm_hide_notice',
                data: {
                    notice_id: $( this ).closest( '.wivm-notice' ).attr( 'id' ),
                    nonce: wivm_ajax.hide_notice_nonce
                }
            });

        });

        /**
         * Send custom email to volunteers using AJAX
         *
         * Logic to show messages and send email must cover four cases:
         * 1. subject is valid/message is not
         * 2. message is valid/subject is not
         * 3. subject and message are both not valid
         * 4. subject and message are both valid
         */
        var email_editor = $( '.volunteer-email-editor' );

        if ( $( email_editor ).exists() ) {

            $( '.wivm-send-email' ).on( 'click', function( event ) {
                event.preventDefault();

                var $this = $( this ),
                    errors      = [],
                    is_valid    = true,
                    button_text = $this.text(),
                    post_id     = $this.data( 'post-id' ),
                    user_id     = $this.data( 'user-id' );

                var subject_id    = $( '#volunteer-email-subject' ),
                    subject_value = subject_id.val(),
                    subject_error = wivm_ajax.volunteer_email_subject_error,
                    editor_id     = '',
                    editor_value;

                // Get content if visual editor is selected, otherwise fall back to textarea
                if ( $( '#wp-volunteer-email-editor-wrap' ).is( '.tmce-active' ) ) {
                    editor_value = tinyMCE.activeEditor.getContent();
                    editor_id    = '#wp-volunteer-email-editor-wrap';
                } else {
                    editor_value = $( '#volunteer-email-editor' ).val();
                    editor_id    = '#volunteer-email-editor';
                }

                // Check that the subject field is not empty
                if ( ! subject_value ) {
                    is_valid = false;
                    errors.push( 'subject-error' );
                }

                // Check that the editor field is not empty
                if ( editor_value == '' ) {
                    is_valid = false;
                    errors.push( 'editor-error' );
                }

                if ( ! is_valid ) {
                    event.preventDefault();

                    var error_count   = errors.length,
                        error_message = '';

                    // Clear the error class on keyup
                    $( subject_id ).keyup( function() {
                       $( this ).removeClass( 'has-error' );
                    });

                    // Set the subject error output
                    if ( $.inArray( 'subject-error', errors ) !== -1 ) {
                        error_message += '<p>' + wivm_ajax.volunteer_email_subject_error + '</p>';
                        $( subject_id ).addClass( 'has-error' );
                    }

                    // Set the editor error output
                    if ( $.inArray( 'editor-error', errors ) !== -1 ) {
                        error_message += '<p>' + wivm_ajax.volunteer_email_editor_error + '</p>';
                    }

                    // Display the error message(s)
                    $( '.volunteer-email-failure' )
                        .html( error_message )
                        .show()
                        .fadeTo( 300, 1 )
                        .addClass( 'is-open' );
                }

                // Process and send the email if the subject and editor values are valid
                if ( is_valid ) {

                    $this.prop( 'disabled', true ).text( 'Sending...' );

                    $.post( ajaxurl,
                        {
                            action: 'wivm_process_email',
                            data: {
                                post_id: post_id,
                                user_id: user_id,
                                nonce: wivm_ajax.volunteer_email_nonce,
                                subject: subject_value,
                                message: editor_value
                            },
                        },
                        function( response ) {

                            $this.prop( 'disabled', false ).text( button_text );

                            // Hide any previously open response messages
                            if ( $( '.volunteer-email-response-message' ).is( '.is-open' ) ) {
                                $( '.volunteer-email-response-message' ).hide().removeClass( 'is-open' );
                            }

                            if ( response == 'success' ) {
                                $( '.volunteer-email-success' )
                                    .html( '<p>' + wivm_ajax.volunteer_email_success_text + '</p>' )
                                    .show()
                                    .fadeTo( 300, 1 )
                                    .addClass( 'is-open' );
                            }
                            // Failure
                            else {
                                $( '.volunteer-email-failure' )
                                    .html( '<p>' + wivm_ajax.volunteer_email_error_text + '</p>' )
                                    .show()
                                    .fadeTo( 300, 1 )
                                    .addClass( 'is-open' );
                            }
                        }
                    );
                }
            });
        }

    }); //document.ready()

})( jQuery );