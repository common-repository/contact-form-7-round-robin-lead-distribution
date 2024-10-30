<?php
/*
 * Plugin : Contact Form 7 Round Robin
 * File : mail-sent-list-table.php
 * Dessription: Display the mail sent list.
 * Author: iCreate Advertising Solutions
 * Author URI: http://icreateadvertising.com.au/
 * Version: 1.0
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class Custom_Mail_List_Table extends WP_List_Table
{
    
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'mailsent',
            'plural' => 'mailsents',
        ));
    }

    // this is a default column renderer
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    // this is example, how to render specific column
    function column_sender($item)
    {
        return  htmlentities($item['sender']);
    }

    // this is example, how to render specific column
    function column_form_name($item)
    {
        $arrFormItems = rr_get_cf7_items();
        $url = admin_url( 'admin.php?page=wpcf7&post='.$item['cf7_form_id'].'&action=edit');
        foreach($arrFormItems as $objformItem) {
            if($objformItem->id() == $item['cf7_form_id'])
                return  '<a href="'.$url.'">'.$objformItem->title().'</a>';
        }

    }


    // this is how checkbox column renders
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }

    // This method return columns to display in table
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'form_name' => __('Form Name', 'rr'),
            'name' => __('Recipient Name', 'rr'),
            'email' => __('Recipient Email', 'rr'),
            'sender' => __('Sender', 'rr'),
            'subject' => __('Subject', 'rr'),
            'date_sent' => __('Sent Date', 'rr'),
        );
        return $columns;
    }

    // This method return columns that may be used to sort table
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'form_name' => array('cf7_form_id', false),
            'name' => array('name', false),
            'email' => array('email', false),
            'sender' => array('sender', false),
            'subject' => array('subject', false),
            'date_sent' => array('date_sent', false),
        );
        $sortable_columns = array(
            'name' => array('name', false),
            'email' => array('email', false),
            'date_sent' => array('date_sent', false)
        );
        return $sortable_columns;
    }

    // Return array of bult actions if has any
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );        
        return $actions;
    }

    // This method processes bulk actions
    function process_bulk_action()
    {
        global $wpdb, $rr;
        $table_name = $rr->table_name_sent; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE ID IN($ids)");
            }
        }
    }

    // It will get rows from database and prepare them to be showed in table
    function prepare_items()
    {
        global $wpdb, $rr;
        //$table_name = $rr->table_name_sent; // do not forget about tables prefix

        $per_page = 20; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        $formListfilter = isset($_REQUEST['form-list']) ? $_REQUEST['form-list'] : '' ;
        $filter = '';
        if($formListfilter)
            $filter = 'WHERE ts.cf7_form_id = '.$formListfilter;
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(ts.ID) FROM $rr->table_name_sent AS ts
                                        LEFT JOIN $rr->table_name_users AS tu
                                        ON ts.user_id = tu.ID
                                        LEFT JOIN $rr->table_name_mail AS tm
                                        ON ts.mail_id = tm.ID $filter");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'ts.ID';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT ts.ID, ts.cf7_form_id, tu.name, tu.email, tm.sender, tm.subject, ts.date_sent  FROM $rr->table_name_sent AS ts
                                        LEFT JOIN $rr->table_name_users AS tu
                                        ON ts.user_id = tu.ID 
                                        LEFT JOIN $rr->table_name_mail AS tm
                                        ON ts.mail_id = tm.ID $filter
                                        ORDER BY $orderby $order
                                        LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
?>
