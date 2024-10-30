<?php
/*
Plugin Name: Contact Form 7 Round Robin
Plugin URI: http://icreateadvertising.com.au/
Description: Add round Robin functionality to the popular Contact Form 7 plugin.
Author: iCreate Advertising Solutions
Author URI: http://icreateadvertising.com.au/
Version: 1.2.1
*/
global $rr;
require_once('class-base.php');
$rr = new BaseRR();

require_once('rr-tables.php');

// Hook for register custom table when activate
register_activation_hook( __FILE__, 'rr_table_install' );

// Hook for Remove Table from Database when we Delete Plugin
register_uninstall_hook(__FILE__,  'rr_table_uninstall');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php')) {
    if(is_plugin_active('contact-form-7/wp-contact-form-7.php')) {

        // If is admin
        if(is_admin()) {
            require_once('admin/rr-admin.php');
        } else {
            require_once('includes/rr-process.php');
        }

    } else {
       add_action('admin_notices', 'show_error_for_activate_cf7');
    }
} else {
    add_action('admin_notices', 'show_error_for_install_cf7');
}
?>