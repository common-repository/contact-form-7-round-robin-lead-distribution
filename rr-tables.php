<?php
/*
 * Plugin : Contact Form 7 Round Robin
 * File : rr-tables.php
 * Dessription: Insert and Remove the tables from database
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.2.1
 */

/* function for insert custom table */
function rr_table_install() {
       global $wpdb, $rr;
       /**Execute the sql statement to create or update the custom table**/
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sqlTbForms = "CREATE TABLE IF NOT EXISTS " . $rr->table_name_forms . " (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `cf7_form_id` int(11) NOT NULL,
                  `users_id` text NOT NULL,
                  `enabled` tinyint DEFAULT 0,
                  `lms_user_id` int(11),
                  `add_date` DATETIME,
                  `update_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`ID`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $sqlTbUser = "CREATE TABLE IF NOT EXISTS " . $rr->table_name_users . " (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,                  
                  `name` varchar(100) NOT NULL,
                  `email` varchar(100) NOT NULL,
                  `is_active` tinyint DEFAULT 0,
                  `add_date` DATETIME,
                  `update_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`ID`)
                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;";

        $sqltbSent = "CREATE TABLE IF NOT EXISTS " . $rr->table_name_sent . " (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `cf7_form_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `mail_id` int(11) NOT NULL,
                  `status` varchar(100) NOT NULL,
                  `date_sent` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`ID`)
                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $sqlTbmail = "CREATE TABLE IF NOT EXISTS " . $rr->table_name_mail . " (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `sender` varchar(150) NOT NULL,
                  `subject` varchar(200) NOT NULL,
                  `body` Text NOT NULL,
                  `add_date` DATETIME,
                  PRIMARY KEY (`ID`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        dbDelta($sqlTbForms);
        dbDelta($sqlTbUser);
        dbDelta($sqltbSent);
        dbDelta($sqlTbmail);
        add_option( "rr_browser_db_version", '1.0' );
}

// Function for Remove Table from Database
function rr_table_uninstall(){
     
    global $wpdb, $rr;
   
    $sqlTbForms = "DROP TABLE IF EXISTS $rr->table_name_forms;";
    $sqlTbUser  = "DROP TABLE IF EXISTS $rr->table_name_users;";
    $sqlTbSent  = "DROP TABLE IF EXISTS $rr->table_name_sent;";
    $sqlTbMail  = "DROP TABLE IF EXISTS $rr->table_name_mail;";
    $wpdb->query($sqlTbForms);
    $wpdb->query($sqlTbUser);
    $wpdb->query($sqlTbSent);
    $wpdb->query( $sqlTbMail);
    delete_option("rr_browser_db_version");
}
?>