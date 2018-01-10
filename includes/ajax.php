<?php

/**
 *
 */
function multiple_authors_ajax_get_users() {
    if ( empty( $_POST ) ) {
        wp_send_json( array(
            'status' => false,
            'message' => 'Post is empty.',
        ) );
        wp_die();
    }

    if ( ! isset( $_POST['s'] ) ) {
        wp_send_json( array(
            'status' => true,
            'data' => array(),
        ) );
        wp_die();
    }

    $args = array(
        'search' => $_POST['s'],
    );

    if ( isset( $_POST['exclude'] ) ) {
        $args['exclude'] = explode( ',', $_POST['exclude'] );
    }

    $users = get_users( $args );
    wp_send_json( array(
        'status' => true,
        'data' => $users,
    ) );
    wp_die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_ma_get_users', 'multiple_authors_ajax_get_users' );
}
