<?php

/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-admin.php
 * Dessription: Admin functions for admin
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.0
 */

// Get cf7 form details
function rr_get_cf7_items() {
	$formItems = WPCF7_ContactForm::find();

	return $formItems;
}

// Get User list by form id
function rr_get_users_by_formId( $formId ) {
	global $wpdb, $rr;
	// Get assigned users id to form
	$arrAssignedUsers = rr_get_form_by_id( $formId );
	$strAssignedUser  = $arrAssignedUsers[0]->users_id;
	if ( $strAssignedUser ) {
		$arrAssignedUsersId = unserialize( $strAssignedUser );
	}
	// Get all user details
	$arrUsers  = rr_get_users();
	$isChecked = '';
	$html      = '';
	foreach ( $arrUsers as $objUser ) {
		if ( ! empty( $arrAssignedUsersId ) ) {
			if ( in_array( $objUser->ID, $arrAssignedUsersId ) ) {
				$isChecked = 'checked';
			}
			else {
				$isChecked = '';
			}
		}
		$html .= '<li class="user-item ' . $objUser->ID . '">                   
                    <span class="item-name">
                        <input type="text" name="user-name-' . $objUser->ID . '" id="user-name-' . $objUser->ID . '" placeholder="Name" value="' . $objUser->name . '" />
                    </span>                    
                    <span class="item-active">
                        <input type="checkbox" value="' . $objUser->ID . '" name="is-user-assigned[]" id="is-user-assigned-' . $objUser->ID . '" ' . $isChecked . ' />
                    </span>                    
                </li>';

	}

	return $html;
}

// Assign users to form
function rr_users_assign_to_form( $request ) {
	global $wpdb, $rr;
	$intFormId  = '';
	$arrUsersId = array();
	if ( isset( $request["formID"] ) ) {
		$intFormId = $request["formID"];
	}
	if ( isset( $request["usersId"] ) ) {
		$arrUsersId = $request["usersId"];
	}
	if ( ! empty( $arrUsersId ) ) {
		$strUsersId = serialize( $arrUsersId );
	}
	else {
		$strUsersId = '';
	}

	$userUpdated = $wpdb->update(
		$rr->table_name_forms,
		array(
			'users_id' => $strUsersId,
		),
		array( 'cf7_form_id' => $intFormId )
	);

	return $userUpdated;

}

// Get user html fields by user Id
function rr_get_user_html_fields( $userId ) {
	$arrUser = rr_get_users_by_id( $userId );
	if ( ! empty( $arrUser ) ) {
		$objUser = $arrUser[0];
		if ( $objUser->is_active ) {
			$isChecked = 'checked';
		}
		else {
			$isChecked = '';
		}
		ob_start();
		?>
        <li class="user-item <?= $objUser->ID; ?>">
            <span class="item-id"><?= $objUser->ID; ?></span>
            <span class="item-name">
                        <label for="user-name-<?= $objUser->ID; ?>" class="label">Name</label>
                        <input type="text" name="user-name-<?= $objUser->ID; ?>" id="user-name-<?= $objUser->ID; ?>" placeholder="Name" value="<?= $objUser->name; ?>"/>
                    </span>
            <span class="item-email">
                        <label for="user-email-<?= $objUser->ID; ?>" class="label">Email</label>
                        <input type="text" name="user-email-<?= $objUser->ID; ?>" id="user-email-<?= $objUser->ID; ?>" placeholder="Email" value="<?= $objUser->email; ?>"/>
                    </span>
            <span class="item-active">
                        <label for="" class="label">&nbsp</label>
                        <input type="checkbox" value="1" name="is-user-active-<?= $objUser->ID; ?>" id="is-user-active-<?= $objUser->ID; ?>" <?= $isChecked; ?> /><label for="is-user-active">Active</label>
                    </span>

            <span class="item-holidays rr-item">
                <span class="rr-section-heading">Holidays</span>
                        <label for="user-holidays-start-<?= $objUser->ID; ?>" class="label">Start Day </label>
                        <input type="text" class="rr-date" name="user-holidays-start-<?= $objUser->ID; ?>" id="holidays-start-<?= $objUser->ID; ?>" value="<?= $objUser->holidays['start']; ?>"/>
                        <label for="user-holidays-end-<?= $objUser->ID; ?>" class="label">Last Day</label>
                        <input type="text" class="rr-date"  name="user-holidays-end-<?= $objUser->ID; ?>" id="holidays-end-<?= $objUser->ID; ?>" value="<?= $objUser->holidays['end'] ?>"/>
<input type="button" name="clear-holidays" id="clear-holidays" onclick="clearHolidays()" value="Clear">
                    </span>

            <span class="item-edit">
                        <label for="" class="label">&nbsp</label>
                        <input type="button" name="user-edit-<?= $objUser->ID; ?>" class="edit-save-button"  id="user-edit-<?= $objUser->ID; ?>" onclick="editUser(<?= $objUser->ID; ?>)" value="Save"/>
                    </span>
            <span class="item-delete">
                        <label for="" class="label">&nbsp</label>
                        <input type="button" class="edit-delete-button" name="user-delete-<?= $objUser->ID; ?>" id="user-delete-<?= $objUser->ID; ?>" onclick="deleteUser(<?= $objUser->ID; ?>)" value="Delete"/>
                    </span>
        </li>

		<?php
		$html = ob_get_clean();

		return $html;
	}


}

// Get user details
function rr_get_users() {
	global $wpdb, $rr;
	$sqlGetUsers = 'SELECT * FROM `' . $rr->table_name_users . '` ORDER BY `ID`';

	return $wpdb->get_results( $sqlGetUsers );
}

// Get user details by form id
function rr_get_users_by_form_id( $formId ) {
	global $wpdb, $rr;
	$sqlGetUser = 'SELECT * FROM `' . $rr->table_name_users . '` WHERE `cf7_form_id` = ' . $formId . ' ORDER BY `ID`';

	return $wpdb->get_results( $sqlGetUser );
}

// Check email is exist

function rr_check_mail_exist( $emailId, $userId = '' ) {
	global $wpdb, $rr;
	$whrUserId = '';
	if ( $userId ) {
		$whrUserId = ' And ID <>  ' . $userId;
	}
	$sqlGetUser = "SELECT ID FROM `" . $rr->table_name_users . "` WHERE `email` = '" . $emailId . "'" . $whrUserId . " ORDER BY `ID`";

	return $wpdb->get_results( $sqlGetUser );
}

// Get user details by id
function rr_get_users_by_id( $userId ) {
	global $wpdb, $rr;
	$sqlGetUser = 'SELECT * FROM `' . $rr->table_name_users . '` WHERE `ID` = ' . $userId;

	$results = $wpdb->get_results( $sqlGetUser );
	if ( ! empty( $results ) ) {
		$results[0]->holidays = rr_get_user_holidays( $userId );
	}

	return $results;

}



// Reload User Select drobdown
function rr_reload_user_select( $userId ) {
	$arrUsers = rr_get_users();
	$html     = '<option value="" ';
	$html     .= ( $userId == '' ) ? 'selected="selected"' : '';
	$html     .= '>Select User</option>';
	foreach ( $arrUsers as $objuser ) {
		$html .= '<option value="' . $objuser->ID . '" ';
		$html .= ( $userId == $objuser->ID ) ? 'selected="selected"' : '';
		$html .= '>' . $objuser->name . '</option>';
	}

	return $html;
}


// Stroe the user details
function rr_add_new_user( $userDetails ) {
	global $wpdb, $rr;
	$lastid  = '';
	$chkUser = rr_check_mail_exist( $userDetails['email'] );
	if ( ! empty( $chkUser ) ) {
		return 'user exist';
	}
	if ( isset( $userDetails['email'] ) ) {
		$wpdb->insert(
			$rr->table_name_users,
			array(
				'name'      => $userDetails['name'],
				'email'     => $userDetails['email'],
				'is_active' => $userDetails['active'],
				'add_date'  => date( 'Y-m-d' )
			)
		);
		$lastid = $wpdb->insert_id;
	}
	if ( $lastid ) {
		$arrUser = rr_get_users_by_id( $lastid );
		if ( ! empty( $arrUser ) ) {
			$objUser = $arrUser[0];
			if ( $objUser->is_active ) {
				$isChecked = 'checked';
			}
			else {
				$isChecked = '';
			}
			$html = '<span class="item-id">' . $objUser->ID . '</span>
                        <span class="item-name">
                            <label for="user-name-' . $objUser->ID . '" class="label">Name</label>
                            <input type="text" name="user-name-' . $objUser->ID . '" id="user-name-' . $objUser->ID . '" placeholder="Name" value="' . $objUser->name . '" />
                        </span>
                        <span class="item-email">
                            <label for="user-email-' . $objUser->ID . '" class="label">Email</label>
                            <input type="text" name="user-email-' . $objUser->ID . '" id="user-email-' . $objUser->ID . '" placeholder="Email" value="' . $objUser->email . '" />
                        </span>
                        <span class="item-active">
                            <label for="" class="label">&nbsp</label>
                            <input type="checkbox" value="1" name="is-user-active-' . $objUser->ID . '" id="is-user-active-' . $lastid . '" ' . $isChecked . ' /><label for="is-user-active">Active</label>
                        </span>
                        <span class="item-edit">
                            <label for="" class="label">&nbsp</label>
                            <input type="button" class="edit-save-button" name="user-edit-' . $objUser->ID . '" id="user-edit-' . $objUser->ID
                    . '" onclick="editUser(' . $objUser->ID . ')" value="Save" />
                        </span>
                        <span class="item-delete">
                            <label for="" class="label">&nbsp</label>
                            <input type="button"  class="edit-delete-button" name="user-delete-' . $objUser->ID . '" id="user-delete-' . $objUser->ID . '" onclick="deleteUser(' . $objUser->ID . ')" value="Delete" />
                        </span>';
		}
		else {
			$html = 'failed';
		}
	}
	else {
		$html = 'failed';
	}

	return $html;
}

// Edit the user details
function rr_edit_user( $userDetails ) {
	global $wpdb, $rr;

	$strUserId     = $userDetails['userId'];
	$strUserName   = $userDetails['name'];
	$strUserEmail  = $userDetails['email'];
	$strUserActive = $userDetails['active'];
	// Check user email exist to other users
	$chkUser = rr_check_mail_exist( $strUserEmail, $strUserId );
	if ( ! empty( $chkUser ) ) {
		return 'user exist';
	}
	$userUpdated = $wpdb->update(
		$rr->table_name_users,
		array(
			'name'      => $strUserName,
			'email'     => $strUserEmail,
			'is_active' => $strUserActive
		),
		array( 'ID' => $strUserId )
	);

	update_option("rr_holidays_{$strUserId}", [
		'start' => $userDetails['holidaysStart'],
		'end'   => $userDetails['holidaysEnd'],
    ]);



	return $userUpdated;
}

// Delete the user details
function rr_delete_user( $userDetails ) {
	global $wpdb, $rr;
	$userDeleted = $wpdb->delete( $rr->table_name_users, array( 'ID' => $userDetails['userId'] ) );

	return $userDeleted;
}

// Enable or Disable the form to round robin
function rr_assign_form( $formDetails ) {
	global $wpdb, $rr;
	$formExist = rr_get_form_by_id( $formDetails['formID'] );
	if ( empty( $formExist ) ) {
		$formUpdated = $wpdb->insert(
			$rr->table_name_forms,
			array(
				'cf7_form_id' => $formDetails['formID'],
				'enabled'     => $formDetails['isRR'],
				'users_id'    => '',
				'add_date'    => date( 'Y-m-d' )
			)
		);
	}
	else {
		$formUpdated = $wpdb->update(
			$rr->table_name_forms,
			array(
				'enabled' => $formDetails['isRR']
			),
			array( 'cf7_form_id' => $formDetails['formID'] )
		);
	}

	return $formUpdated;
}

// Check the form id is exist in rr_forms table
function rr_get_form_by_id( $formId ) {
	global $wpdb, $rr;
	$sqlCheckForm = 'SELECT * FROM `' . $rr->table_name_forms . '` WHERE `cf7_form_id` = ' . $formId;
	$arrForm      = $wpdb->get_results( $sqlCheckForm );

	return $arrForm;
}

?>