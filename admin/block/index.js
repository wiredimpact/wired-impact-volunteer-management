import metadata from './block.json';
import Edit from './edit';
import Save from './save';
import { volunteerOppsIcon } from './icons';

wp.blocks.registerBlockType( metadata.name, {
	/**
	 * @see ./icons.js
	 */
	icon: volunteerOppsIcon,

	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save: Save,
} );

// Flag to track if we've unregistered the block
let hasUnregisteredBlock = false;

// Unregister the block when editing a volunteer_opp post type.
wp.data.subscribe( function() {

	if ( hasUnregisteredBlock === false ) {

		// Grab the current post type. On domReady, this returns null.
		let currentPostType = wp.data.select( 'core/editor' ).getCurrentPostType();

		if ( currentPostType === null ) {

			return;
		}

		if ( currentPostType === 'volunteer_opp' && typeof wp.blocks.getBlockType( 'wired-impact-volunteer-management/volunteer-opps' ) !== 'undefined' ) {

			wp.blocks.unregisterBlockType( 'wired-impact-volunteer-management/volunteer-opps' );
		}

		hasUnregisteredBlock = true;
	}
} );