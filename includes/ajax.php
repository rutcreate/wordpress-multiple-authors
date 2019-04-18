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

    if ( ! isset( $_POST['search'] ) ) {
        wp_send_json( array(
            'status' => false,
            'message' => 'No search data',
            'data' => array(),
        ) );
        wp_die();
    }

	$user_query = new WP_User_Query( array(
		'search'         => '*'.esc_attr( $_POST['search'] ).'*',
		'search_columns' => array(
			'user_login',
			'display_name',
		),
		'number' => $_POST['ipp'] ?: 10,
	) );
	$users = $user_query->get_results();

    $items = array();
    foreach ( $users as $user ) {
    	$items[] = array(
    		'id' => $user->ID,
		    'label' => $user->data->display_name . ' ('. $user->data->user_login .')',
	    );
    }
    wp_send_json( array(
        'status' => true,
        'data' => $items,
    ) );
    wp_die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_ma_get_users', 'multiple_authors_ajax_get_users' );
}
