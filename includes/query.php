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

        $section_where = '';
        if ( $sections = get_option( 'multiple_authors_allowed_sections' ) ) {
			$section_where = " AND {$prefix}multiple_authors.section IN ({$sections})";
        }

        $replace_text = "
        (
	        (
	            {$find_text} OR
		        {$prefix}multiple_authors.user_id = {$user_id}
		    ) {$section_where}
		)";
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

/**
 *
 */
function multiple_authors_get_the_archive_title( $title ) {
    if ( is_author() ) {
        $author = get_queried_object();
        if ( $author ) {
            $title = sprintf( __( 'Author: %s' ), '<span class="vcard">' . $author->data->display_name . '</span>' );
        }
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'multiple_authors_get_the_archive_title', 10, 1 );
