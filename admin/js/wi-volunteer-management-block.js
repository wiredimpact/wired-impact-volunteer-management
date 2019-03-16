( function( wp ) {

	const { registerBlockType }						= wp.blocks;
	const { createElement, Fragment } 				= wp.element;
	const { BlockControls } 						= wp.editor;
	const { Disabled, ServerSideRender, Toolbar } 	= wp.components;
	const { __ } 									= wp.i18n;
	const volunteerOppsIcon							= 	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 256 256" xmlSpace="preserve">
															<circle fill="#2d2d2d" cx="128" cy="128" r="128"/>
															<path fill="#2d2d2d" d="M256,128C256,57.3,198.7,0,128,0v256C198.7,256,256,198.7,256,128z"/>
															<g>
															<path fill="#ffffff" d="M112.7,256l0.4-90.2c0.7-6.8-0.5-12.5-6.5-17.2c-10.1-7.8-17.8-17.7-21.9-30.1c-2-5.9-5-11.5-8.6-16.7
																c-1.2-1.7-2.5-3.7-0.9-5.8c1.5-2,3.7-2.6,6.1-2.4c3.9,0.3,6.8,2.5,8.9,5.6c2.5,3.6,4.5,7.5,6.7,11.3c1,1.8,1.6,4.5,4.1,4.1
																c2.8-0.4,1.8-3.4,2-5.3c1.6-18.3,0.6-36.6,1.2-54.9c0.1-4.6,1.3-8.6,6.3-8.5c5.1,0.1,4.5,4.8,4.6,8.3c0.3,10.5,0.4,21,0.6,31.5
																c0,2.2-0.5,5.3,2.8,5.3c3.4,0,2.5-3.2,2.6-5.3c0.9-13.3,1.8-26.6,2.5-40c0.2-4.1,1.4-7.7,5.9-7.7c5,0,5.8,3.9,5.6,8.2
																c-0.3,6.7-0.4,13.3-0.7,20c-0.3,7.1-0.6,14.1-1,21.1c-0.1,1.8-0.2,3.8,1.9,4.2c2.9,0.5,2.5-2.2,3-3.8c0.3-0.9,0.1-1.9,0.2-2.8
																c1.1-10.4,2.2-20.9,3.3-31.3c0.4-3.3,1.5-6.3,5.5-6c3.8,0.3,5.6,2.9,5.7,6.7c0,1.7,0,3.4-0.2,5.1c-0.9,10.8-1.9,21.6-2.9,32.4
																c-0.2,2-0.6,4.4,1.6,4.8c3,0.6,2.9-2.5,3.2-4.4c1.3-8.7,2.4-17.4,3.5-26.1c0.3-2.6,0.8-5.2,4.1-5.3c3-0.1,4.5,2.3,4.8,4.8
																c0.4,3.6,0.2,7.2,0.2,10.8c0,9.6-3.2,18.8-2.8,28.4c0.7,16.2,0,32.1-7.5,46.9c-0.7,1.3-0.7,2.9-1,4.4c-2.1,11.6,2.4,87.9,2.9,99.5"
																/>
															</g>
														</svg>;

	registerBlockType( 'wired-impact-volunteer-management/volunteer-opps', {

		title: 			__( 'Volunteer Opportunities', 'wired-impact-volunteer-management' ),

		description: 	__( 'Display a list of volunteer opportunities available with your organization.', 'wired-impact-volunteer-management' ),

		keywords: 		[ __( 'nonprofit', 'wired-impact-volunteer-management' ), __( 'not for profit', 'wired-impact-volunteer-management' ) ],

		category: 		'widgets',

		icon: 			volunteerOppsIcon,

		supports: 		{ html: false },

		attributes: 	{
							showOneTime: {
								type: 'boolean',
								default: true
							},
						},
		
		transforms: 	{
							from: [
								/*{
									/**
									 * @todo 	We should be able to use this transform and remove
									 * 			the other two at some point in the near future.
									 * 			I posted about a bug where including two shortcodes
									 * 			in the "tag" property doesn't appear to be working.
									 * 			You can see the issue at https://github.com/WordPress/gutenberg/issues/14476.
									 */ /*
									type: 'shortcode',
									tag: [ 'one_time_volunteer_opps', 'flexible_volunteer_opps' ],
									attributes: {
										showOneTime: {
											type: 'boolean',
											shortcode: ( attributes, { shortcode } ) => {
												return shortcode.tag === 'one_time_volunteer_opps' ? true : false;
											},
										}
									},
								}, */
								{
									type: 'shortcode',
									tag: 'one_time_volunteer_opps',
									attributes: {
										showOneTime: {
											type: 'boolean',
											shortcode: ( attributes, { shortcode } ) => {
												return shortcode.tag === 'one_time_volunteer_opps' ? true : false;
											},
										}
									},
								},
								{
									type: 'shortcode',
									tag: 'flexible_volunteer_opps',
									attributes: {
										showOneTime: {
											type: 'boolean',
											shortcode: ( attributes, { shortcode } ) => {
												return shortcode.tag === 'one_time_volunteer_opps' ? true : false;
											},
										}
									},
								},
							]
						},

		/**
		 * This represents what the editor will render when the block is used.
		 * 
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {

			const { showOneTime } = props.attributes;
			const { setAttributes } = props;

			return (
				<Fragment>

					<BlockControls>
						<Toolbar controls={
							[
								{
									icon: 'format-status',
									title: __( 'Show One-Time Opportunities', 'wired-impact-volunteer-management' ),
									onClick: () => setAttributes( { showOneTime: true } ),
									isActive: showOneTime === true,
								},
								{
									icon: 'video-alt2',
									title: __( 'Show Flexible Opportunities', 'wired-impact-volunteer-management' ),
									onClick: () => setAttributes( { showOneTime: false } ),
									isActive: showOneTime !== true,
								},
							]
						} />
					</BlockControls>

					<Disabled>
						<ServerSideRender
							block='wired-impact-volunteer-management/volunteer-opps'
							attributes={ props.attributes }
						/>
					</Disabled>

				</Fragment>
			);
		},
	
		save: function( props ) {
			// Rendering happens in PHP using the "render_callback"
			return null;
		},
	} );
} )( window.wp );
