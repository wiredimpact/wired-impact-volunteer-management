<?php

/**
 * Send emails associated with signups and reminders.
 *
 * @link       http://wiredimpact.com
 * @since      0.1
 *
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 */

/**
 * Send emails associated with signups and reminders.
 *
 * Send all emails associated with volunteer sign ups as well as reminders to volunteers.
 * 
 * @since      0.1
 * @package    WI_Volunteer_Management
 * @subpackage WI_Volunteer_Management/Includes
 * @author     Wired Impact <info@wiredimpact.com>
 */
class WI_Volunteer_Management_Email { 

	/**
	 * Volunteer object with included meta data.
   *
   * @since 0.1 
	 * @var   object
	 */
	public $user;

	/**
	 * Opportunity object with included meta data.
   *
   * @since 0.1 
	 * @var object
	 */
	public $opp;

	/**
	 * All the options set within the plugin settings.
   *
   * @since 0.1 
	 * @var array
	 */
	public $options;

	/**
	 * List of variables that will be replaced in emails.
   *
   * @since 0.1 
	 * @var array
	 */
	public $search_text = array();

	/**
	 * List of text used to replace the search text within emails.
   *
   * @since 0.1 
	 * @var array
	 */
	public $replace_text = array();

	/**
	 * Grab all our options and the search and replace text to use for each email.
	 * 
	 * @param object $opp  Opportunity object with all the meta data.
	 * @param object $user Volunteer object with all the meta data. This is not used for reminder emails.
	 */
	public function __construct( $opp, $user = null ) {

		$this->opp				= $opp;

		if( $user != null ){
			$this->user			= $user;
		}

		$wivm_options 			= new WI_Volunteer_Management_Options();
		$this->options 			= $wivm_options->get_options();
		$this->set_replace_text();
		    
	}

	/**
	 * Send email to volunteer immediately after they sign up.
	 *
	 * We use the options settings to send the email and we also do a replace for the variables in the email.
	 *
	 * @see    https://codex.wordpress.org/Function_Reference/wp_mail
	 */
  	public function send_volunteer_signup_email(){
  		$to  			  = $this->user->meta['email'];
  		$subject 		= $this->options['volunteer_signup_email_subject'];
  		$message 		= wpautop( $this->replace_variables( $this->options['volunteer_signup_email'] ) );

  		$headers		= array();
  		$headers[] 	= $this->get_from_header();
  		$headers[]	= 'Content-type: text/html';

  		$result = wp_mail( $to, $subject, $message, $headers );
      do_action( 'wivm_volunteer_signup_email', $result, $to, $subject, $message );     
  	}

  /**
	 * Send email to admins and volunteer opportunity contact immediately after someone signs up.
	 *
	 * We use the options settings to send the email and we also do a replace for the variables in the email.
	 *
	 * @see    https://codex.wordpress.org/Function_Reference/wp_mail
	 */
  	public function send_admin_signup_email(){
  		$to  			  = $this->get_opp_admin_email_addresses();
  		$subject 		= $this->options['admin_signup_email_subject'];
  		$message 		= wpautop( $this->replace_variables( $this->options['admin_signup_email'] ) );

  		$headers		= array();
  		$headers[] 	= $this->get_from_header();
  		$headers[]	= 'Content-type: text/html';

  		$result = wp_mail( $to, $subject, $message, $headers );
      do_action( 'wivm_admin_signup_email', $result, $to, $subject, $message );  
  	}

  	/**
  	 * Send reminder email to volunteers that signed up.
  	 *
  	 * We send the email "to" the admins, but BCC those that have signed up to volunteer. This allows us to send 
  	 * only one email instead of sending a ton of them.
  	 *
  	 * @see    https://codex.wordpress.org/Function_Reference/wp_mail
  	 */
  	public function send_volunteer_reminder_email(){
  		$to  			  = $this->get_opp_admin_email_addresses();
  		$subject 		= $this->options['volunteer_reminder_email_subject'];
  		$message 		= wpautop( $this->replace_variables( $this->options['volunteer_reminder_email'] ) );

  		$headers		= array();
  		$headers[] 	= $this->get_from_header();
  		$headers[]	= 'Content-type: text/html';
  		$headers[]	= 'Bcc: ' . $this->get_volunteer_email_addresses();

  		$result = wp_mail( $to, $subject, $message, $headers );
      do_action( 'wivm_volunteer_reminder_email', $result, $to, $subject, $message );  
  	}

    /**
     * Get the From email header which includes the from email address and name.
     *
     * We use the from email address and name if they're available. If not, we use the WordPress admin email and the website name.
     * 
     * @return string   The from header to use in the email.
     */
    public function get_from_header(){
      $from_email_name    = ( $this->options['from_email_name'] != '' ) ? $this->options['from_email_name'] : get_option( 'blogname' );
      $from_email_address = ( $this->options['from_email_address'] != '' ) ? $this->options['from_email_address'] : get_option( 'admin_email' );

      return 'From: ' . $from_email_name . ' <' . $from_email_address . '>';
    }

  	/**
  	 * Create an array representing the 'to' field for admins when someone signs up.
  	 * 
  	 * @return array Email addresses separated by commas or an empty string if none exist.
  	 */
  	public function get_opp_admin_email_addresses(){
  		$email_addresses = array();

  		if( $this->options['admin_email_address'] != '' ){
  			$email_addresses[] = $this->options['admin_email_address'];
  		}
  		if( $this->opp->opp_meta['contact_email'] != '' ){
  			$email_addresses[] = $this->opp->opp_meta['contact_email'];
  		}

  		return $email_addresses;
  	}

  	/**
  	 * Create a string of email addresses to BCC for the volunteer reminder email including everyone that signed up.
  	 * 	
  	 * @return string String of volunteer email addresses for this opportunity to BCC.
  	 */
  	public function get_volunteer_email_addresses(){
  		$email_addresses = array();
  		$volunteers = $this->opp->get_all_rsvped_volunteers();

  		foreach( $volunteers as $volunteer ){
  			$email_addresses[] = $volunteer->meta['email'];
  		}

  		return implode( ', ', $email_addresses );
  	}

  	/**
  	 * Take a string and replace all variables with the values to be used in the email.
  	 * 		
  	 * @param  string $string_to_replace String to have variables replaced. Usually an email body.
  	 * @return string                    String with variables replaced.
  	 */
  	public function replace_variables( $string_to_replace ){
  		return str_replace( $this->search_text, $this->replace_text, $string_to_replace );
  	}

  	/**
  	 * Set two arrays: One to hold the variables used in the email templates, and the other to hold the text to replace it.
  	 */
  	public function set_replace_text(){
  		$search_and_replace_text = array(
  			'{volunteer_first_name}' 	=> $this->user->meta['first_name'],
				'{volunteer_last_name}' 	=> $this->user->meta['last_name'],
				'{volunteer_phone}'			  => $this->user->meta['phone'],
				'{volunteer_email}'			  => $this->user->meta['email'],
				'{opportunity_name}' 		  => get_the_title( $this->opp->ID ),
				'{opportunity_date_time}'	=> $this->opp->get_one_date_time(),
				'{opportunity_location}'	=> $this->opp->format_address(),
				'{contact_name}'			    => $this->opp->opp_meta['contact_name'],
				'{contact_phone}'			    => $this->opp->opp_meta['contact_formatted_phone'],
				'{contact_email}'			    => $this->opp->opp_meta['contact_email'],
  		);

      $search_and_replace_text  = apply_filters( 'wivm_search_and_replace_text', $search_and_replace_text );

  		foreach( $search_and_replace_text as $key => $value ){
  			$this->search_text[] 	  = $key;
  			$this->replace_text[] 	= $value;
  		}
  	}

}//WI_Volunteer_Management_Email