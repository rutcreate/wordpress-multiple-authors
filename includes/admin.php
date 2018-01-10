<?php

/**
 * Add javascript to admin.
 */
function multiple_authors_admin_enqueue_scripts() {
    wp_enqueue_script(
        'ma-autocomplete',
        MULTIPLE_AUTHORS_PLUGIN_DIR . 'assets/js/autocomplete.js',
        array( 'jquery-ui-autocomplete', 'jquery-ui-sortable' ),
        '1.0.0',
        true
    );

    wp_localize_script(
        'ma-autocomplete',
        'maAutocomplete',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'multiple_authors_admin_enqueue_scripts' );
