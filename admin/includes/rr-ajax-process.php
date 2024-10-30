<?php
/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-ajax-process.php
 * Dessription: Admin functions for admin
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.0
 */

// Ajax action process function
function rr_ajax_process($request){
    require_once 'class-roundrobin.php';
    switch ($request['process']){
            case 'getUser': // Get user details for form
                $formID = $request['formID'];

                // Add the form id to forms table if not exist
                $arrForms = rr_get_form_by_id($formID);
                if(empty($arrForms)) {
                    $request['isRR'] = 0;
                    rr_assign_form($request);
                }

                // Fetech the users details by form
                $html = rr_get_users_by_formId($formID);
                echo $html;
                break;
            case 'getUserDetails':
                $userID = $request['userID'];
                $html = rr_get_user_html_fields($userID);
                echo $html;
                break;
            case 'usersAssignToForm':
                $html = rr_users_assign_to_form($request);
                echo $html;
                break;
            case 'reloadUserSelect':
                $userID = $request['userID'];
                $html  = rr_reload_user_select($userID);
                echo $html;  
                break;
            case 'addUser': // Add new user
                $html = rr_add_new_user($request);
                echo $html;
                break;
            case 'editUser': // Edit user
                $html = rr_edit_user($request);
                echo $html;
                break;
            case 'deleteUser': // Delete User
                $html = rr_delete_user($request);
                echo $html;
                break;
            case 'assignForm': // enable and disable option for round robin process to the form
                $html = rr_assign_form($request);
                echo $html;
                break;
            case 'formStatus': // Check form status
                $formID = $request['formID'];
                $arrForms = rr_get_form_by_id($formID);
                if(!empty($arrForms)) {
                    if($arrForms[0]->enabled)
                        echo 1;
                    else
                        echo 0;
                } else {
                    echo 0;
                }                
                break;
        }
}

?>