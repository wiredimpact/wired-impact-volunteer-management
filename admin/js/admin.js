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

	 // init
    var wivm_active_tab = window.location.hash.replace('#top#', '');

    // default to first tab
    if (wivm_active_tab == '' || wivm_active_tab == '#_=_') {
        wivm_active_tab = $('.wivmtab').attr('id');
    }

    $('#' + wivm_active_tab).addClass('active');
    $('#' + wivm_active_tab + '-tab').addClass('nav-tab-active');

    $('.nav-tab-active').click();


    function wivm_set_tab_hash() {
        var settings = jQuery('#wivm-settings-form');
        if (settings.length) {
            var currentUrl = settings.attr('action').split('#')[0];
            settings.attr('action', currentUrl + window.location.hash);
        }
    }

    /**
     * When the hash changes, get the base url from the action and then add the current hash
     */
    jQuery(window).on('hashchange', wivm_set_tab_hash);

    /**
     * When the hash changes, get the base url from the action and then add the current hash
     */
    jQuery(document).on('ready', wivm_set_tab_hash);


	}); //document.ready()

})( jQuery );
