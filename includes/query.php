<?php

/**
 * Override author page query where statement.
 */
function multiple_authors_posts_where( $where, $query ) {
    global $wpdb;

    if ( $query->is_author() ) {
        $user_id = $query->get( 'author' );
        $prefix = $wpdb->prefix;
        $find_text = "({$prefix}posts.post_author = {$user_id})";
        $replace_text = "{$prefix}multiple_authors.post_id IS NOT NULL AND {$prefix}multiple_authors.user_id = {$user_id}";
        $where = str_replace( $find_text, $replace_text, $where );
    }

    return $where;
}
add_filter( 'posts_where', 'multiple_authors_posts_where', 10, 2 );

/**
 * Override author page query join statement.
 */
function multiple_authors_posts_join( $join, $query ) {
    global $wpdb;

    if ( $query->is_author() ) {
        $user_id = $query->get( 'author' );
        $join = "LEFT JOIN {$wpdb->prefix}multiple_authors ON
            {$wpdb->prefix}multiple_authors.post_id = {$wpdb->prefix}posts.ID";
    }

    return $join;
}
add_filter( 'posts_join', 'multiple_authors_posts_join', 10, 2 );