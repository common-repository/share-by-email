<?php

/**
 * Add Form Elements
 */
if (!class_exists('WpLHLAdminUiForm')) {
    include plugin_dir_path( dirname( __FILE__ ) ) . 'wp-lhl-admin-ui/wp-lhl-admin-ui-form.php';
}

/**
 * Add Pages
 */
if (!class_exists('WpLHLAdminUptimeGhost')) {
    include plugin_dir_path( dirname( __FILE__ ) ) . 'wp-lhl-admin-ui/wp-lhl-admin-ui-uptimeghost.php';
}

function wp_lhl_admin_ui_styles() {
    wp_enqueue_style('wp-lhl-admin-ui-styles',  plugin_dir_url( dirname( __FILE__ ) ) . 'wp-lhl-admin-ui/css/wp-lhl-admin-ui.css');
}
add_action('admin_enqueue_scripts', 'wp_lhl_admin_ui_styles');