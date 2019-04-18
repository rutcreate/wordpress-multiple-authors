<?php

/**
 * Install database's table.
 * TODO Find the way to drop table when plugin is uninstalled.
 */
function multiple_authors_install() {
    global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$wpdb->prefix}multiple_authors (
        post_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
		section varchar(255) NOT NULL DEFAULT '',
        weight int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (post_id, user_id, section)
	) $charset_collate;";
	dbDelta( $sql );

    $sql = "CREATE TABLE {$wpdb->prefix}multiple_authors_section (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL DEFAULT '',
        weight int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (id)
	) $charset_collate;";
	dbDelta( $sql );

    // Default section.
    $wpdb->insert( $wpdb->prefix . 'multiple_authors_section', array(
        'id' => 1,
        'title' => 'Authors',
        'key' => 'author',
        'weight' => -99,
    ) );

	add_option( 'multiple_authors_version', MULTIPLE_AUTHORS_VERSION );
}
register_activation_hook( __FILE__, 'multiple_authors_install' );
