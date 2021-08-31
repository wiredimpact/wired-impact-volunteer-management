( function( wp ) {

	const { registerBlockType }                  = wp.blocks;
	const { createElement, Fragment }            = wp.element;
	const { BlockControls }                      = wp.blockEditor;
	const { Disabled, Toolbar, ToolbarButton }   = wp.components;
	const { serverSideRender: ServerSideRender } = wp;
	const { __ }                                 = wp.i18n;
	const volunteerOppsIcon                      = 	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xmlSpace="preserve">
															<path d="M17.2,3c-0.2,0-0.3,0-0.5,0V2.8c0-1.2-1-2.1-2.3-2.1c-0.3,0-0.6,0-0.9,0.1C13.2,0.4,12.6,0,11.8,0c-1.2,0-2.2,0.9-2.3,1.9
															c-0.2,0-0.3,0-0.5,0C7.8,1.9,6.8,2.8,6.8,4v8L4.9,9.9C4.3,9.6,3.9,10,3.3,10.5l-1.6,1.3L7.3,19c0.3,0.3,0.6,0.5,0.9,0.6V24h1.8v-4
															h2.7v-1.6h-2.7H9.9c-0.5,0-1.1-0.2-1.5-0.5l-4.7-5.8l0.9-0.6l3.8,4L8.3,4c0-0.2,0.5-0.4,0.7-0.4S9.6,3.8,9.6,4v6h1.5V2.1
															c0-0.2,0.4-0.4,0.6-0.4s0.6,0.2,0.6,0.4V10h1.6V2.8c0-0.2,0.3-0.4,0.6-0.4s0.6,0.2,0.6,0.4V10h1.6V5.1c0-0.2,0.3-0.4,0.6-0.4
															c0.3,0,0.6,0.2,0.6,0.4v11.5c0,0.6-0.4,1.1-0.9,1.5v0.3v1.4V24h1.8v-5.3c0.5-0.6,0.8-1.3,0.8-2V5.1C19.5,4,18.5,3,17.2,3z"/>
														</svg>;
	const oneTimeOppsIcon                        = 	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xmlSpace="preserve">
															<path fill="none" d="M0,0h24v24H0V0z"/>
															<rect x="15.1" y="15.7" width="3.4" height="3.5"/>
															<path d="M19.8,3h-0.6V0.9h-2.3V3H7.2V0.9H4.9V3H4.2C3,3,2.1,4,2.1,5.1v15.4c0,1.2,0.9,2.1,2.1,2.1h15.7c1.2,0,2.1-0.9,2.1-2.1V5.1 C21.9,4,21,3,19.8,3z M19.8,20.5H4.2V7.3h15.7V20.5z"/>
														</svg>;
	const flexibleOppsIcon                       = 	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xmlSpace="preserve">
															<rect x="15.1" y="15.7" width="3.4" height="3.5"/>
															<rect x="10.4" y="15.7" width="3.1" height="3.5"/>
															<rect x="5.5" y="15.7" width="3.3" height="3.5"/>
															<rect x="15.1" y="8.4" width="3.4" height="3.5"/>
															<rect x="10.4" y="8.4" width="3.1" height="3.5"/>
															<rect x="5.5" y="8.4" width="3.3" height="3.5"/>
															<path d="M19.8,3h-0.6V0.9h-2.3V3H7.2V0.9H4.9V3H4.2C3,3,2.1,4,2.1,5.1v15.4c0,1.2,0.9,2.1,2.1,2.1h15.7c1.2,0,2.1-0.9,2.1-2.1V5.1 C21.9,4,21,3,19.8,3z M19.8,20.5H4.2V7.3h15.7V20.5z"/>
														</svg>
	let isVolunteerManagementBlockRegistered     = false;

	let registerVolunteerManagementBlock = () => registerBlockType( 'wired-impact-volunteer-management/volunteer-opps', {

		title: 			__( 'Volunteer Opportunities', 'wired-impact-volunteer-management' ),

		description: 	__( 'Display a list of volunteer opportunities available with your organization.', 'wired-impact-volunteer-management' ),

		keywords: 		[ __( 'nonprofit', 'wired-impact-volunteer-management' ), __( 'not for profit', 'wired-impact-volunteer-management' ) ],

		category: 		'widgets',

		icon: 			volunteerOppsIcon,

		supports: 		{
							html: false,
							anchor: true
						},

		attributes: 	{
							showOneTime: {
								type: 'boolean',
								default: true
							},
							anchor: {
								type: 'string',
								default: '',
								attribute: 'id',
								selector: '.volunteer-opps'
							},
						},
		
		transforms: 	{
							from: [
								{
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
						<Toolbar label={ __( 'Opportunity Type Controls', 'wired-impact-volunteer-management' ) }>
								<ToolbarButton
									icon={ oneTimeOppsIcon }
									label={ __( 'Show One-Time Opportunities', 'wired-impact-volunteer-management' ) }
									onClick={ () => setAttributes( { showOneTime: true } ) }
									isPressed={ showOneTime === true}
								/>
								<ToolbarButton
									icon={ flexibleOppsIcon }
									title={ __( 'Show Flexible Opportunities', 'wired-impact-volunteer-management' ) }
									onClick={ () => setAttributes( { showOneTime: false } ) }
									isPressed={ showOneTime !== true }
								/>
						</Toolbar>
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

	// We have to subscribe to changes because on domReady the block editor isn't initialized yet.
	wp.data.subscribe( function() {

		// Only check if we should register the block if it isn't already registered.
		if ( isVolunteerManagementBlockRegistered === false ) {

			// Grab the current post type. On domReady, this returns null.
			let currentPostType = wp.data.select( 'core/editor' ).getCurrentPostType();

			// Only register the block if the post type is set and the post isn't a Volunteer Opportunity.
			if ( currentPostType !== null && currentPostType !== 'volunteer_opp' ) {
				isVolunteerManagementBlockRegistered = true;
				registerVolunteerManagementBlock();
			}
		}
	} );
} )( window.wp );
