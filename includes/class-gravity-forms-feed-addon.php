<?php
/**
 * Runs all functionality related to the Gravity Forms Volunteer Management Feed Add-On.
 *
 * This includes allowing users to create feeds, and processing them after form submission.
 *
 * @since 2.1
 */
class WI_Volunteer_Management_Gravity_Forms_Feed_AddOn extends GFFeedAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @var WI_Volunteer_Management_Gravity_Forms_Feed_AddOn
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Add-On.
	 *
	 * @var string
	 */
	protected $_version = '1.0';

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @var string
	 */
	protected $_min_gravityforms_version = '2.5';

	/**
	 * Defines the add-on slug.
	 *
	 * @var string
	 */
	protected $_slug = 'wired-impact-volunteer-management';

	/**
	 * Defines the main plugin file.
	 *
	 * @var string
	 */
	protected $_path = 'wired-impact-volunteer-management/wivm.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @var string
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the title of this add-on.
	 *
	 * @var string
	 */
	protected $_title = 'Wired Impact Volunteer Management';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @var string
	 */
	protected $_short_title = 'Volunteer Mgmt';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @return WI_Volunteer_Management_Gravity_Forms_Feed_AddOn An instance of the class.
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Define feed settings fields.
	 *
	 * @return array Feed settings fields.
	 */
	public function feed_settings_fields() {

		// Define feed title and feed name field.
		$fields = array(
			'title'       => __( 'Volunteer Management Feed Settings', 'wired-impact-volunteer-management' ),
			'description' => '',
			'fields'      => array(
				array(
					'name'     => 'feed_name',
					'label'    => __( 'Name', 'wired-impact-volunteer-management' ),
					'type'     => 'text',
					'class'    => 'medium',
					'required' => true,
					'tooltip'  => sprintf( '<h6>%s</h6>%s', __( 'Name', 'wired-impact-volunteer-management' ), __( 'Enter a feed name to uniquely identify this setup.', 'wired-impact-volunteer-management' ) ),
				),
			),
		);

		// Define fields for first name, last name, phone number and email.
		$fields['fields'][] = array(
			'name'      => 'field_map',
			'label'     => __( 'Map Fields', 'wired-impact-volunteer-management' ),
			'type'      => 'field_map',
			'field_map' => array(
				array(
					'name'          => 'first_name',
					'label'         => __( 'First Name', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'name', 'text', 'hidden' ),
					'default_value' => $this->get_first_field_by_type( 'name', 3 ),
				),
				array(
					'name'          => 'last_name',
					'label'         => __( 'Last Name', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'name', 'text', 'hidden' ),
					'default_value' => $this->get_first_field_by_type( 'name', 6 ),
				),
				array(
					'name'          => 'phone',
					'label'         => __( 'Phone Number', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'phone', 'text', 'hidden' ),
					'default_value' => $this->get_first_field_by_type( 'phone' ),
				),
				array(
					'name'          => 'email',
					'label'         => __( 'Email', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'email', 'hidden' ),
					'default_value' => $this->get_first_field_by_type( 'email' ),
				),
			),
			'tooltip'   => sprintf( '<h6>%s</h6>%s', __( 'Map Fields', 'wired-impact-volunteer-management' ), __( 'Match the information needed for the volunteer management system with fields from your form.', 'wired-impact-volunteer-management' ) ),
		);

		// Add conditional logic field.
		$fields['fields'][] = array(
			'name'    => 'conditions',
			'label'   => __( 'Conditional Logic', 'wired-impact-volunteer-management' ),
			'type'    => 'feed_condition',
			'tooltip' => sprintf( '<h6>%s</h6>%s', __( 'Conditional Logic', 'wired-impact-volunteer-management' ), __( 'When conditional logic is enabled, form submissions will only be sent to the volunteer management system when the conditions are met. When disabled all form submissions will be sent.', 'wired-impact-volunteer-management' ) ),
		);

		return array( $fields );
	}

	/**
	 * Define the feed list table columns.
	 *
	 * @return array The feed list table columns.
	 */
	public function feed_list_columns() {

		return array(
			'feed_name' => __( 'Name', 'wired-impact-volunteer-management' ),
		);
	}

	/**
	 * Process the feed when a form is submitted.
	 *
	 * @param array $feed The feed object to be processed.
	 * @param array $entry The form entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 */
	public function process_feed( $feed, $entry, $form ) {

		error_log( 'Feed processed!' );

		return true;
	}

	/**
	 * Return the add-on's icon for the form settings menu.
	 *
	 * The "groups" dashicon matches what's used for the main 
	 * volunteer management plugin's menu item.
	 *
	 * @return string The WordPress dashicon for the form settings menu.
	 */
	public function get_menu_icon() {

		return 'dashicons-groups';
	}
}