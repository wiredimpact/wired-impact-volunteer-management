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
	 * Register initialization hooks.
	 */
	public function init() {

		parent::init();

		add_filter( 'gform_validation', array( $this, 'validate_form_submission' ), 10, 2 );
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
					'field_type'    => array( 'name', 'text' ),
					'default_value' => $this->get_first_field_by_type( 'name', 3 ),
				),
				array(
					'name'          => 'last_name',
					'label'         => __( 'Last Name', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'name', 'text' ),
					'default_value' => $this->get_first_field_by_type( 'name', 6 ),
				),
				array(
					'name'          => 'phone',
					'label'         => __( 'Phone Number', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'phone', 'text' ),
					'default_value' => $this->get_first_field_by_type( 'phone' ),
				),
				array(
					'name'          => 'email',
					'label'         => __( 'Email', 'wired-impact-volunteer-management' ),
					'required'      => true,
					'field_type'    => array( 'email' ),
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
	 * Validate the form prior to submission and feed processing.
	 *
	 * During validation we complete two checks:
	 *
	 * 1. The volunteer opportunity is still accepting new RSVPs.
	 * 2. The name, phone and email fields aren't empty.
	 *
	 * @see https://docs.gravityforms.com/gform_validation/
	 * @see https://docs.gravityforms.com/using-gform-validation-hook/
	 *
	 * @param array  $validation_result Contains the validation result and the current Form Object.
	 * @param string $context The context for the current submission. Possible values: form-submit, api-submit, or api-validate.
	 * @return array The potentially modified validation result.
	 */
	public function validate_form_submission( $validation_result, $context ) {

		$form             = $validation_result['form'];
		$entry            = GFFormsModel::get_current_lead();
		$feeds_processing = $this->get_feeds_processing( $form, $entry );

		// If no feeds will process, bail.
		if ( empty( $feeds_processing ) ) {

			return $validation_result;
		}

		// Even though multiple feeds might process, there should really only be one, so we always use the first.
		$form_data = $this->get_submitted_form_data( $feeds_processing[0], $entry, $form );

		// If the form's last page is being submitted and the volunteer opportunity is no longer allowing RSVPs.
		if ( GFFormDisplay::is_last_page( $form ) === true && $this->validate_opp_still_allowing_rsvps( $form_data, $form ) === false ) {

			$validation_result['is_valid'] = false;

			return $validation_result;
		}

		// If the checked name, phone or email field is on the submitted form page and it's blank.
		$submitted_form_page = rgpost( 'gform_source_page_number_' . $form['id'] );
		$submitted_form_page = ( $submitted_form_page !== '' ) ? (int) $submitted_form_page : 1;
		$mapped_field_ids    = $this->get_mapped_feed_field_ids( $feeds_processing[0] );

		foreach ( $mapped_field_ids as $field_key => $field_id ) {

			foreach ( $form['fields'] as &$field ) {

				if ( $field->id === $field_id && $field->pageNumber === $submitted_form_page && $form_data[ $field_key ] === '' ) {

					$validation_result['is_valid'] = false;
					$field->failed_validation      = true;
					$field->validation_message     = __( 'This field is required.', 'wired-impact-volunteer-management' );
					$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: The name, phone number or email address is missing.' );

					break;
				}
			}
		}

		$validation_result['form'] = $form;

		return $validation_result;
	}

	/**
	 * Check whether the volunteer opportunity is still allowing RSVPs.
	 *
	 * If the volounteer opportunity isn't allowing RSVPs, show a global
	 * error above the form noting that. Then add a note to the Gravity
	 * Forms logs as well.
	 *
	 * @param array $form_data The name, phone, email and post ID data from the form.
	 * @param array $form The form object currently being processed.
	 * @return bool Whether the volunteer opportunity is still allowing RSVPs.
	 */
	private function validate_opp_still_allowing_rsvps( $form_data, $form ) {

		$opp = new WI_Volunteer_Management_Opportunity( $form_data['wivm_opportunity_id'] );

		if ( $opp->should_allow_rvsps() === false ) {

			add_filter( 'gform_validation_message_' . $form['id'], array( $this, 'show_rsvp_closed_form_error_message' ), 10, 2 );
			$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: There are no more open spots for this opportunity.' );

			return false;
		}

		return true;
	}

	/**
	 * Show a Gravity Forms error above the form after it's submitted if the
	 * volunteer opportunity is no longer allowing RSVPs.
	 *
	 * @param string $validation_message_markup The default validation markup and message.
	 * @param array  $form The form object currently being processed.
	 * @return string The updated validation markup and message.
	 */
	public function show_rsvp_closed_form_error_message( $validation_message_markup, $form ) {

		$pattern                   = '/<\/span>.*?<\/h2>/s';
		$replacement               = '</span>' . __( 'We\'re sorry, but we weren\'t able to sign you up. We have no more open spots.', 'wired-impact-volunteer-management' ) . '</h2>';
		$validation_message_markup = preg_replace( $pattern, $replacement, $validation_message_markup );

		return $validation_message_markup;
	}

	/**
	 * Process the feed when a form is submitted.
	 *
	 * This occurs after the submission passes all validation and
	 * the submitter is about to see the confirmation text or page.
	 *
	 * There are two error checks that happen at this stage. If they
	 * fail either one the submission still goes through, but an error
	 * note is added to the Gravity Forms entry. The checks are:
	 *
	 * 1. The post ID provided is a volunteer opportunity.
	 * 2. The volunteer hasn't already RSVPed for this opportunity.
	 *
	 * @param array $feed The feed object to be processed.
	 * @param array $entry The form entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 */
	public function process_feed( $feed, $entry, $form ) {

		$form_data = $this->get_submitted_form_data( $feed, $entry, $form );

		// If the post ID isn't a volunteer opportunity, bail.
		if ( get_post_type( $form_data['wivm_opportunity_id'] ) !== 'volunteer_opp' ) {

			$this->add_note(
				$entry['id'],
				__( 'Error Sending Data to the Volunteer Management System: The volunteer opportunity\'s ID was not provided correctly.', 'wired-impact-volunteer-management' ),
				'error'
			);
			$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: The volunteer opportunity\'s ID was not provided correctly.' );

			return false;
		}

		// Send the data to the volunteer management system to sign up the volunteer.
		$result = WI_Volunteer_Management_Public::process_volunteer_sign_up( $form_data );

		// If the volunteer already RSVPed for this opportunity.
		if ( $result === 'already_rsvped' ) {

			$this->add_note(
				$entry['id'],
				__( 'Error Sending Data to the Volunteer Management System: The volunteer has already signed up for this opportunity.', 'wired-impact-volunteer-management' ),
				'error'
			);
			$this->log_debug( __METHOD__ . '(): Error Sending Data to the Volunteer Management System: The volunteer has already signed up for this opportunity.' );

			return false;
		}

		// Add an entry note and log message if the volunteer was successfully signed up.
		$this->add_note(
			$entry['id'],
			__( 'The volunteer successfully signed up for this opportunity.', 'wired-impact-volunteer-management' ),
			'success'
		);
		$this->log_debug( __METHOD__ . '(): The volunteer successfully signed up for this opportunity.' );

		return true;
	}

	/**
	 * Get the form field IDs mapped to the needed volunteer management fields.
	 *
	 * For fields with more than one input, like name fields, we only need
	 * the top-level field's ID. For example, if the first name field is
	 * located at field ID 1.3, we return only 1.
	 *
	 * @param array $feed The feed object to get the mapped field IDs from.
	 * @return array The mapped field IDs.
	 */
	private function get_mapped_feed_field_ids( $feed ) {

		$mapped_field_ids = array(
			'wivm_first_name' => rgars( $feed, 'meta/field_map_first_name' ),
			'wivm_last_name'  => rgars( $feed, 'meta/field_map_last_name' ),
			'wivm_phone'      => rgars( $feed, 'meta/field_map_phone' ),
			'wivm_email'      => rgars( $feed, 'meta/field_map_email' ),
		);

		$mapped_field_ids = array_map( array( $this, 'get_top_level_field_id_from_subfield_id' ), $mapped_field_ids );

		return $mapped_field_ids;
	}

	/**
	 * Ensure the top-level field ID is returned when given a subfield ID.
	 *
	 * For example, if the subfield ID 1.3 is provided, this returns 1. If
	 * a top-level field ID is provided, it's returned as is.
	 *
	 * @param string $field_id The field ID to get the top-level field ID from.
	 * @return int The top-level field ID.
	 */
	private function get_top_level_field_id_from_subfield_id( $field_id ) {

		return (int) explode( '.', $field_id )[0];
	}

	/**
	 * Get the data submitted in the form to be sent to the volunteer
	 * management system.
	 *
	 * @param array $feed The feed object currently being processed.
	 * @param array $entry The form entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 * @return array The data to be sent to the volunteer management system.
	 */
	private function get_submitted_form_data( $feed, $entry, $form ) {

		$form_data = array(
			'wivm_first_name'     => $this->get_mapped_field_value( 'field_map_first_name', $form, $entry, $feed['meta'] ),
			'wivm_last_name'      => $this->get_mapped_field_value( 'field_map_last_name', $form, $entry, $feed['meta'] ),
			'wivm_phone'          => $this->get_mapped_field_value( 'field_map_phone', $form, $entry, $feed['meta'] ),
			'wivm_email'          => $this->get_mapped_field_value( 'field_map_email', $form, $entry, $feed['meta'] ),
			'wivm_opportunity_id' => get_the_ID(),
		);

		return $form_data;
	}

	/**
	 * Get the volunteer management feeds that will process for this
	 * submission. This includes:
	 *
	 * 1. Those feeds that are active without conditional logic.
	 * 2. Those feeds that are active and pass conditional logic.
	 *
	 * @param array $form The form object currently being processed.
	 * @param array $entry The form entry object currently being processed.
	 * @return array The feeds that will process for this submission.
	 */
	private function get_feeds_processing( $form, $entry ) {

		$form_feeds       = GFAPI::get_feeds( null, $form['id'], $this->_slug );
		$feeds_processing = array();

		// If an active volunteer management form feed doesn't exist, return an empty array.
		if ( is_wp_error( $form_feeds ) ) {

			return $feeds_processing;
		}

		// Add feeds that have conditional logic deactivated, or conditional logic that passes.
		foreach ( $form_feeds as $feed ) {

			$is_conditional_logic_activated = rgars( $feed, 'meta/feed_condition_conditional_logic' );
			$conditional_logic              = rgars( $feed, 'meta/feed_condition_conditional_logic_object/conditionalLogic' );

			if ( ! $is_conditional_logic_activated ) {

				$feeds_processing[] = $feed;

			} elseif ( $is_conditional_logic_activated && GFCommon::evaluate_conditional_logic( $conditional_logic, $form, $entry ) === true ) {

				$feeds_processing[] = $feed;
			}
		}

		return $feeds_processing;
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
