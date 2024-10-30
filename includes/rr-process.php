<?php
/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-ajax-process.php
 * Dessription: Admin functions for admin
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.1
 */

//Round Robin process function
function rr_process( $WPCF7 ) {

	$submission = WPCF7_Submission::get_instance();
	if ( ! $submission ) {
		return;
	}
	$formId      = $WPCF7->id;
	$arrForm = rr_get_form_by_id( $formId );
	if ( ! empty( $arrForm ) ) {
		$frmStatus = $arrForm[0]->enabled;
		if ( $frmStatus ) {
			$strAssignedUsers = $arrForm[0]->users_id;

			if ( $strAssignedUsers ) {
				$arrAssignedUsersId = unserialize( $strAssignedUsers );
				$lmsUserId          = $arrForm[0]->lms_user_id;

				$objUser = rr_get_next_user( $lmsUserId, $arrAssignedUsersId );

				if ( ! empty( $objUser ) ) {
					//Parsing Mail Template Fields
					$mailTemplate = rr_parsing_fields( $WPCF7 );
					//Insert mail template details to Database
					$mailTemplateID = rr_add_mail_template( $mailTemplate );


					$mail              = $WPCF7->prop( 'mail' );
					$mail['recipient'] = $objUser->name . " <" . $objUser->email . ">";
					$WPCF7->set_properties( array( 'mail' => $mail ) );


					// Insert Mail sent status to database
					$result = 1;
					rr_add_mail_sent_status( $formId, $objUser->ID, $mailTemplateID, $result );

				}
			}
		}
	}

}

add_action( "wpcf7_before_send_mail", "rr_process" );

// Get user details by form id
function rr_get_users( $formId ) {
	global $wpdb, $rr;
	$sqlGetUser = 'SELECT * FROM `' . $rr->table_name_users . '` WHERE `cf7_form_id` = ' . $formId . ' AND `is_active` = 1 ORDER BY ID';

	return $wpdb->get_results( $sqlGetUser );
}

function rr_get_next_user( $current_user_id, $user_list_array ) {
	global $wpdb, $rr;
	$user_list = implode( ",", $user_list_array );
	$query     = 'SELECT * FROM `' . $rr->table_name_users . '` WHERE `ID` IN( ' . $user_list . ' ) AND `is_active` = 1 ORDER BY ID';
	$results   = $wpdb->get_results( $query );

	foreach ( $results as $id => $fields ) {
		if ( rr_is_on_holidays( $fields->ID ) ) {
			$results[ $id ]->on_holidyas = true;
		}
		else {
			$results[ $id ]->on_holidays = false;
		}
	}
	$ids                = wp_list_pluck( $results, 'ID' );
	$current_user_index = array_search( $current_user_id, $ids );

	for ( $i = 1; $i <= count( $results ); $i ++ ) {
		$next_user_index = ( $current_user_index + $i ) % count( $results );
		if ( $results[ $next_user_index ]->on_holidays === false ) {
			return $results[ $next_user_index ];
		}
	}

	return false;
}

function rr_is_on_holidays( $user_id ) {
	$holidays = rr_get_user_holidays( $user_id );

	if ( $holidays['start'] == '' ) {
		return false;
	}

	$timezone_string = get_option('timezone_string');
	$timezone = $timezone_string ? new DateTimeZone($timezone_string) : null;

	$start_date = DateTime::createFromFormat( '!Y-m-d', $holidays['start'], $timezone );
	$now        = new DateTime('now', $timezone);

	if ( $holidays['end'] == '' ) {
		return $start_date < $now;
	}
	else {
		$end_date = DateTime::createFromFormat( '!Y-m-d', $holidays['end'], $timezone );
		$end_date->modify( '+1 day' );

		return ( $start_date < $now ) && ( $end_date >= $now );
	}
}


// Add mail sent status to users
function rr_add_mail_sent_status( $formId, $userId, $mailTemplateID, $result ) {
	global $wpdb, $rr;
	$wpdb->insert(
		$rr->table_name_sent,
		array(
			'cf7_form_id' => $formId,
			'user_id'     => $userId,
			'mail_id'     => $mailTemplateID,
			'status'      => $result,
			'date_sent'   => date( 'Y-m-d H:i:s' )
		)
	);

	$userUpdated = $wpdb->update(
		$rr->table_name_forms,
		array(
			'lms_user_id' => $userId,
		),
		array( 'cf7_form_id' => $formId )
	);
}

// Get form details by form id
function rr_get_form_by_id( $formId ) {
	global $wpdb, $rr;
	$sqlCheckForm = 'SELECT * FROM `' . $rr->table_name_forms . '` WHERE `cf7_form_id` = ' . $formId;
	$arrForm      = $wpdb->get_results( $sqlCheckForm );

	return $arrForm;
}

// Parsing mails template fields
function rr_parsing_fields( $WPCF7 ) {
	$defaults      = array(
		'subject'  => '',
		'sender'   => '',
		'body'     => '',
		'use_html' => false
	);
	$mail_template = wp_parse_args( $WPCF7->mail, $defaults );

	$mailTemplate['subject'] = wpcf7_mail_replace_tags( $mail_template['subject'] );
	$mailTemplate['sender']  = wpcf7_mail_replace_tags( $mail_template['sender'] );
	$mailTemplate['body']    = wpcf7_mail_replace_tags( $mail_template['body'] );

	return $mailTemplate;
}

// Insert mail template details
function rr_add_mail_template( $mailTemplate ) {
	global $wpdb, $rr;
	$wpdb->insert(
		$rr->table_name_mail,
		array(
			'sender'   => $mailTemplate['sender'],
			'subject'  => $mailTemplate['subject'],
			'body'     => $mailTemplate['body'],
			'add_date' => date( 'Y-m-d H:i:s' )
		)
	);

	return $wpdb->insert_id;
}

if ( ! function_exists( 'array_key_first' ) ) {
	function array_key_first( array $arr ) {
		foreach ( $arr as $key => $unused ) {
			return $key;
		}

		return null;
	}
}

