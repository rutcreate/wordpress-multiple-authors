<?php
/*
Plugin Name: Multiple Authors
Plugin URI: https://themomentum.co/
Description: Allow to select multiple user to be post's authors.
Version: 1.0.0
Author: Nirut Khemasakchai (@rutcreate)
Author URI: http://rutcreate.com
Text Domain: multiple author
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MULTIPLE_AUTHORS_PLUGIN_DIR', plugin_dir_path( __FILE__ ), true );
define( 'MULTIPLE_AUTHORS_PLUGIN_URL', plugin_dir_url( __FILE__ ), true );
define( 'MULTIPLE_AUTHORS_VERSION', '1.0', true );

if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/helper.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/api.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/classes.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/install.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/ajax.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/admin.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/meta-box.php';
require_once MULTIPLE_AUTHORS_PLUGIN_DIR . 'includes/query.php';
