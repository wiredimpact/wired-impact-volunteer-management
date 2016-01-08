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

    $(function() {

        //Only run on WI Volunteer Management settings page.
        if( typeof pagenow != 'undefined' && pagenow == 'volunteer-mgmt_page_wi-volunteer-management-help-settings' ){

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

            var wivm_active_tab = window.location.hash.replace('#top#', '');

            if (wivm_active_tab == '' || wivm_active_tab == '#_=_') {
                wivm_active_tab = $('.wivmtab').attr('id');
            }

            $('#' + wivm_active_tab).addClass('active');
            $('#' + wivm_active_tab + '-tab').addClass('nav-tab-active');

            $('.nav-tab-active').click();

            /**
             * When the hash changes, get the base url from the action and then add the current hash
             */
            $(window).on('hashchange', wivm_set_tab_hash);

            /**
             * When the hash changes, get the base url from the action and then add the current hash
             */
            $(document).on('ready', wivm_set_tab_hash);

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
     * For volunteer opportunity edit screen including jQuery Timepicker
     */
    
    $(function() {
    
        //Show and hide one-time volunteer opportunity fields
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

        //Show and hide fields if there is a limit on the number of volunteers
        $('#has-volunteer-limit').change(function() {
            if( this.checked ) {
                $( '.volunteer-limit-field' ).show();
            }
            else {
                $( '.volunteer-limit-field' ).hide();
            } 
        });


        var start_date_time = jQuery( '#volunteer-opportunity-details #start-date-time' ),
            end_date_time = jQuery( '#volunteer-opportunity-details #end-date-time' ),
            end_date_time_error = end_date_time.siblings( '.error' );

        //Set the end date & time field to match the start date and time if the end is empty.
        //Only do this when focusing out on start time.
        start_date_time.datetimepicker({
            controlType: 'select',
            oneLine: true,
            dateFormat: "D, MM dd, yy",
            timeFormat: "h:mm tt",
            separator: ' @ ',
            stepMinute: 5,
            onClose: function( dateText, inst ) {
                if ( end_date_time.val() != '' ) {
                    var test_start_date = start_date_time.datetimepicker( 'getDate' );
                    var test_end_date = end_date_time.datetimepicker( 'getDate' );
                    if ( test_start_date > test_end_date ){
                        end_date_time.datetimepicker( 'setDate', test_start_date );
                    }
                }
                else {
                    end_date_time.val( dateText );
                }
             }
        }); 

        end_date_time.datetimepicker({
            controlType: 'select',
            oneLine: true,
            dateFormat: "D, MM dd, yy",
            timeFormat: "h:mm tt",
            separator: ' @ ',
            stepMinute: 5,
            onClose: function( dateText, inst ) {
                if ( start_date_time.val() != '' ) {
                    var test_start_date = start_date_time.datetimepicker( 'getDate' );
                    var test_end_date = end_date_time.datetimepicker( 'getDate' );
                    if ( test_start_date > test_end_date ){
                        start_date_time.datetimepicker( 'setDate', test_end_date );
                    }
                }
                else {
                    start_date_time.val( dateText );
                }
             }
        });


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

        // Set the var for the email editor container
        var email_editor = $( '.volunteer-email-editor' );

        if ( $( email_editor ).exists() ) {

            $( '.wivm-send-email' ).on( 'click', function( event ) {
                event.preventDefault();

                var $this = $( this ),
                    button_text = $this.text(),
                    is_valid = true,
                    editor_value,
                    email_button = $this,
                    post_id = email_button.data( 'post-id' ),
                    user_id = email_button.data( 'user-id' );

                var subject_field = $( '#volunteer-email-subject' ),
                    subject_value = subject_field.val();

                // Get content if visual editor is selected, otherwise fall back to textarea
                if ( $( '#wp-volunteer-email-editor-wrap' ).is( '.tmce-active' ) ) {
                    editor_value = tinyMCE.activeEditor.getContent();
                } else {
                    editor_value = $( '#volunteer-email-editor' ).val();
                }

                // Check that the subject field is valid/not null
                check_valid( subject_field );

                function check_valid( field ) {
                    $( field ).keyup( function() {
                        $this.removeClass( 'has-error' );
                    });

                    if ( '' == field.val() ) {
                        field.addClass( 'has-error' );
                        is_valid = false;
                    } else {
                        is_valid = true;
                    }
                }

                // Process the email if the subject is valid
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

                            if ( response == 'success' ) {
                                $( '.volunteer-email-success' ).show().fadeTo( 300, 1 ).addClass( 'is-open' );
                            }
                            // Failure
                            else {
                                $( '.volunteer-email-failure' ).show().fadeTo( 300, 1 ).addClass( 'is-open' );
                            }
                        }
                    );
                }
            });
        }

    }); //document.ready()

})( jQuery );