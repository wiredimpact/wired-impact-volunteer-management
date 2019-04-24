<?php

/**
 * Output the form and fields for all settings.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 */

/**
 * Output the form and fields for all settings.
 *
 * Used to generate the form and fields for settings within the WordPress admin.
 * It is adapted from the WordPress SEO by Yoast plugin (https://wordpress.org/plugins/wordpress-seo/)
 * 
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Admin
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Form {

	/**
	 * The options object.
	 *
	 * @since  0.1
	 * @var    object
	 */
	public $wivm_options;

	/**
	 * Then name of our saved option.
	 *
	 * @since  0.1
	 * @var    string
	 */
	public $option_name;

	/**
	 * Use WI_Volunteer_Management_Options to retrieve our options
	 * or the defaults if necessary.
	 */
	public function __construct(){
		$this->wivm_options = new WI_Volunteer_Management_Options();
		$this->option_name 	= $this->wivm_options->option_name;
	}

	/**
	 * Generates the header for admin pages
	 */
	public function admin_header() {
		?>
		<div class="wrap">
			<?php
			/**
			 * Display the updated/error messages
			 * Only needed as our settings page is not under options, otherwise it will automatically be included
			 * @see settings_errors()
			 */
			require_once( ABSPATH . 'wp-admin/options-head.php' );
			?>
        	<h1><?php _e( 'Wired Impact Volunteer Management: ' . apply_filters( 'wivm_help_and_settings_menu_item_name', 'Help & Settings' ), 'wired-impact-volunteer-management' ); ?></h1>
        	<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" id="wivm-settings-form" method="POST">
        <?php
	}

	/**
	 * Generates the footer for admin pages.
	 */
	public function admin_footer() {
		submit_button();
		echo '</form>';
		echo '</div><!-- end of wrap -->';
	}

	/**
	 * Display the beginning of a form table for a settings form to match the style of other settings pages.
	 * Usually this is the beginning of one tab's content.
	 * 
	 * @param  string $tab_id
	 */
	public function form_table_start( $tab_id ){
		?>
		<div id="<?php echo $tab_id; ?>" class="wivmtab">
		<table class="form-table">
			<tbody>
		<?php
	}

	/**
	 * Display the end of a form table for a settings form to match the style of other settings pages.
	 * Usually this is the end of one tab's content.
	 */
	public function form_table_end(){
		?>
				</tbody>
    		</table>
		</div>
		<?php
	}

	/**
	 * Output a heading to break up the options within a tab.
	 * 
	 * @param  string $text The text to be used for the heading.
	 * @param  string $description Paragraph text to describe the group of settings.
	 */
	public function section_heading( $heading, $description ){
		echo '<tr><th colspan="2">';
			echo '<h3>' . $heading . '</h3>';
			echo '<p>' . $description . '</p>';
		echo '</th></tr>';
	}

	/**
	 * Output a label element. Usually this is an HTML label element, but sometimes it's just basic text.
	 *
	 * @param string $text The text to show on the left side of settings page.
	 * @param array  $attr
	 */
	public function label( $text, $attr ) {
		$attr = wp_parse_args( $attr, array(
				'class' => '',
				'text_only' => false,
				'for'   => '',
			)
		);

		echo '<th>';
			if( $attr['text_only'] != true ) echo '<label class="' . $attr['class'] . '" for="' . esc_attr( $attr['for'] ) . '">';
			echo $text;
			if( $attr['text_only'] != true ) echo '</label>';
		echo '</th>';
	}

	/**
	 * Create a Text input field.
	 *
	 * @param string $var   		The variable within the option to create the text input field for.
	 * @param string $label 		The label to show for the variable.
	 * @param array  $attr  		Extra class to add to the input field, Description for for field, Placeholder for field.
	 * @param string $val_format 	Method to format the value before it's output into the form field.
	 */
	public function textinput( $var, $label, $attr = array(), $val_format = null ) {
		$attr = wp_parse_args( $attr, array(
			'placeholder' => '',
			'class'       => '',
			'description' => '',
		) );
		$val = $this->wivm_options->get_option( $var );

		if( $val_format != null ){
			$val = $this->{$val_format}( $val );
		}

		echo '<tr>';
			$this->label( $label, array( 'for' => $var ) );
			echo '<td>';
				echo '<input class="regular-text ' . esc_attr( $attr['class'] ) . ' " placeholder="' . esc_attr( $attr['placeholder'] ) . '" type="text" id="', esc_attr( $var ), '" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" value="', esc_attr( $val ), '"/>';
				if( $attr['description'] ) echo '<p class="description">' . $attr['description'] . '</p>';
			echo '</td>';
		echo '</tr>';
	}

	/**
	 * Create a textarea.
	 *
	 * @param string $var   The variable within the option to create the textarea for.
	 * @param string $label The label to show for the variable.
	 * @param array  $attr  The CSS class to assign to the textarea.
	 */
	public function textarea( $var, $label, $attr = array() ) {
		$attr = wp_parse_args( $attr, array(
			'cols'  		=> '',
			'rows'  		=> '',
			'class' 		=> '',
			'description' 	=> ''
		) );
		$val = $this->wivm_options->get_option( $var );

		echo '<tr>';

			$this->label( $label, array( 'for' => $var, 'class' => 'textarea' ) );
			echo '<td>';

				echo '<textarea cols="' . esc_attr( $attr['cols'] ) . '" rows="' . esc_attr( $attr['rows'] ) . '" class="large-text ' . esc_attr( $attr['class'] ) . '" id="' . esc_attr( $var ) . '" name="' . esc_attr( $this->option_name ) . '[' . esc_attr( $var ) . ']">' . esc_textarea( $val ) . '</textarea>';
				if( $attr['description'] ) echo '<p class="description">' . $attr['description'] . '</p>';

			echo '</td>';

		echo '</tr>';
	}

	/**
	 * Create a WYSIWYG editor.
	 *
	 * @param string       $var   The variable within the option to create the text input field for.
	 * @param string       $label The label to show for the variable.
	 * @param array $attr  Extra class to add to the input field, Description for the field, Placeholder for field
	 */
	public function wysiwyg_editor( $var, $label, $attr = array() ) {
		$attr = wp_parse_args( $attr, array(
			'class'       => '',
			'description' => '',
		) );
		$content = $this->wivm_options->get_option( $var );

		echo '<tr>';

			$this->label( $label, array( 'for' => $var . '-editor' ) );
			echo '<td>';

				wp_editor( $content, $var . '-editor', array(
					'media_buttons' => false,
					'textarea_name' => esc_attr( $this->option_name ) . '[' . esc_attr( $var ) . ']',
					'editor_height' => 425,
					'editor_css'    => $attr['class']
				));
				if( $attr['description'] ) echo '<p class="description">' . $attr['description'] . '</p>';

			echo '</td>';

		echo '</tr>';	
	}

	/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var            The variable within the option to create the checkbox for.
	 * @param string $main_label     The main label that shows on the left side of the settings page.
	 * @param string $checkbox_label The label that shows just to the right of the checkbox.
	 */
	public function checkbox( $var, $main_label, $checkbox_label ) {
		echo '<tr>';

			$this->label( $main_label, array( 'text_only' => true ) );
			echo '<td>';

				echo '<input class="checkbox" type="checkbox" id="' . esc_attr( $var ) . '" name="' . esc_attr( $this->option_name ) . '[' . esc_attr( $var ) . ']" value="on"' . checked( $this->wivm_options->get_option( $var ), 'on', false ), '/>';
				echo '<label for="' . $var . '">' . $checkbox_label . '</label>';

			echo '</td>';

		echo '</tr>';
	}

	/**
	 * Create a Select Box.
	 *
	 * @param string $var    The variable within the option to create the select for.
	 * @param string $label  The label to show for the variable.
	 * @param array  $values The select options to choose from.
	 * @param array  $attr   Description for the field
	 */
	public function select( $var, $label, $values, $attr = array() ) {
		$attr = wp_parse_args( $attr, array(
			'description' => '',
		) );

		if ( ! is_array( $values ) || $values === array() ) {
			return;
		}
		$val = $this->wivm_options->get_option( $var );

		echo '<tr>';

			$this->label( $label, array( 'for' => $var, 'class' => 'select' ) );
			echo '<td>';

				echo '<select class="select" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" id="', esc_attr( $var ), '">';
				foreach ( $values as $value => $label ) {
					if ( ! empty( $label ) ) {
						echo '<option value="', esc_attr( $value ), '"', selected( $val, $value, false ), '>', $label, '</option>';
					}
				}
				echo '</select>';
				if( $attr['description'] ) echo '<p class="description">' . $attr['description'] . '</p>';

			echo '</td>';

		echo '</tr>';
	}

	/**
	 * Create a Radio input field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The radio options to choose from.
	 * @param string $label  The label to show for the variable.
	 */
	public function radio( $var, $values, $label ) {
		if ( ! is_array( $values ) || $values === array() ) {
			return;
		}

		$var_esc = esc_attr( $var );

		echo '<tr>';

			$this->label( $label, array() );

			echo '<td><fieldset>';

				foreach ( $values as $key => $value ) {
					$key_esc = esc_attr( $key );
					echo '<label for="' . $var_esc . '-' . $key_esc . '">';
						echo '<input type="radio" class="radio" id="' . $var_esc . '-' . $key_esc . '" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" value="' . $key_esc . '" ' . checked( $this->wivm_options->get_option( $var ), $key_esc, false ) . ' />';
					echo ' ' . $value . '</label><br>';
				}

			echo '</fieldset></td>';

		echo '</tr>';
	}

	/**
	 * Create a hidden input field.
	 *
	 * @param string $var   		The variable within the option to create the hidden input field for.
	 * @param array  $attr  		Extra class to add to the input field
	 * @param string $val_format 	Method to format the value before it's output into the form field.
	 */
	public function hidden( $var, $attr = array(), $val_format = null ) {
		$attr = wp_parse_args( $attr, array(
			'class'       => '',
		) );
		$val = $this->wivm_options->get_option( $var );

		if( $val_format != null ){
			$val = $this->{$val_format}( $val );
		}

		echo '<tr style="display: none;">';
			echo '<td>';
				echo '<input class="' . esc_attr( $attr['class'] ) . '" type="hidden" id="', esc_attr( $var ), '" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" value="', esc_attr( $val ), '"/>';
			echo '</td>';
		echo '</tr>';
	}

	/**
	 * Format a phone number that's provided only in integers.
	 *
	 * @todo   Remove duplicates of this method that exist in other classes
	 * 
	 * @param  int $unformmated_number Phone number in only integers
	 * @return string Phone number formatted to look nice.
	 */
	public function format_phone_number( $unformatted_number ){
		$formatted_number = '';

		if( $unformatted_number != '' ){
			//translators: phone number pattern for preg_replace(), see http://php.net/manual/en/function.preg-replace.php
			$pattern = __( '/^(\d{3})(\d{3})(\d{4})$/', 'wired-impact-volunteer-management' );
			//translators: phone number replacement for preg_replace(), see http://php.net/manual/en/function.preg-replace.php
			$replacement = __( '($1) $2-$3', 'wired-impact-volunteer-management' );

			$formatted_number = preg_replace( $pattern, $replacement, $unformatted_number );
		}

		return apply_filters( 'wivm_formatted_phone', $formatted_number, $unformatted_number );
	}
}