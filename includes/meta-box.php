<?php

/**
 *
 */
function multiple_authors_add_meta_boxes() {
    global $wpdb;
    $sections = $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}multiple_authors_section ORDER BY weight ASC");
    foreach ($sections as $section) {
        add_meta_box(
            'multiple_authors_'. $section->id,
            $section->title,
            'multiple_authors_meta_box_view',
            'post',
            'advanced',
            'default',
            array( 'section' => $section )
        );
    }
}
add_action( 'add_meta_boxes', 'multiple_authors_add_meta_boxes' );

/**
 *
 */
function multiple_authors_meta_box_view( $post, $box ) {
    global $wpdb;

    extract( $box['args'] );

    if ( ! isset( $section ) ) {
        return;
    }

    $results = $wpdb->get_results("
        SELECT *
        FROM {$wpdb->prefix}multiple_authors
        WHERE section = {$section->id}
        AND post_id = {$post->ID} ORDER BY weight ASC
    ");

    $user_ids = array();
    foreach ( $results as $result) {
        $user_ids[] = $result->user_id;
    }

    $input_name = "multiple_authors[{$section->id}][]";
    $input_value = implode( ',', $user_ids );

    echo '<div class="multiple-authors-meta-box" data-name="'. $input_name .'" data-value="'. $input_value .'">';
    echo '<select></select>';
    echo '<ul class="sortable"></ul>';
    echo '</div>';
    multiple_authors_nonce_field( 'multiple_authors' );
}

/**
 * Save multiple authors from meta box.
 */
function multiple_authors_meta_box_save( $post_id ) {
    global $wpdb;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( ! multiple_authors_verify_nonce( 'multiple_authors' ) ) return;
    if ( ! isset( $_POST['multiple_authors'] ) ) return;

    // Section author (id:1) must be provided.
    $authors = $_POST['multiple_authors'];
    if ( ! is_array( $authors ) ) return;
    if ( count( $authors ) === 0 ) return;
    if ( ! isset( $authors[1] ) ) return;

    // Author is required atleast 1 user.
    $user_ids = $authors[1];
    if ( ! is_array( $user_ids ) ) return;
    if ( count( $user_ids ) === 0 ) return;

    // Validate users existing.
    foreach ( $user_ids as $user_id ) {
        if ( ! $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}users WHERE id = {$user_id}" ) ) {
            return;
        }
    }

    $table_name = "{$wpdb->prefix}multiple_authors";
    $wpdb->query( "DELETE FROM {$table_name} WHERE post_id = {$post_id}" );
    foreach ( $_POST['multiple_authors'] as $section_id => $user_ids ) {
        $section = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}multiple_authors_section WHERE id = {$section_id}" );
        if ( ! $section ) continue;

        foreach ( $user_ids as $weight => $user_id ) {
            if ( $section_id == 1 && $weight == 0 ) {
                $wpdb->query( "UPDATE {$wpdb->prefix}posts SET post_author = {$user_id} WHERE ID = {$post_id}" );
            }
            $wpdb->query( "INSERT INTO {$table_name} (post_id, user_id, section, weight) VALUES ({$post_id}, {$user_id}, {$section_id}, {$weight})" );
        }
    }
}
add_action( 'save_post', 'multiple_authors_meta_box_save' );
