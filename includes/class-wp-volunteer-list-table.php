<?php
/**
 * Volunteer User List Table class.
 *
 * This class uses many adapted methods from the WP_Users_List_Table and also extends that class.
 *
 * @since 1.0.0
 * @access private
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/includes
 */
class WI_Volunteer_Users_List_Table extends WP_Users_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'volunteer',
			'plural'   => 'volunteers',
			'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	/**
	 * Prepare the volunteers list for display.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function prepare_items() {
		global $usersearch;

		//Prepare the columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		$role = 'volunteer';

		$per_page = 'users_per_page';
		$users_per_page = $this->get_items_per_page( $per_page );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'role' => $role,
			'search' => $usersearch,
			'fields' => 'all_with_meta'
		);

		if ( '' !== $args['search'] )
			$args['search'] = '*' . $args['search'] . '*';

		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];

		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		//Order by phone number if necessary
		if( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] == 'phone' ){
			$args['meta_key'] = 'phone';
		}

		// Query the user IDs for this page
		$wp_user_search = new WP_User_Query( $args );

		$this->items = $wp_user_search->get_results();

		//Order by number of volunteer activities if necessary
		if( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] == 'num_volunteer_opps' ){
			@uasort( $this->items, array( $this, "sort_users_by_number_volunteer_opportunities" ) ); //Errors suppressed due to PHP bug
		}

		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,
		) );
	}

	/**
	 * Sort users by the number of volunteer opportunities they've participated in.
	 *
	 * This is used with usort to sort an array of users.
	 * 
	 * @param  array $a User a information
	 * @param  array $b User b information
	 * @return int      1 or -1 to pass back the correct order
	 */
	public function sort_users_by_number_volunteer_opportunities( $a, $b ){
		//Set up variables for returning numbers so we can handle flip the order if order is set to descending.
		$less = -1;
		$more = 1; 
		if( isset( $_REQUEST['order'] ) && $_REQUEST['order'] == 'desc' ){
			$less = 1;
			$more = -1;
		}

		$user_a = new WI_Volunteer_Management_Volunteer( $a->ID );
		$user_b = new WI_Volunteer_Management_Volunteer( $b->ID );

		if( $user_a->meta['num_volunteer_opps'] == $user_b->meta['num_volunteer_opps'] ) {
	        return 0;
	    }

	    return ( $user_a->meta['num_volunteer_opps'] < $user_b->meta['num_volunteer_opps'] ) ? $less : $more;
	}

	/**
	 * Get a list of columns for the list table.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Array in which the key is the ID of the column, and the value is the description.
	 */
	public function get_columns() {
		$c = array(
			'name'     					=> __( 'Name', 'wivm' ),
			'email'    					=> __( 'E-mail', 'wivm' ),
			'phone'    					=> __( 'Phone Number', 'wivm' ),
			'num_volunteer_opps'  		=> __( '# of Volunteer Opportunities', 'wivm' ),
		);

		return $c;
	}

	/**
	 * Get a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		$c = array(
			'name'     					=> array( 'name', false ),
			'email'    					=> array( 'email', false ),
			'phone'     				=> array( 'phone', false ),
			'num_volunteer_opps'  		=> array( 'num_volunteer_opps', false )
		);

		return $c;
	}

	/**
	 * Retrieve an associative array of bulk actions available on this table. We have none so it's an empty array.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return array Array of bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array();

		return $actions;
	}

	/**
	 * Output nothing for extra bulk changes since we don't allow that at this point.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $which Whether this is being invoked above ("top") or below the table ("bottom").
	 */
	protected function extra_tablenav( $which ) {
		echo '';
	}

	/**
	 * Generate HTML for a single row on the volunteers list admin panel.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @global WP_Roles $wp_roles User roles object.
	 *
	 * @param object $user_object The current user object.
	 * @param string $style       Deprecated. Not used.
	 * @param string $role        Optional. Key for the $wp_roles array. Default empty.
	 * @param int    $numposts    Optional. Post count to display for this user. Defaults
	 *                            to zero, as in, a new user has made zero posts.
	 * @return string Output for a single row.
	 */
	public function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		global $wp_roles;

		if ( ! ( $user_object instanceof WP_User ) ) {
			$user_object = get_userdata( (int) $user_object );
		}
		$user_object->filter = 'display';
		$email = $user_object->user_email;

		$volunteer 			= new WI_Volunteer_Management_Volunteer( $user_object->ID );
		$phone 				= $volunteer->meta['phone'];
		$num_volunteer_opps = $volunteer->meta['num_volunteer_opps'];
		$admin_url 			= $volunteer->get_admin_url();

		if ( $this->is_site_users )
			$url = "site-users.php?id={$this->site_id}&amp;";
		else
			$url = 'users.php?';

		$checkbox = '';
		// Check if the volunteer for this row is editable
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link
			$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_object->ID ) ) );

			// Set up the hover actions for this user
			$actions = array();

			if ( current_user_can( 'edit_user',  $user_object->ID ) ) {
				$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			} else {
				$edit = "<strong>$user_object->user_login</strong><br />";
			}

			if ( !is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) )
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=delete&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Delete' ) . "</a>";
			if ( is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'remove_user', $user_object->ID ) )
				$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url( $url."action=remove&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Remove' ) . "</a>";

			/**
			 * Filter the action links displayed under each volunteer in the Users list table.
			 *
			 * @since 2.8.0
			 *
			 * @param array   $actions     An array of action links to be displayed.
			 *                             Default 'Edit', 'Delete' for single site, and
			 *                             'Edit', 'Remove' for Multisite.
			 * @param WP_User $user_object WP_User object for the currently-listed user.
			 */
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );
			$edit .= $this->row_actions( $actions );

			// Set up the checkbox ( because the user is editable, otherwise it's empty )
			$checkbox = '<label class="screen-reader-text" for="user_' . $user_object->ID . '">' . sprintf( __( 'Select %s' ), $user_object->user_login ) . '</label>'
						. "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' />";

		} else {
			$edit = '<strong>' . $user_object->user_login . '</strong>';
		}
		$role_name = isset( $wp_roles->role_names[$role] ) ? translate_user_role( $wp_roles->role_names[$role] ) : __( 'None' );
		$avatar = get_avatar( $user_object->ID, 32 );

		$r = "<tr id='user-$user_object->ID'>";

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch ( $column_name ) {
				case 'name':
					$r .= "<td $attributes>$avatar <strong><a href='" . $admin_url . "'>$user_object->first_name $user_object->last_name</a><strong></td>";
					break;
				case 'email':
					$r .= "<td $attributes>$email</td>";
					break;
				case 'phone':
					$r .= "<td $attributes>$phone</td>";
					break;
				case 'num_volunteer_opps':
					$r .= "<td $attributes>$num_volunteer_opps</td>";
					break;
				default:
					$r .= "<td $attributes>";

					/**
					 * Filter the display output of custom columns in the volunteers list table.
					 *
					 * @since 1.0.0
					 *
					 * @param string $output      Custom column output. Default empty.
					 * @param string $column_name Column name.
					 * @param int    $user_id     ID of the currently-listed user.
					 */
					$r .= apply_filters( 'manage_volunteers_custom_column', '', $column_name, $user_object->ID );
					$r .= "</td>";
			}
		}
		$r .= '</tr>';

		return $r;
	}

	/**
	 * Message to be displayed when no volunteers have signed up yet or when a search returns no results.
	 *
	 * Checks that volunteer users do exist and if not, it shows a message that people will show up when they sign up
	 * to volunteer. If they do exist then a message shows that the search returned no results.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function no_items() {
		$user_counts = count_users();
		if( !isset( $user_counts['avail_roles']['volunteer'] ) || $user_counts['avail_roles']['volunteer'] == 0 ){
			_e( 'No volunteers yet. Once they sign up they\'ll appear here.', 'wivm' );
		}
		else {
			_e( 'Oops. Your search didn\'t return any volunteers. Please try search again.', 'wivm' );
		}
	}

} //class WI_Volunteer_Users_List_Table