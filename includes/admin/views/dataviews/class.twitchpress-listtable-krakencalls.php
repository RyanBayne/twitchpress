<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class.wp-list-table.php' );
}

/**
 * TwitchPress_ListTable_Krakencalls.
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     TwitchPress/Views
 * @version     1.0.0
 */
class TwitchPress_ListTable_Krakencalls extends WP_List_Table {

    /**
     * Max items.
     *
     * @var int
     */
    protected $max_items;

    public $items = array();
    
    /**
     * Constructor.
     */
    public function __construct() {

        parent::__construct( array(
            'singular'  => __( 'Request', 'wpseed' ),
            'plural'    => __( 'Requests', 'wpseed' ),
            'ajax'      => false
        ) );
        
        // Apply default items to the $items object.
        $this->default_items();
    }

    /**
    * Setup default items. 
    * 
    * This is not required and was only implemented for demonstration purposes. 
    * 
    * @version 1.2
    */
    public function default_items() {
        global $bugnet;
        $entry_counter = 0;// Acts as temporary ID for data that does not have one. 
        
        // Get data for GET requests. 
        $get_calls = get_transient( 'twitchpress_kraken_requests' );

        if( !isset( $get_calls['get']['requests'] ) || !is_array( $get_calls['get']['requests'] ) ) {
            $get_calls['get']['requests'] = array();            
        } 

        // Loop on individual trace entries. 
        foreach( $get_calls['get']['requests'] as $key => $entry ) {

            // Filter very common
            if( $entry['function'] == 'getChannelObject_Authd' ){ continue; }
            if( $entry['function'] == 'checkToken' ){ continue; }
            
            ++$entry_counter;
            
            $this->items[]['entry_number'] = $entry_counter; 

            // Get the new array key we just created. 
            end( $this->items);
            $new_key = key( $this->items );
                                
            // Time of entry example: int 1503562573
            $this->items[$new_key]['time']         = $entry['time'];
            $this->items[$new_key]['function']     = $entry['function'];
            $this->items[$new_key]['result']       = $entry['result'];
            $this->items[$new_key]['httpdstatus']  = $entry['httpdstatus'];
            $this->items[$new_key]['header']       = $entry['header'];
            $this->items[$new_key]['get']          = $entry['get'];
            $this->items[$new_key]['url']          = $entry['url'];
            $this->items[$new_key]['curl_url']     = $entry['curl_url'];
            $this->items[$new_key]['error_string'] = $entry['error_string'];
            $this->items[$new_key]['error_no']     = $entry['error_no'];
                       
        }

    }
    
    /**
     * No items found text.
     */
    public function no_items() {
        _e( 'No items found.', 'wpseed' );
    }

    /**
     * Don't need this.
     *
     * @param string $position
     */
    public function display_tablenav( $position ) {

        if ( $position != 'top' ) {
            parent::display_tablenav( $position );
        }
    }

    /**
     * Output the report.
     */
    public function output_result() {

        $this->prepare_items();
        echo '<div id="poststuff" class="twitchpress-tablelist-wide">';
        $this->display();
        echo '</div>';
    }

    /**
     * Get column value.
     *
     * @param mixed $item
     * @param string $column_name   
     * 
     * @version 1.0
     */
    public function column_default( $item, $column_name ) {
        
        switch( $column_name ) {

            case 'time' :
                echo $item['time'];
            break;

            case 'function' :
                echo '<pre>'; print_r( $item['function'] ); echo '</pre>';
            break;

            case 'result' :
                echo '<pre>'; print_r( $item['result'] ); echo '</pre>';
            break;          
            
            case 'httpdstatus' :
                echo '<pre>'; print_r( $item['httpdstatus'] ); echo '</pre>';
            break;            
            
            case 'header' :
                echo '<pre>'; print_r( $item['header'] ); echo '</pre>';
            break;            
            
            case 'get' :
                echo '<pre>'; print_r( $item['get'] ); echo '</pre>';
            break;            
   
            case 'url' :
                echo '<pre>'; print_r( $item['url'] ); echo '</pre>';
            break;            
        
            case 'curl_url' :
                echo '<pre>'; print_r( $item['curl_url'] ); echo '</pre>';
            break;            
  
            case 'error_string' :
                echo '<pre>'; print_r( $item['error_string'] ); echo '</pre>';
            break;            

            case 'error_no' :
                echo '<pre>'; print_r( $item['error_no'] ); echo '</pre>';
            break;
                        
        }
    }

    /**
     * Get columns.
     *
     * @return array
     */
    public function get_columns() {

        $columns = array(
            'time'         => __( 'Date/Time', 'wpseed' ),
            'function'     => __( 'function', 'wpseed' ),
            'result'       => __( 'Result', 'wpseed' ),
            //'httpdstatus'  => __( 'httpdstatus', 'wpseed' ),
            //'header'       => __( 'header', 'wpseed' ),
            //'get'          => __( 'get', 'wpseed' ),
            'url'          => __( 'url', 'wpseed' ),
            //'curl_url'     => __( 'curl_url', 'wpseed' ),
            //'error_string' => __( 'error_string', 'wpseed' ),
            //'error_no'     => __( 'error_no', 'wpseed' ),
        );
            
        return $columns;
    }

    /**
     * Prepare customer list items.
     */
    public function prepare_items() {

        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
        $current_page          = absint( $this->get_pagenum() );
        $per_page              = apply_filters( 'twitchpress_listtable_krakencalls_items_per_page', 20 );

        $this->get_items( $current_page, $per_page );

        /**
         * Pagination.
         */
        $this->set_pagination_args( array(
            'total_items' => $this->max_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $this->max_items / $per_page )
        ) );
    }
}
