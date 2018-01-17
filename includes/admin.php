<?php

/**
 * Add javascript to admin.
 */
function multiple_authors_admin_enqueue_scripts( $hook ) {
    if ( $hook === 'post.php' ) {
        wp_enqueue_script(
            'multiple-authors',
            MULTIPLE_AUTHORS_PLUGIN_URL . 'admin/js/multiple-authors-select.js',
            array( 'jquery-ui-sortable' ),
            '1.0.0',
            true
        );

        $wp_users = get_users();
        $users = array();
        foreach ( $wp_users as $user ) {
            $data = $user->data;
            $users[] = (object) array(
                'id' => $data->ID,
                'display_name' => $data->display_name . ' ('. $data->user_login .')',
            );
        }

        wp_localize_script(
            'multiple-authors',
            'multipleAuthors',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'users' => $users,
            )
        );
    }

    else if ( $hook === 'multiple-authors_page_multiple-authors-section-order' ) {
        wp_enqueue_script(
            'multiple-authors-section',
            MULTIPLE_AUTHORS_PLUGIN_URL . 'admin/js/multiple-authors-section-order.js',
            array( 'jquery-ui-sortable' ),
            '1.0.0',
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'multiple_authors_admin_enqueue_scripts', 10, 1 );

function multiple_authors_admin_print_scripts() {
    echo '<script>rutcreate</script>';
}
add_action( 'admin_print_scripts-admin.php', 'multiple_authors_admin_print_scripts', 10 );

/**
 * Add admin menu.
 */
function multiple_authors_admin_menu() {
    add_menu_page(
        'Multiple Authors',
        'Multiple Authors',
        'manage_options',
        'multiple-authors',
        'multiple_authors_section_list'
    );

    add_submenu_page(
        'multiple-authors',
        'Add New Section',
        'Add New',
        'manage_options',
        'multiple-authors-section-add',
        'multiple_authors_section_add'
    );

    add_submenu_page(
        null,
        'Edit Section',
        'Edit Section',
        'manage_options',
        'multiple-authors-section-edit',
        'multiple_authors_section_edit'
    );

    add_submenu_page(
        'multiple-authors',
        'Section Order',
        'Section Order',
        'manage_options',
        'multiple-authors-section-order',
        'multiple_authors_section_order'
    );

    add_submenu_page(
        'multiple-authors',
        'Advanced',
        'Advanced',
        'manage_options',
        'multiple-authors-section-advanced',
        'multiple_authors_section_advanced'
    );
}
add_action( 'admin_menu', 'multiple_authors_admin_menu' );

/**
 * List section.
 */
function multiple_authors_section_list() {
    $table = new Multiple_Authors_Section_List_Table();
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Multiple Authors Section</h1>
        <a href="<?php menu_page_url( 'multiple-authors-section-add', true ) ?>" class="page-title-action">Add New</a>
        <hr class="wp-header-end" />

        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php $table->prepare_items(); ?>
                            <?php $table->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
<?php
}

/**
 * Add section form.
 */
function multiple_authors_section_add() {
?>
    <div class="wrap">
        <h1 class="">Add New Section</h1>
        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <input type="hidden" name="action" value="multiple_authors_section_add" />
            <table class="form-table">
                <tr>
                    <th scope="row">Title</th>
                    <td><input type="text" name="title" class="" /></td>
                </tr>
            </table>
            <?php wp_nonce_field( 'multiple-authors-add' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

/**
 * Edit section form.
 */
function multiple_authors_section_edit() {
    if ( ! isset( $_GET['id'] ) ) {
        die( 'Something went wrong.' );
    }

    global $wpdb;
    $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}multiple_authors_section WHERE id = ". $_GET['id'] );

    if ( ! $row ) {
        die( 'Section not found.' );
    }
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Edit Section</h1>
        <a href="<?php menu_page_url( 'multiple-authors-section-add', true ) ?>" class="page-title-action">Add New</a>
        <hr class="wp-header-end" />

        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <input type="hidden" name="action" value="multiple_authors_section_update" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <table class="form-table">
                <tr>
                    <th scope="row">Title</th>
                    <td><input type="text" name="title" value="<?php echo $row->title; ?>" /></td>
                </tr>
            </table>
            <?php wp_nonce_field( 'multiple-authors-edit' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

/**
 * Section order form.
 */
function multiple_authors_section_order() {
    global $wpdb;
    $sections = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}multiple_authors_section ORDER BY weight ASC" );
?>
    <div class="wrap">
        <h1 class="">Section Order</h1>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <ul class="sortable">
                <?php foreach ( $sections as $section ): ?>
                <li style="cursor: move; background: #f7f7f7; border: 1px solid #ddd; padding: 5px 10px;">
                    <?php echo $section->title; ?>
                    <input type="hidden" name="sections[]" value="<?php echo $section->id; ?>" />
                </li>
                <?php endforeach; ?>
            </ul>

            <input type="hidden" name="action" value="multiple_authors_section_order" />
            <?php wp_nonce_field( 'multiple-authors-order' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

/**
 * Section order form.
 */
function multiple_authors_section_advanced() {
?>
    <div class="wrap">
        <h1 class="">Advanced</h1>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="multiple_authors_section_advanced" />
            <?php wp_nonce_field( 'multiple-authors-advanced' ); ?>
            <?php submit_button( 'Import author from original post', 'primary', 'import_post_author' ); ?>
        </form>
    </div>
<?php
}

/**
 * Create section.
 */
function multiple_authors_section_post_add() {
    if ( ! isset( $_POST['_wpnonce'] ) ) {
        die('Access denied.');
    }

    $nonce = $_POST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'multiple-authors-add' ) ) {
      die( 'Access denied.' );
    }

    if ( isset ( $_POST['title'] ) && $_POST['title'] ) {
        if ( $title = sanitize_text_field( $_POST['title'] ) ) {
            global $wpdb;
            $weights = $wpdb->get_col( "SELECT weight FROM {$wpdb->prefix}multiple_authors_section ORDER BY weight DESC ");
            $weight = count( $weights ) === 0 ? 0 : $weights[0] + 1;
            $result = $wpdb->insert(
                "{$wpdb->prefix}multiple_authors_section",
                array( 'title' => $title, 'weight' => $weight ),
                array( '%s', '%d' )
            );
            if ( $result ) {
                $insert_id = $wpdb->insert_id;
                wp_redirect( admin_url( 'admin.php?page=multiple-authors' ) );
                exit;
            }
        }
    }

    wp_redirect( admin_url( 'admin.php?page=multiple-authors' ) );
    exit;
}
add_action( 'admin_post_multiple_authors_section_add', 'multiple_authors_section_post_add' );

/**
 * Update section.
 */
function multiple_authors_section_post_update() {
    if ( ! isset( $_POST['_wpnonce'] ) ) {
        die('Access denied.');
    }

    $nonce = $_POST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'multiple-authors-edit' ) ) {
      die( 'Access denied.' );
    }

    if ( ! isset( $_POST['id'] ) || ! $_POST['id'] ) {
        die('Something went wrong.');
    }

    if ( isset ( $_POST['title'] ) && $_POST['title'] ) {
        if ( $title = sanitize_text_field( $_POST['title'] ) ) {
            global $wpdb;
            $result = $wpdb->update(
                "{$wpdb->prefix}multiple_authors_section",
                array( 'title' => $title ),
                array( 'id' => $_POST['id'] ),
                array( '%s' ),
                array( '%d' )
            );
            if ( $result ) {
                wp_redirect( admin_url( 'admin.php?page=multiple-authors' ) );
                exit;
            }
        }
    }

    wp_redirect( admin_url( 'admin.php?page=multiple-authors' ) );
    exit;
}
add_action( 'admin_post_multiple_authors_section_update', 'multiple_authors_section_post_update' );

/**
 * Save order.
 */
function multiple_authors_section_post_order() {
    if ( ! isset( $_POST['_wpnonce'] ) ) {
        die('Access denied.');
    }

    $nonce = $_POST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'multiple-authors-order' ) ) {
      die( 'Access denied.' );
    }

    if ( ! isset( $_POST['sections'] ) ) {
        die( 'Something went wrong.' );
    }

    global $wpdb;
    $sections = $_POST['sections'];
    foreach ( $sections as $weight => $section ) {
        $wpdb->update(
            "{$wpdb->prefix}multiple_authors_section",
            array( 'weight' => $weight ),
            array( 'id' => $section ),
            array( '%d' ),
            array( '%d' )
        );
    }

    wp_redirect( admin_url( 'admin.php?page=multiple-authors-section-order' ) );
    exit();
}
add_action( 'admin_post_multiple_authors_section_order', 'multiple_authors_section_post_order' );

/**
 * 
 */
function multiple_authors_section_post_advanced() {
    if ( ! isset( $_POST['_wpnonce'] ) ) {
        die('Access denied.');
    }

    $nonce = $_POST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'multiple-authors-advanced' ) ) {
      die( 'Access denied.' );
    }

    global $wpdb;
    $posts = $wpdb->get_results( "SELECT ID, post_author FROM {$wpdb->prefix}posts WHERE post_type = 'post' and post_status != 'auto-draft'" );
    $table_name = "{$wpdb->prefix}multiple_authors";
    foreach ( $posts as $post ) {
        $wpdb->delete(
            $table_name,
            array(
                'section' => 1,
                'post_id' => $post->ID,
                'user_id' => $post->post_author,
            ),
            array( '%d', '%d', '%d' )
        );
        $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post->ID,
                'user_id' => $post->post_author,
                'section' => 1,
                'weight' => -99,
            ),
            array( '%d', '%d', '%d', '%d' )
        );
    }

    wp_redirect( admin_url( 'admin.php?page=multiple-authors' ) );
    exit();
}
add_action( 'admin_post_multiple_authors_section_advanced', 'multiple_authors_section_post_advanced' );
