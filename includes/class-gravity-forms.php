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
	 * Add Gravity Forms as an option for the type of form to display for a volunteer opportunity.
	 *
	 * @param array $form_type_options The current options for the type of form to display.
	 * @return array The updated options for the type of form to display with Gravity Forms added.
	 */
	public function add_gravity_forms_form_type_option( $form_type_options ) {

		$form_type_options['gravity_forms'] = __( 'Gravity Forms', 'wired-impact-volunteer-management' );

		return $form_type_options;
	}

	/**
	 * Show the field to select a Gravity Forms form to display on a volunteer opportunity.
	 *
	 * @param object $volunteer_opp Volunteer opportunity object with all the meta data.
	 */
	public function show_opportunity_select_form_meta_field( $volunteer_opp ) {

		$form_options = $this->get_all_forms();
		$row_class    = ( $volunteer_opp->opp_meta['form_type'] !== 'gravity_forms' ) ? 'not-gravity-forms' : '';
		?>
		<tr class="select-form-field <?php echo $row_class; ?>">
			<td><label for="form_id"><?php _e( 'Select a Form', 'wired-impact-volunteer-management' ); ?></label></td>
			<td>
				<select id="form_id" name="form_id">
					<?php
					foreach ( $form_options as $form_id => $form_name ) {
						?>
						<option value="<?php echo $form_id; ?>" <?php selected( $volunteer_opp->opp_meta['form_id'], $form_id ); ?>><?php echo $form_name; ?></option>
						<?php
					}
					?>
				</select>
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
	 * Save the volunteer opportunity's selected Gravity Forms form to show on the front end.
	 *
	 * @param int    $volunteer_opp_id The ID of the volunteer opportunity being saved.
	 * @param object $volunteer_opp The $post object for the volunteer opportunity.
	 */
	public function save_opportunity_select_form_meta_field( $volunteer_opp_id, $volunteer_opp ) {

		if ( isset( $_REQUEST['form_id'] ) ) {

			update_post_meta( $volunteer_opp_id, '_form_id', absint( $_REQUEST['form_id'] ) );
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

		$volunteer_opp_meta['form_id'] = absint( get_post_meta( $volunteer_opp_id, '_form_id', true ) );

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

		if ( $volunteer_opp->opp_meta['form_type'] === 'gravity_forms' && is_int( $volunteer_opp->opp_meta['form_id'] ) ) {

			gravity_form( $volunteer_opp->opp_meta['form_id'], false, false, false, false, true );
		}
	}
}