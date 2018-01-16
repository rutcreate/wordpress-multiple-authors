<?php

class Multiple_Authors_Section_List_Table extends WP_List_Table {
    function __construct() {
        parent::__construct( array(
            'singular' => 'Multiple Authors Section',
            'plural' => 'Multiple Authors Sections',
            'ajax' => false,
        ) );
    }

    public static function get_sections( $per_page = 5, $page_number = 1 ) {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}multiple_authors_section ORDER BY weight ASC, id ASC";
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $results;
    }

    public static function delete_section( $id ) {
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}multiple_authors_section", array( 'id' => $id ), array( '%d' ) );
    }

    public static function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}multiple_authors_section";
        return $wpdb->get_var( $sql );
    }

    public function no_items() {
        _e( 'No sections avaliable.', 'multiple_authors' );
    }

    function column_title( $item ) {
        $nonce = wp_create_nonce( 'multiple-authors-delete' );
        $url = menu_page_url( 'multiple-authors-section-edit', false ) . '&id=' . $item['id'];
        $title = sprintf( '<a href="%s"><strong>%s</strong></a>', $url, $item['title'] );
        $actions = array();
        $actions['edit'] = sprintf( '<a href="%s">Edit</a>', $url );
        if ( $item['id'] != 1 ) {
            $actions['trash'] = sprintf(
                '<a href="?page=%s&action=delete&section=%s&_wpnonce=%s" onclick="javascript:return confirm(\'%s\')">Delete</a>',
                esc_attr( $_REQUEST['page'] ),
                absint( $item['id'] ),
                $nonce,
                sprintf( 'Are you sure you want to delete \\\'%s\\\'?', $item['title'] )
            );
        }
        return $title . $this->row_actions( $actions );
    }

    function column_cb( $item ) {
        if ( $item['id'] != 1 ) {
            return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id'] );
        }
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Title', 'multiple_authors' ),
        );
        return $columns;
    }

    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => 'Delete'
        ];
        return $actions;
    }

    function get_hidden_columns() {
        return array();
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page( 'sections_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ) );

        $this->items = self::get_sections( $per_page, $current_page );
    }

    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( ! wp_verify_nonce( $nonce, 'multiple-authors-delete' ) ) {
              die( 'Access denied.' );
            }

            self::delete_section( absint( $_GET['section'] ) );
            echo '<meta http-equiv="refresh" content="0;url='. menu_page_url( 'multiple-authors', false ) .'" />';
            exit;
        }

        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
           || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );
            foreach ( $delete_ids as $id ) {
              self::delete_section( $id );
            }

            echo '<meta http-equiv="refresh" content="0;url='. menu_page_url( 'multiple-authors', false ) .'" />';
            exit;
        }
    }
}
