<?php

/**
 * Add javascript to admin.
 */
function multiple_authors_admin_enqueue_scripts() {
    wp_enqueue_script(
        'multiple-authors',
        MULTIPLE_AUTHORS_PLUGIN_URL . 'admin/js/multiple-authors-select.js',
        array( 'jquery-ui-sortable' ),
        '1.0.0',
        true
    );

    $wp_users = get_users();
    $users = array();
    foreach ( $wp_users as $user ) {
        $data = $user->data;
        $users[] = (object) array(
            'id' => $data->ID,
            'display_name' => $data->display_name . ' ('. $data->user_login .')',
        );
    }

    wp_localize_script(
        'multiple-authors',
        'multipleAuthors',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'users' => $users,
        )
    );
}
add_action( 'admin_enqueue_scripts', 'multiple_authors_admin_enqueue_scripts' );
