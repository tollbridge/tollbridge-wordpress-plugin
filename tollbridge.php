<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Tollbridge\Paywall
 *
 * @wordpress-plugin
 * Plugin Name:       Tollbridge Paywall Management
 * Plugin URI:        https://www.tollbridge.co
 * Description:       Implement Tollbridge paywall on your existing Wordpress site.
 * Version:           1.1.0
 * Author:            Tollbridge.co
 * Author URI:        tollbridge.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tollbridge-paywall
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('TOLLBRIDGE_VERSION', '1.0.0');
define('TOLLBRIDGE_BASE_PATH', plugin_dir_path(__FILE__));
define('TOLLBRIDGE_BASE_URL', plugin_dir_url(__FILE__));

// include the Composer autoload file
require TOLLBRIDGE_BASE_PATH . 'autoload.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tollbridge()
{
    $plugin = new \Tollbridge\Paywall\Runner();
    $plugin->run();
}

run_tollbridge();
