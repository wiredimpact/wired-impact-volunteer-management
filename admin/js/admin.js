/**
 * All javascript for admin-specific functionality.
 */
(function( $ ) {
	'use strict';

	$(function() {

	/**
	 * Hide and show tab content on click
	 */
	$('#wivm-tabs').find('a').click(function () {
        $('#wivm-tabs').find('a').removeClass('nav-tab-active');
        $('.wivmtab').removeClass('active');

        var id = $(this).attr('id').replace('-tab', '');
        $('#' + id).addClass('active');
        $(this).addClass('nav-tab-active');
    });

    var wivm_active_tab = window.location.hash.replace('#top#', '');

    if (wivm_active_tab == '' || wivm_active_tab == '#_=_') {
        wivm_active_tab = $('.wivmtab').attr('id');
    }

    $('#' + wivm_active_tab).addClass('active');
    $('#' + wivm_active_tab + '-tab').addClass('nav-tab-active');

    $('.nav-tab-active').click();


    function wivm_set_tab_hash() {
        var settings = $('#wivm-settings-form');
        if (settings.length) {
            var currentUrl = settings.attr('action').split('#')[0];
            settings.attr('action', currentUrl + window.location.hash);
        }
    }

    /**
     * When the hash changes, get the base url from the action and then add the current hash
     */
    $(window).on('hashchange', wivm_set_tab_hash);

    /**
     * When the hash changes, get the base url from the action and then add the current hash
     */
    $(document).on('ready', wivm_set_tab_hash);


    /**
     * For volunteer opportunity edit screen including jQuery Timepicker
     */
    
    //Show and hide one-time volunteer opportunity fields
    $('#one-time-opportunity').change(function() {
        if( this.checked ) {
            $( '.one-time-field' ).show();
            $( '.non-one-time-field' ).hide();
        }
        else {
            $( '.one-time-field' ).hide();
            $( '.non-one-time-field' ).show();
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


	}); //document.ready()

})( jQuery );
