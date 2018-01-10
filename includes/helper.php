<?php

/**
 * Output nonce field.
 */
function multiple_authors_nonce_field( $name, $referer = true, $echo = true ) {
    return wp_nonce_field( '_multiple_authors_nonce_'. $name .'_value', '_multiple_authors_nonce_'. $name, $referer, $echo );
}

/**
 * Verify nonce.
 */
function multiple_authors_verify_nonce( $action ) {
    $nonce = multiple_authors_get_nonce( $action );
    return wp_verify_nonce( $nonce, '_multiple_authors_nonce_'. $action .'_value' );
}

/**
 * Return nonce from specified action.
 */
function multiple_authors_get_nonce( $action ) {
    if ( isset( $_POST['_multiple_authors_nonce_'. $action] ) ) {
        return $_POST['_multiple_authors_nonce_'. $action];
    }
}
