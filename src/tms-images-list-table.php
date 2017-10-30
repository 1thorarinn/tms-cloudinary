<?php
/**
 * Created by PhpStorm.
 * User: thorarinnt
 * Date: 20/10/2017
 * Time: 21:20
 */



//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


// Tms_Images_List_Table
class Tms_Product_List_Table extends WP_List_Table
{


    /**
     *
     * For this sample we'll use a dataset in a static array
     *
     */

    private $sample_data = array(

        array (
            "id" => 1,
            "title" => "A hard day's night",
            "artist" => "The Beatles",
            "year" => 1964
        ),
        array (
            "id" => 2,
            "title" => "Lucifer Sam",
            "artist" => "Pink Floyd",
            "year" => 1967
        ),
        array (
            "id" => 3,
            "title" => "Light My Fire",
            "artist" => "The Doors",
            "year" => 1966
        ),
        array (
            "id" => 4,
            "title" => "I heard it through the grapevine",
            "artist" => "Marvin Gaye",
            "year" => 1968
        ),
        array (
            "id" => 5,
            "title" => "Like a rolling stone",
            "artist" => "Bob Dylan",
            "year" => 1965
        ),
        array (
            "id" => 6,
            "title" => "Suspicious minds",
            "artist" => "Elvis Presley",
            "year" => 1969
        ),
        array (
            "id" => 7,
            "title" => "Sympathy for the devil",
            "artist" => "Rolling Stones",
            "year" => 1968
        ),
        array (
            "id" => 8,
            "title" => "I’m waiting for the man",
            "artist" => "Velvet Underground",
            "year" => 1967
        ),
        array (
            "id" => 9,
            "title" => "Shangri-Las",
            "artist" => "Leader of the pack",
            "year" => 1964
        ),
        array (
            "id" => 10,
            "title" => "All along the watchtower",
            "artist" => "Jimi Hendrix Experience",
            "year" => 1968
        ),
        array (
            "id" => 11,
            "title" => "Good vibrations",
            "artist" => "Beach Boys",
            "year" => 1966
        ),
        array (
            "id" => 12,
            "title" => "Be my baby",
            "artist" => "Ronettes",
            "year" => 1963
        ),
        array (
            "id" => 13,
            "title" => "A day in the life",
            "artist" => "The Beatles",
            "year" => 1967
        ),
        array (
            "id" => 14,
            "title" => "People are strange",
            "artist" => "The Doors",
            "year" => 1967
        ),
        array (
            "id" => 15,
            "title" => "Sunday morning",
            "artist" => "Velvet Underground",
            "year" => 1967
        ),
        array (
            "id" => 16,
            "title" => "A hard days night",
            "artist" => "The Beatles",
            "year" => 1964
        ),
        array (
            "id" => 17,
            "title" => "Help",
            "artist" => "The Beatles",
            "year" => 1965
        ),
        array (
            "id" => 18,
            "title" => "Astronomy Domine",
            "artist" => "Pink Floyd",
            "year" => 1969
        ),
        array (
            "id" => 19,
            "title" => "Barbara Ann",
            "artist" => "Beach Boys",
            "year" => 1965
        ),
        array (
            "id" => 20,
            "title" => "A Whiter Shade Of Pale",
            "artist" => "Procol Harum",
            "year" => 1967
        )

    );

    /**
     *
     * @Override of constructor
     * Constructor take 3 parameters:
     * singular : name of an element in the List Table
     * plural : name of all of the elements in the List Table
     * ajax : if List Table supports AJAX set to true
     *
     */

    function __construct() {

        parent::__construct(
            array(
                'singular'  => '60s hit',
                'plural'    => '60s hits',
                'ajax'      => true
            )
        );

    }

    /**
     * @return array
     *
     * The array is associative :
     * keys are slug columns
     * values are description columns
     *
     */

    function get_columns() {

        $columns = array(
            'id'      => 'ID',
            'title'   => 'Title',
            'artist'  => 'Artist',
            'year'    => 'Year',
            'post_author' => 'Post Author',
            'post_title' => 'Post Title'
        );
        return $columns;

    }

    /**
     * @param $item
     * @param $column_name
     *
     * @return mixed
     *
     * Method column_default let at your choice the rendering of everyone of column
     *
     */

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
            case 'title':
            case 'artist':
            case 'post_author':
            case 'post_title':
            case 'year':
                return $item[ $column_name ];
            default:
                return print_r( $item, true );
        }
    }


    function column_code($item){
        var_dump($item);
    }

    function column_post_title($item){
        // var_dump($item);
        var_dump( wp_get_attachment_metadata( $item['ID'] )) ;
    }

    /**
     * @var array
     *
     * Array contains slug columns that you want hidden
     *
     */

    private $hidden_columns = array(
        'id'
    );

    /**
     * @return array
     *
     * The array is associative :
     * keys are slug columns
     * values are array of slug and a boolean that indicates if is sorted yet
     *
     */

    function get_sortable_columns() {

        return $sortable_columns = array(
            'title'	 	=> array( 'title', false ),
            'artist'	=> array( 'artist', false ),
            'year'   	=> array( 'year', false )
        );
    }

    /**
     * @Override of prepare_items method
     *
     */

    function prepare_items() {

        /**
         * How many records for page do you want to show?
         */
        $per_page = 5;

        /**
         * Define of column_headers. It's an array that contains:
         * columns of List Table
         * hiddens columns of table
         * sortable columns of table
         * optionally primary column of table
         */
        $columns  = $this->get_columns();
        $hidden   = $this->hidden_columns;
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /**
         * Following lines are only a sample with a static array
         * in a real situation you can get data
         * from a REST architecture or from database (using $wpdb)
         */
        // $data = $this->sample_data;

        global $post;

        // Use nonce for verification to secure data sending
        //wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );


        $data = '';
        //var_dump($post->ID);



        $data = $this->sample_data; // json_decode($changeResponse->body, true);
        /*global $wpdb;
        $res = $wpdb->get_results("select p1.*
        FROM {$wpdb->posts} p1, {$wpdb->posts} p2
        WHERE p1.post_parent = p2.ID 
           AND p1.post_mime_type LIKE 'image%'
           AND p2.post_type = 'attachment'
        ORDER BY p2.post_date
        LIMIT 10;"
        );
        $data = $res;*/

        global $wpdb;
       /* $sql = "
    SELECT  p1.ID, p1.post_title         
    FROM    {$wpdb->posts} p1
    WHERE   p1.post_type = 'post'
        AND p1.post_status = 'publish' 
        AND p1.ID NOT IN ( 
                SELECT DISTINCT p2.post_parent
                FROM {$wpdb->posts} p2
                WHERE p2.post_type = 'attachment' AND p2.post_parent > 0  
        ) 
    ORDER BY p1.post_date DESC
";

// Fetch posts without attachments:
        $posts_without_attachments = $wpdb->get_results( $sql );
        $data = $posts_without_attachments;

*/
       /*

        $querystr = "
    SELECT $wpdb->posts.*
    FROM $wpdb->posts, $wpdb->postmeta
    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
    AND $wpdb->postmeta.meta_key = 'tag'
    AND $wpdb->postmeta.meta_value = 'email'
    AND $wpdb->posts.post_status = 'publish'
    AND $wpdb->posts.post_type = 'post'
    AND $wpdb->posts.post_date < NOW()
    ORDER BY $wpdb->posts.post_date DESC
 ";

 $pageposts = $wpdb->get_results($querystr, OBJECT);

        */


        $querystr = "
    SELECT $wpdb->posts.* 
    FROM $wpdb->posts, $wpdb->postmeta
    WHERE $wpdb->posts.post_type = 'attachment'
    AND $wpdb->posts.post_date < NOW()
    ORDER BY $wpdb->posts.post_date DESC
 ";

        $pageposts = $wpdb->get_results($querystr, ARRAY_A);

        $data = $pageposts;
        $this->items = $data;

        function usort_reorder( $a, $b ) {

            $orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title';
            $order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
            $result = strcmp( $a[ $orderby ], $b[ $orderby ] );
            return ( 'asc' === $order ) ? $result : -$result;
        }
        //usort( $data, 'usort_reorder' );

        /**
         * Get current page calling get_pagenum method
         */
        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        /**
         * Call to _set_pagination_args method for informations about
         * total items, items for page, total pages and ordering
         */
        $this->set_pagination_args(
            array(

                'total_items'	=> $total_items,
                'per_page'	    => $per_page,
                'total_pages'	=> ceil( $total_items / $per_page ),
                'orderby'	    => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'title',
                'order'		    => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc'
            )
        );
    }

    /**
     * @Override of display method
     */

    function display() {

        /**
         * Adds a nonce field
         */
        wp_nonce_field( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

        /**
         * Adds field order and orderby
         */
        echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';

        parent::display();
    }

    /**
     * @Override ajax_response method
     */

    function ajax_response() {

        check_ajax_referer( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

        $this->prepare_items();

        extract( $this->_args );
        extract( $this->_pagination_args, EXTR_SKIP );

        ob_start();
        if ( ! empty( $_REQUEST['no_placeholder'] ) )
            $this->display_rows();
        else
            $this->display_rows_or_placeholder();
        $rows = ob_get_clean();

        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();

        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();

        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();

        $response = array( 'rows' => $rows );
        $response['pagination']['top'] = $pagination_top;
        $response['pagination']['bottom'] = $pagination_bottom;
        $response['column_headers'] = $headers;

        if ( isset( $total_items ) )
            $response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

        if ( isset( $total_pages ) ) {
            $response['total_pages'] = $total_pages;
            $response['total_pages_i18n'] = number_format_i18n( $total_pages );
        }

        die( json_encode( $response ) );
    }

    /*

        private function table_data()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'table_name';

            $data=array();

            if(isset($_GET['s']))
            {

                $search=$_GET['s'];

                $search = trim($search);

                $wk_post = $wpdb->get_results("SELECT column_name_one,column_name_two FROM $table_name WHERE column_name_three LIKE '%$search%' and column_name_four='value'");

            }

            else{
                $wk_post=$wpdb->get_results("SELECT column_name_one,column_name_two FROM $table_name WHERE column_name_three='value'");
            }

            $field_name_one = array();

            $field_name_two = array();

            $i=0;

            foreach ($wk_post as $wk_posts) {

                $field_name_one[]=$wk_posts->field_name_one;

                $field_name_two[]=$wk_posts->field_name_two;

                $data[] = array(

                    'first_column_name'  => $field_name_one[$i],

                    'second_column_name' =>   $field_name_two[$i]

                );

                $i++;

            }

            return $data;

        }*/

    /*

        public function prepare_items()
        {

            global $wpdb;

            $columns = $this->get_columns();

            $sortable = $this->get_sortable_columns();

            $hidden=$this->get_hidden_columns();

            $this->process_bulk_action();

            $data = $this->table_data();


        } */


}