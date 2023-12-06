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

		if ( self::$_instance === null ) {

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

		$wivm_data = array(
			'first_name' => $this->get_mapped_field_value( 'field_map_first_name', $form, $entry, $feed['meta'] ),
			'last_name'  => $this->get_mapped_field_value( 'field_map_last_name', $form, $entry, $feed['meta'] ),
			'phone'      => $this->get_mapped_field_value( 'field_map_phone', $form, $entry, $feed['meta'] ),
			'email'      => $this->get_mapped_field_value( 'field_map_email', $form, $entry, $feed['meta'] ),
			'post_id'    => get_the_ID(),
		);

		if ( $this->is_volunteer_data_valid( $wivm_data, $entry ) === false ) {

			return false;
		}

		$this->add_note(
			$entry['id'],
			__( 'Data passed successfully to the volunteer management system.', 'wired-impact-volunteer-management' ),
			'success'
		);

		return true;
	}

	/**
	 * Check whether the volunteer data from the form is valid.
	 *
	 * @param array $wivm_data The name, phone and email data from the form.
	 * @param array $entry The form entry object currently being processed.
	 * @return boolean Whether the volunteer data is valid.
	 */
	private function is_volunteer_data_valid( $wivm_data, $entry ) {

		// If some volunteer data is missing.
		if ( empty( $wivm_data['first_name'] ) || empty( $wivm_data['last_name'] ) || empty( $wivm_data['phone'] ) || empty( $wivm_data['email'] ) ) {

			$this->add_note(
				$entry['id'],
				__( 'Error Sending Data to the Volunteer Management System: The name, phone number or email address is missing.', 'wired-impact-volunteer-management' ),
				'error'
			);
			$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: The name, phone number or email address is missing.' );

			return false;
		}

		// If the provided email address isn't formatted correctly.
		if ( ! is_email( $wivm_data['email'] ) ) {

			$this->add_note(
				$entry['id'],
				__( 'Error Sending Data to the Volunteer Management System: The provided email address is invalid.', 'wired-impact-volunteer-management' ),
				'error'
			);
			$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: The provided email address is invalid.' );

			return false;
		}

		// If the post ID isn't a volunteer opportunity.
		if ( get_post_type( $wivm_data['post_id'] ) !== 'volunteer_opp' ) {

			$this->add_note(
				$entry['id'],
				__( 'Error Sending Data to the Volunteer Management System: The volunteer opportunity\'s ID was not provided correctly.', 'wired-impact-volunteer-management' ),
				'error'
			);
			$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: The volunteer opportunity\'s ID was not provided correctly.' );

			return false;
		}

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
