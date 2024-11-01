<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.lehelmatyus.com
 * @since             1.0.2
 * @package           Share_By_Email
 *
 * @wordpress-plugin
 * Plugin Name:       Share by Email
 * Plugin URI:        www.lehelmatyus.com/my-wordpress-plugins/share-by-email
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.2
 * Author:            Lehel Mátyus
 * Author URI:        https://www.lehelmatyus.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       share-by-email
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.2 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SHARE_BY_EMAIL_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-share-by-email-activator.php
 */
function activate_share_by_email() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-share-by-email-activator.php';
	Share_By_Email_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-share-by-email-deactivator.php
 */
function deactivate_share_by_email() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-share-by-email-deactivator.php';
	Share_By_Email_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_share_by_email' );
register_deactivation_hook( __FILE__, 'deactivate_share_by_email' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-share-by-email.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.2
 */
function run_share_by_email() {

	$plugin = new Share_By_Email();
	$plugin->run();

}
run_share_by_email();
