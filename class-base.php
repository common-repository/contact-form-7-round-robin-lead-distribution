<?php
/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-admin.php
 * Dessription: Admin functions for admin
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.2
 */

class BaseRR {
    public $prefix;
    public $table_name_forms;
    public $table_name_users;
    public $table_name_sent;
    public $table_name_mail;
    public $browser_db_version;

    public function __construct() {
        $this->prefix = 'rr_' ;
        $this->table_name_forms     = $this->prefix.'cf7_forms';
        $this->table_name_users     = $this->prefix.'cf7_users';
        $this->table_name_sent      = $this->prefix.'cf7_sent';
        $this->table_name_mail      = $this->prefix.'cf7_mail';
        $this->browser_db_version   = '1.0';
    }    
}

function show_error_for_activate_cf7() {
    $out = '<div class="error" id="messages"><p>';
    $out .= 'The Contact Form 7 is installed, but you must activate Contact Form 7 below for the <strong>Round Robin</strong> plugin to work.';
    $out .= '</p></div>';
    echo $out;
}
function show_error_for_install_cf7() {
    $out = '<div class="error" id="messages"><p>';
    $out .= 'The Contact Form 7 plugin must be installed for the <strong>Round Robin</strong> plugin to work.';
    $out .= '</p></div>';
    echo $out;
}

function rr_get_user_holidays( $userId ) {
	$holidays = get_option( "rr_holidays_{$userId}" );
	if ( $holidays ) {
		return $holidays;
	}

	return [
		'start' => '',
		'end'   => '',
	];
}
