<?php

/**
 *
 */
function multiple_authors_add_meta_boxes() {
    add_meta_box(
        'multiple_authors',
        'Authors',
        'multiple_authors_meta_box_view',
        'post'
    );
}
add_action( 'add_meta_boxes', 'multiple_authors_add_meta_boxes' );

/**
 *
 */
function multiple_authors_meta_box_view( $post, $box ) {
    global $wpdb;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}multiple_authors WHERE post_id = 214");
    print_r($results);
    // echo '<div class="search-autocomplete" data-value="'. $value .'" data-name="'. $name .'" data-max="'. $max .'">';
    // $post_ids = explode( ',', get_option( $name, '' ) );
    // echo '<ul class="sortable" style="margin-top:0px">';
    // if ( $post_ids ) {
    //     $posts = get_posts( array(
    //         'include' => $post_ids,
    //         'orderby' => 'post__in',
    //     ) );
    //
    //     foreach ( $posts as $post ) {
    //         echo '<li class="item" data-id="'. $post->ID .'">';
    //         echo '<a href="#" class="button button-small remove">&#10007;</a> ';
    //         echo $post->post_title .' (id:'. $post->ID .')';
    //         echo '</li>';
    //     }
    // }
    // echo '</ul>';
    // echo '</div>';
    echo '<p><input type="text" name="newtag[post_tag]" class="form-input-tip ui-autocomplete-input" size="16" autocomplete="off" value=""></p>';
}

/**
 * Save multiple authors from meta box.
 */
function multiple_authors_meta_box_save() {
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( ! multiple_authors_verify_nonce( 'multiple_authors' ) ) return;

    if ( isset( $_POST['multiple_authors'] ) ) {
        // update_post_meta( $post_id, '_themomentum_banner_style', $_POST['banner_style'] );
    }
}
add_action( 'save_post', 'multiple_authors_meta_box_save' );
