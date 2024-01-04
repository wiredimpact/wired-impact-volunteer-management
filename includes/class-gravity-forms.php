<?php
/**
 * Integration with the Gravity Forms plugin.
 *
 * @link       https://wiredimpact.com
 * @since      2.0
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 */

/**
 * Integration with the Gravity Forms plugin.
 *
 * Handles displaying a Gravity Forms form for volunteer sign ups and
 * passing data between Gravity Forms and the volunteer management
 * plugin.
 *
 * @since      2.0
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Gravity_Forms_Integration {

	/**
	 * The form type setting value stored in the database when the volunteer
	 * opportunity should show a Gravity Forms form.
	 *
	 * @var string
	 */
	const FORM_TYPE_SETTING_GF_VALUE = 'gravity_forms';

	/**
	 * The setting key used to store the Gravity Forms form ID to display for
	 * a specific volunteer opportunity.
	 *
	 * @var string
	 */
	const FORM_ID_META_SETTING_KEY = '_form_id';

	/**
	 * The setting key used to store the Gravity Forms form ID to be used by
	 * default for all new volunteer opportunities.
	 *
	 * @var string
	 */
	const FORM_ID_DEFAULT_SETTING_KEY = 'default_form_id';

	/**
	 * Add Gravity Forms as an option for the type of form to display for a volunteer opportunity.
	 *
	 * @param array $form_type_options The current options for the type of form to display.
	 * @return array The updated options for the type of form to display with Gravity Forms added.
	 */
	public function add_gravity_forms_form_type_option( $form_type_options ) {

		$form_type_options[ self::FORM_TYPE_SETTING_GF_VALUE ] = __( 'Custom Form', 'wired-impact-volunteer-management' );

		return $form_type_options;
	}

	/**
	 * Show the field to select a Gravity Forms form to be used by default
	 * for all new volunteer opportunities.
	 *
	 * @param object $wi_form Instance of the WI_Volunteer_Management_Form class used to manage plugin settings fields.
	 */
	public function show_opportunity_default_select_form_meta_field( $wi_form ) {

		$default_form_type = $wi_form->wivm_options->get_option( 'default_form_type' );
		$row_class         = ( $default_form_type !== self::FORM_TYPE_SETTING_GF_VALUE ) ? 'not-gravity-forms' : '';

		$wi_form->select(
			self::FORM_ID_DEFAULT_SETTING_KEY,
			__( 'Select a Default Form', 'wired-impact-volunteer-management' ),
			$this->get_all_forms(),
			array(
				'class'             => 'select-form-field ' . $row_class,
				'no_values_message' => $this->get_no_forms_found_message(),
			)
		);
	}

	/**
	 * Show the field to select a Gravity Forms form to display on a volunteer opportunity.
	 *
	 * @param object $volunteer_opp Volunteer opportunity object with all the meta data.
	 */
	public function show_opportunity_select_form_meta_field( $volunteer_opp ) {

		$form_options = $this->get_all_forms();
		$row_class    = ( $volunteer_opp->opp_meta['form_type'] !== self::FORM_TYPE_SETTING_GF_VALUE ) ? 'not-gravity-forms' : '';
		?>
		<tr class="select-form-field <?php echo $row_class; ?>">
			<td><label for="form_id"><?php _e( 'Select a Form', 'wired-impact-volunteer-management' ); ?></label></td>
			<td>
				<?php if ( ! empty( $form_options ) ) : ?>

					<select id="form_id" name="form_id">
						<?php
						foreach ( $form_options as $form_id => $form_name ) {
							?>
							<option value="<?php echo $form_id; ?>" <?php selected( $volunteer_opp->opp_meta['form_id'], $form_id ); ?>><?php echo $form_name; ?></option>
							<?php
						}
						?>
					</select>

				<?php else : ?>

					<p class="error"><?php echo $this->get_no_forms_found_message(); ?></p>

				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get all the website's Gravity Forms forms to display in a select field.
	 *
	 * @return array The forms including their ID as the key and title as the value.
	 */
	private function get_all_forms() {

		$form_options = array();

		if ( ! class_exists( 'GFAPI' ) ) {

			return $form_options;
		}

		$all_forms = GFAPI::get_forms( true, false, 'title' );

		foreach ( $all_forms as $form ) {

			$form_options[ $form['id'] ] = $form['title'];
		}

		return $form_options;
	}

	/**
	 * Get the message to display when no forms are found while trying
	 * to build a list of select element options.
	 *
	 * @return string The message to display.
	 */
	private function get_no_forms_found_message() {

		return __( 'No forms found. Forms will appear here as soon as you create one in Gravity Forms.', 'wired-impact-volunteer-management' );
	}

	/**
	 * Save the volunteer opportunity's selected Gravity Forms form to show on the front end.
	 *
	 * @param int    $volunteer_opp_id The ID of the volunteer opportunity being saved.
	 * @param object $volunteer_opp The $post object for the volunteer opportunity.
	 */
	public function save_opportunity_select_form_meta_field( $volunteer_opp_id, $volunteer_opp ) {

		if ( isset( $_REQUEST['form_id'] ) ) {

			update_post_meta( $volunteer_opp_id, self::FORM_ID_META_SETTING_KEY, absint( $_REQUEST['form_id'] ) );
		}
	}

	/**
	 * Get the chosen Gravity Forms form for a volunteer opportunity and store it with the
	 * other volunteer opportunity meta.
	 *
	 * @param array $volunteer_opp_meta The other volunteer opportunity meta.
	 * @param int   $volunteer_opp_id The ID of the volunteer opportunity.
	 * @return array The updated volunteer opportunity meta including the form ID.
	 */
	public function get_selected_form_for_opp_meta( $volunteer_opp_meta, $volunteer_opp_id ) {

		$form_id = absint( get_post_meta( $volunteer_opp_id, self::FORM_ID_META_SETTING_KEY, true ) );

		if ( $form_id > 0 ) {

			$volunteer_opp_meta['form_id'] = $form_id;

		} else {

			$options                       = new WI_Volunteer_Management_Options();
			$volunteer_opp_meta['form_id'] = absint( $options->get_option( self::FORM_ID_DEFAULT_SETTING_KEY ) );
		}

		return $volunteer_opp_meta;
	}

	/**
	 * Show the Gravity Forms form for a volunteer opportunity.
	 *
	 * This only runs when the admin has selected to show a Gravity Forms form
	 * instead of the built-in form or no form at all.
	 *
	 * @param object $volunteer_opp Volunteer opportunity object with all the meta data.
	 */
	public function show_volunteer_sign_up_form( $volunteer_opp ) {

		if ( $volunteer_opp->opp_meta['form_type'] === self::FORM_TYPE_SETTING_GF_VALUE && is_int( $volunteer_opp->opp_meta['form_id'] ) ) {

			$form_heading_text = ( $volunteer_opp->opp_meta['one_time_opp'] === 1 ) ? __( 'Sign Up to Volunteer', 'wired-impact-volunteer-management' ) : __( 'Express Interest in Volunteering', 'wired-impact-volunteer-management' );

			echo apply_filters( 'wivm_sign_up_form_heading', '<h3 class="wivm-form-heading">' . $form_heading_text . '</h3>', $volunteer_opp );

			if ( $volunteer_opp->should_allow_rvsps() ) {

				gravity_form( $volunteer_opp->opp_meta['form_id'], false, false, false, false, true );

			} else {

				echo '<p>' . __( 'We\'re sorry, but we\'re no longer accepting new volunteers for this opportunity.', 'wired-impact-volunteer-management' ) . '</p>';
			}
		}
	}

	/**
	 * Enqueue the necessary styles and scripts for the Gravity Forms form loading on the page.
	 *
	 * @see https://docs.gravityforms.com/gravity_form_enqueue_scripts/
	 */
	public function enqueue_scripts() {

		if ( ! is_singular( 'volunteer_opp' ) ) {

			return;
		}

		$volunteer_opp = new WI_Volunteer_Management_Opportunity( get_the_ID() );

		if ( $volunteer_opp->opp_meta['form_type'] === self::FORM_TYPE_SETTING_GF_VALUE && is_int( $volunteer_opp->opp_meta['form_id'] ) ) {

			gravity_form_enqueue_scripts( $volunteer_opp->opp_meta['form_id'], true );
		}
	}

	/**
	 * Add custom volunteer opportunity merge tags to the Gravity Forms
	 * merge tag dropdown.
	 *
	 * Our custom volunteer opportunity merge tags are only shown if a volunteer
	 * opportunity form feed is active.
	 *
	 * @param array      $merge_tags The existing custom merge tags.
	 * @param int        $form_id The ID of the current form.
	 * @param array      $fields An array of field objects.
	 * @param string|int $element_id The ID of the input field.
	 * @return array The updated custom merge tags with the volunteer opportunity merge tags added.
	 */
	public function add_custom_merge_tags( $merge_tags, $form_id, $fields, $element_id ) {

		$form_feeds = GFAPI::get_feeds( null, $form_id, 'wired-impact-volunteer-management' );

		if ( is_wp_error( $form_feeds ) ) {

			return $merge_tags;
		}

		array_push(
			$merge_tags,
			array(
				'label' => __( 'Volunteer Opportunity Name', 'wired-impact-volunteer-management' ),
				'tag'   => '{volunteer_opportunity_name}',
			),
			array(
				'label' => __( 'Volunteer Opportunity Date & Time', 'wired-impact-volunteer-management' ),
				'tag'   => '{volunteer_opportunity_date_time}',
			),
			array(
				'label' => __( 'Volunteer Opportunity Location', 'wired-impact-volunteer-management' ),
				'tag'   => '{volunteer_opportunity_location}',
			),
			array(
				'label' => __( 'Volunteer Opportunity Contact Name', 'wired-impact-volunteer-management' ),
				'tag'   => '{volunteer_opportunity_contact_name}',
			),
			array(
				'label' => __( 'Volunteer Opportunity Contact Phone Number', 'wired-impact-volunteer-management' ),
				'tag'   => '{volunteer_opportunity_contact_phone}',
			),
			array(
				'label' => __( 'Volunteer Opportunity Contact Email', 'wired-impact-volunteer-management' ),
				'tag'   => '{volunteer_opportunity_contact_email}',
			)
		);

		return $merge_tags;
	}

	/**
	 * Replace the custom volunteer opportunity merge tags with the appropriate text.
	 *
	 * For example, this runs when sending a Gravity Forms notification email.
	 * The merge tags wrapped in "{}" are replaced with the appropriate text.
	 *
	 * @param string $text The current text in which merge tags are being replaced.
	 * @param object $form The current form.
	 * @param object $entry The current form entry.
	 * @param bool   $url_encode Whether to encode any URLs found in the replaced value.
	 * @param bool   $esc_html Whether to encode HTML found in the replaced value.
	 * @param bool   $nl2br Whether to convert newlines to break tags.
	 * @param string $format Determines how the value should be formatted. Default is html.
	 * @return string The updated text with the custom merge tags replaced.
	 */
	public function replace_custom_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( $form === false ) {

			return $text;
		}

		$form_feeds = GFAPI::get_feeds( null, $form['id'], 'wired-impact-volunteer-management' );

		if ( is_wp_error( $form_feeds ) ) {

			return $text;
		}

		add_filter( 'wivm_search_and_replace_text', array( $this, 'add_email_search_replace_text' ), 10, 2 );

		$volunteer_opp_id = get_the_ID();
		$volunteer_opp    = new WI_Volunteer_Management_Opportunity( $volunteer_opp_id );
		$volunteer_email  = new WI_Volunteer_Management_Email( $volunteer_opp );
		$text             = $volunteer_email->replace_variables( $text );

		remove_filter( 'wivm_search_and_replace_text', array( $this, 'add_email_search_replace_text' ), 10, 2 );

		return $text;
	}

	/**
	 * Add our custom merge tags to the list of variables when doing a search and replace.
	 *
	 * Using the "wivm_search_and_replace_text" hook we piggyback onto the existing email
	 * variables, copying the values from those that already exist. That way if those change
	 * these don't need to be updated manually.
	 *
	 * @param array  $search_and_replace_text The existing variables with the text to replace them with.
	 * @param object $user The user object for the volunteer.
	 * @return array The updated variables with the text to replace them with.
	 */
	public function add_email_search_replace_text( $search_and_replace_text, $user ) {

		$search_and_replace_text['{volunteer_opportunity_name}']          = $search_and_replace_text['{opportunity_name}'];
		$search_and_replace_text['{volunteer_opportunity_date_time}']     = $search_and_replace_text['{opportunity_date_time}'];
		$search_and_replace_text['{volunteer_opportunity_location}']      = $search_and_replace_text['{opportunity_location}'];
		$search_and_replace_text['{volunteer_opportunity_contact_name}']  = $search_and_replace_text['{contact_name}'];
		$search_and_replace_text['{volunteer_opportunity_contact_phone}'] = $search_and_replace_text['{contact_phone}'];
		$search_and_replace_text['{volunteer_opportunity_contact_email}'] = $search_and_replace_text['{contact_email}'];

		return $search_and_replace_text;
	}

	/**
	 * Hide the volunteer opportunity RSVP and email meta boxes if the
	 * opportunity uses Gravity Forms and there are no active form feeds.
	 *
	 * This avoids showing meta boxes focused on volunteers when no volunteers
	 * will be stored in the plugin for the opportunity.
	 *
	 * @param bool   $show_volunteer_opp_meta_boxes Whether to show the meta boxes.
	 * @param object $volunteer_opp The volunteer opportunity object.
	 * @return bool Whether to show the meta boxes.
	 */
	public function show_hide_volunteer_opp_meta_boxes( $show_volunteer_opp_meta_boxes, $volunteer_opp ) {

		// If the form type isn't Gravity Forms, don't alter whether the meta boxes are shown.
		if ( $volunteer_opp->opp_meta['form_type'] !== self::FORM_TYPE_SETTING_GF_VALUE ) {

			return $show_volunteer_opp_meta_boxes;
		}

		// If there's no active volunteer management form feed for this form, hide the meta boxes. If there is, show them.
		$form_feeds                    = GFAPI::get_feeds( null, $volunteer_opp->opp_meta['form_id'], 'wired-impact-volunteer-management' );
		$show_volunteer_opp_meta_boxes = ( is_wp_error( $form_feeds ) ) ? false : true;

		return $show_volunteer_opp_meta_boxes;
	}
}
