<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://www.linkedin.com/in/manoj-jamble-206ba6227/
 * @since             1.0.0
 * @package           Wc_Pickup
 *
 * @wordpress-plugin
 * Plugin Name:       WC Pickup
 * Plugin URI:        https://https://github.com/manoj-wisdmlabs/Wp-pickup
 * Description:       WC Pickup plugin allows users to add pickup functionality in their Woocommerce site.
 * Version:           1.0.0
 * Author:            Manoj Jamble
 * Author URI:        https://https://www.linkedin.com/in/manoj-jamble-206ba6227//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-pickup
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WC_PICKUP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-pickup-activator.php
 */
function activate_wc_pickup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-pickup-activator.php';
	Wc_Pickup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-pickup-deactivator.php
 */
function deactivate_wc_pickup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-pickup-deactivator.php';
	Wc_Pickup_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_pickup' );
register_deactivation_hook( __FILE__, 'deactivate_wc_pickup' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-pickup.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

 function run_wc_pickup() {

	$plugin = new Wc_Pickup();
	$plugin->run();

}
run_wc_pickup();
