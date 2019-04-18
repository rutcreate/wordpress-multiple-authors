<?php

function multiple_authors_get_count( $section_id , $user_id ) {
    global $wpdb;
    static $count;
    if ( $count === null ) {
        $count = $wpdb->get_col( "
            SELECT COUNT(*)
            FROM {$wpdb->prefix}multiple_authors
            WHERE user_id = {$user_id} AND section = {$section_id}
        " );
    }
    return $count;
}

/**
 * Return authors.
 */
function get_multiple_authors( $_post = NULL ) {
    global $wpdb;
    global $post;

    if ( ! $_post ) {
        $_post = $post;
    }

    $authors = array();

    if ( ! $post ) {
        return $authors;
    }

    $users = $wpdb->get_results("
        SELECT
            {$wpdb->prefix}multiple_authors_section.title,
            {$wpdb->prefix}multiple_authors.user_id,
            {$wpdb->prefix}multiple_authors.section
        FROM
            {$wpdb->prefix}multiple_authors
        LEFT JOIN
            {$wpdb->prefix}multiple_authors_section
        ON
            {$wpdb->prefix}multiple_authors_section.id = {$wpdb->prefix}multiple_authors.section
        WHERE
            {$wpdb->prefix}multiple_authors.post_id = {$_post->ID}
        ORDER BY
            {$wpdb->prefix}multiple_authors_section.weight ASC,
            {$wpdb->prefix}multiple_authors.weight ASC
    ");

    $result = array();
    if ( count( $users ) > 0 ) {
        foreach ( $users as $user ) {
            if ( ! isset( $result[ $user->section ] ) ) {
                $result[ $user->section ] = (object) array(
                    'title' => $user->title,
                    'users' => array(),
                );
            }
            $result[ $user->section ]->users[] = get_userdata( $user->user_id );
        }
    } 
    else {
        $titles = $wpdb->get_col("
            SELECT
                title
            FROM
                {$wpdb->prefix}multiple_authors_section
            WHERE
                id = 1
        ");
        if ( count( $titles ) > 0 ) {
            $result[1] = (object) array(
                'title' => $titles[0],
                'users' => array( get_userdata( $_post->post_author ) ),
            );
        }
    }

    $sections = array();
    foreach ( $result as $id => $section ) {
    	$sections[] = $section;
    }

    return $sections;
}

/**
 * Return authors by section.
 */
function get_multiple_authors_by_section( $_post = NULL, $section = NULL ) {
    global $wpdb;
    global $post;

    if ( ! $_post ) {
        $_post = $post;
    }

    $authors = array();

    if ( ! $post ) {
        return $authors;
    }

    $section_query = '';
    if ($section) {
        $section_query = " AND {$wpdb->prefix}multiple_authors.section = {$section} ";
    }

    $query = "
        SELECT
            {$wpdb->prefix}multiple_authors_section.title,
            {$wpdb->prefix}multiple_authors.user_id,
            {$wpdb->prefix}multiple_authors.section
        FROM
            {$wpdb->prefix}multiple_authors
        LEFT JOIN
            {$wpdb->prefix}multiple_authors_section
        ON
            {$wpdb->prefix}multiple_authors_section.id = {$wpdb->prefix}multiple_authors.section
        WHERE
            {$wpdb->prefix}multiple_authors.post_id = {$_post->ID}
            {$section_query}
        ORDER BY
            {$wpdb->prefix}multiple_authors_section.weight ASC,
            {$wpdb->prefix}multiple_authors.weight ASC
    ";

    $users = $wpdb->get_results($query);
    // print $query;

    $result = array();
    if ( count( $users ) > 0 ) {
        //print_r($users);
        foreach ( $users as $user ) {
            if ( ! isset( $result[ $user->section ] ) ) {
                $result[ $user->section ] = (object) array(
                    'title' => $user->title,
                    'users' => array(),
                );
            }
            $result[ $user->section ]->users[] = get_userdata( $user->user_id );
        }
    } 
    else {
        $titles = $wpdb->get_col("
            SELECT
                title
            FROM
                {$wpdb->prefix}multiple_authors_section
            WHERE
                id = 1
        ");
        if ( count( $titles ) > 0 ) {
            $result[1] = array(
                'title' => $titles[0],
                'users' => array( get_userdata( $_post->post_author ) ),
            );
        }
    }
    return $result;
}