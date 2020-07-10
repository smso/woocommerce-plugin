<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.smso.ro/
 * @since             1.0.0
 * @package           smso
 *
 * @wordpress-plugin
 * Plugin Name:       SMS Order Notifications
 * Plugin URI:        https://github.com/smso/woocommerce-plugin
 * Description:       Engage with your customers through SMS every step of the way, from order submission to delivery.
 * Version:           1.0.0
 * Author:            SMSO
 * Author URI:        https://www.smso.ro/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smso
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action('admin_notices', 'smso_install_message');
    return;
}
function smso_install_message(){
	echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Sorry!<strong> But woocommerce plugin is required if you want to install and work with SMSO plugin.</p>
            <p>SMSO send you best regards!</p>
          </div>';
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SMSO_VERSION', '1.0.0' );
define( 'SMSO_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_smso() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/smso-activator.php';
	Smso_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_smso() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/smso-deactivator.php';
	Smso_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_smso' );
register_deactivation_hook( __FILE__, 'deactivate_smso' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/smso.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_smso()
{
	$plugin = new Smso();
	$plugin->run();
}
run_smso();
