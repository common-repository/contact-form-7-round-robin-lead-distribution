<?php
/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-admin.php
 * Dessription: Admin functions for admin
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.0
 */

// action for add submenu in admin
add_action( 'admin_menu', 'rr_admin_menu', 10 );


function rr_admin_menu() {
    
        add_submenu_page( 'wpcf7',
        __( 'RR Users Manager', 'rr' ),
        __( 'RR Users Manager', 'rr' ),
        'manage_options', 'rr-users-manager',
        'rr_users_manager_content' );
        add_submenu_page( 'wpcf7',
        __( 'RR Forms Manager', 'rr' ),
        __( 'RR Forms Manager', 'rr' ),
        'manage_options', 'rr-forms-manager',
        'rr_forms_manager_content' );
        add_submenu_page( 'wpcf7',
        __( 'RR Mail Tracker', 'rr' ),
        __( 'RR Mail Tracker', 'rr' ),
        'manage_options', 'rr-mail-tracker',
        'rr_mail_tracker_content' );
         
        
        
}

// Call ajax action function
function round_robin_ajax() {    
    if ( isset($_REQUEST) ) {
        require_once 'includes/rr-ajax-process.php';
        rr_ajax_process($_REQUEST);        
    }
    die();
}

// Call ajax action for guest
function round_robin_ajax_guest() {
    echo 'Permission Denied.';
    die();
}

// Add action for call ajax
add_action( 'wp_ajax_rr_ajax_request', 'round_robin_ajax' );
add_action( 'wp_ajax_nopriv_rr_ajax_request', 'round_robin_ajax_guest' );
add_action( 'init', 'rr_deregister_heartbeat', 1 );


function rr_deregister_heartbeat() {
    //global $pagenow;
    //if ( 'post.php' != $pagenow && 'post-new.php' != $pagenow )
        wp_deregister_script('heartbeat');
}

// Action function for main page in admin
function rr_forms_manager_content(){
    global $wpcf7;

    //Add style sheet for admin pages
    wp_enqueue_style( 'style-name', plugin_dir_url( __FILE__ ) . 'css/rr-style.css' );

    // Add the client side script
    wp_enqueue_script( 'rr-ajax-request', plugin_dir_url( __FILE__ ) . 'js/rr-plugin.js', array( 'jquery' ) );
    wp_localize_script( 'rr-ajax-request', 'rrAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

    //include the class file
    require_once 'includes/class-roundrobin.php';
    $arrFormItems = rr_get_cf7_items();       
    $arrUsers = rr_get_users();
?>
<div class="wrap round-robin form-manager">
    <?php #screen_icon(); ?>
    <h2 class="rrPageTitle"><?php echo esc_html( __( 'Round Robin - Forms Manager', 'rr' ) ); ?></h2>
    <br class="rrClearBoth" />
    <form name="rrOption" id="rrOption" action="">
    <div class="form-select section">
        <label for="cf7-form">Choose CF7 Form </label>
        <select name="cf7-form" id="cf7-form">
            <option value="">Select Form</option>
            <?php foreach($arrFormItems as $objformItem){
                echo '<option value="'.$objformItem->id().'">'.$objformItem->title().'</option>';
            }?>
        </select>
    </div>
    <div class="check-form section">
        <input type="checkbox" name="is-round-robin" id="is-round-robin" onclick="formAssignToRR();" /><label for="is-round-robin">Use Round robin for this form</label>
        <span class="msg"></span>
    </div>
    <div class="add-users-link section">
        <a href="admin.php?page=rr-users-manager">Add Users</a>
    </div>

    <div class="user-items-wraper section">
        <h4>Select users for this form</h4>
        <ul class="user-items">
            <?php

            if(!empty($arrUsers)){
                foreach($arrUsers as $objUser){
                    //if($objUser->is_active)
                        //$isChecked = 'checked';
                    //else
                        $isChecked = '';
                echo '<li class="user-item ' .$objUser->ID. '">                        
                        <span class="item-name">
                            <input type="text" name="user-name-' .$objUser->ID. '" id="user-name-' .$objUser->ID. '" placeholder="Name" value="' .$objUser->name. '" />
                        </span>                        
                        <span class="item-active">
                            <input type="checkbox" value="' .$objUser->ID. '" name="is-user-assigned[]" id="is-user-assigned-' .$objUser->ID. '" '. $isChecked .' />
                        </span>                        
                    </li>';
                }
            }
            ?>
        </ul>
        <?php if(!empty($arrUsers)){ ?>
        <input type="button" name="assignUsers" id="assignUsers" onclick="assignUsersToForm()" value="Save" />
        <?php } else { ?>
        <span class="msg rr-error">User(s) not found. Please add new user.</span>
        <?php } ?>
    </div>
    </form>
</div>
<?php
}

// Action function for users manage page
function rr_users_manager_content() {
    //Add style sheet for admin pages

	wp_enqueue_style( 'rr-jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css' );
    wp_enqueue_style( 'style-name', plugin_dir_url( __FILE__ ) . 'css/rr-style.css' );

    // Add the client side script
    wp_enqueue_script( 'rr-ajax-request', plugin_dir_url( __FILE__ ) . 'js/rr-plugin.js', array( 'jquery', 'jquery-ui-datepicker' ) );
    wp_localize_script( 'rr-ajax-request', 'rrAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

    //include the class file
    require_once 'includes/class-roundrobin.php';
    $arrUsers = rr_get_users();
    ?>
    <div class="wrap round-robin user-manager">
        <?php #screen_icon(); ?>
        <h2 class="rrPageTitle"><?php echo esc_html( __( 'Round Robin - Users Manager', 'rr' ) ); ?></h2>
        <br class="rrClearBoth" />
        <form name="rrUserOption" id="rrUserOption" action="">
        <div class="form-select section">
            <label for="rr-users">Users</label>
            <select name="rr-users" id="rr-users">
                <option value="">Select User</option>
                <?php foreach($arrUsers as $objuser){
                    echo '<option value="'.$objuser->ID.'">'.$objuser->name.'</option>';
                }?>
            </select>
        </div>
        <div class="add-user section">
            <input type="button" name="add-user" id="add-user" value="Add New User" />
        </div>
        
        <div class="user-details-wraper section">
            <span class="msg"></span>
            <ul class="user-details">
                <li>Please select the user (OR) Add the new user. </li>
            </ul>
        </div>
        </form>
    </div>
    <?php
}



// Action function for show mail sent details
function rr_mail_tracker_content(){
    global $wpcf7;

    //Add style sheet for admin pages
    wp_enqueue_style( 'style-name', plugin_dir_url( __FILE__ ) . 'css/rr-style.css' );

    //include the class file
    require_once 'includes/class-roundrobin.php';
    require_once 'includes/mail-sent-list-table.php';

    $table = new Custom_Mail_List_Table();
    $table->prepare_items();
    //echo '<pre>'; print_r($table);

    $arrFormItems = rr_get_cf7_items();

?>
<div class="wrap round-robin mail-list">
    <?php #screen_icon(); ?>
    <h2 class="rrPageTitle"><?php echo esc_html( __( 'Round Robin - Mail Sent Details', 'rr' ) ); ?></h2>
    <br class="rrClearBoth" />
    <form id="mail-list-table" method="post">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <select name="form-list" id="form-list">
            <option value="">Select Form</option>
            <?php foreach($arrFormItems as $objformItem){ //echo $_REQUEST['form-list'] ." == " . $objformItem->id;
                $selected = '';
                if($_REQUEST['form-list'] == $objformItem->id())
                        $selected = 'selected="selected"';
                echo '<option value="'.$objformItem->id().'" '.$selected.'>'.$objformItem->title().'</option>';
            }?>
        </select>
        <input type="submit" name="" id="post-query-submit" class="button" value="Filter">
        <?php $table->display() ?>
    </form>
</div>
<?php
}
?>