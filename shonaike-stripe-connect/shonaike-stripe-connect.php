<?php
/**
 * Plugin Name: Shonaike Stripe Connect
 * Plugin URI:  https://github.com/something-here-eventually
 * Description: Pass custom metadata from our Woo store to Stripe
 * Version:     0.0.2
 * Author:      Lola Shonaike
 * Author URI:  https://github.com/your-name
 * Text Domain: shonaike-stripe-connect
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package ShonaikeStripeConnect
 */

// Declare our namespace.
namespace LolaShonaike\ShonaikeStripeConnect;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Define our plugin version.
define( __NAMESPACE__ . '\VERS', '0.0.1' );

// Plugin root file.
define( __NAMESPACE__ . '\FILE', __FILE__ );

// Define our file base.
define( __NAMESPACE__ . '\BASE', plugin_basename( __FILE__ ) );

// Plugin Folder URL.
define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

// Set our assets URL constant.
define( __NAMESPACE__ . '\ASSETS_URL', URL . 'assets' );

// Set our includes and template path constants.
define( __NAMESPACE__ . '\INCLUDES_PATH', __DIR__ . '/includes' );

// Set the various prefixes for our actions and filters.
define( __NAMESPACE__ . '\HOOK_PREFIX', 'sho_stripe_' );
define( __NAMESPACE__ . '\NONCE_PREFIX', 'sho_stripe_nonce_' );
define( __NAMESPACE__ . '\TRANSIENT_PREFIX', 'sho_stripe_tr_' );
define( __NAMESPACE__ . '\OPTION_PREFIX', 'sho_stripe_setting_' );

// Now we handle all the various file loading.
lolashonaike_ssc_file_load();

/**
 * Actually load our files.
 *
 * @return void
 */
function lolashonaike_ssc_file_load() {

	// Load our metadata handler.
	require_once __DIR__ . '/includes/metadata.php';
}
