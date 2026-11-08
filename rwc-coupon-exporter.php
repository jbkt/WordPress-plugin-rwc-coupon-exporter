<?php
/**
 * Plugin Name: RWC Coupon Exporter for WooCommerce
 * Plugin URI: https://github.com/Reliefcreation/WordPress-plugin-rwc-coupon-exporter
 * Description: Export WooCommerce coupons to CSV file
 * Version: 1.3.4
 * Author: RELIEF Creation
 * Author URI: https://reliefcreation.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rwc-coupon-exporter
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 3.0
 * WC tested up to: 11.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('RWC_COUPON_EXPORTER_VERSION', '1.3.4');
define('RWC_COUPON_EXPORTER_FILE', __FILE__);
define('RWC_COUPON_EXPORTER_PATH', plugin_dir_path(__FILE__));

// Load required files
require_once RWC_COUPON_EXPORTER_PATH . 'includes/class-rwc-coupon-exporter.php';
require_once RWC_COUPON_EXPORTER_PATH . 'includes/class-rwc-coupon-exporter-admin.php';
require_once RWC_COUPON_EXPORTER_PATH . 'includes/class-rwc-coupon-exporter-handler.php';

/**
 * Declare compatibility with WooCommerce features (HPOS, Cart & Checkout Blocks)
 */
add_action('before_woocommerce_init', function() {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
});

/**
 * Check if WooCommerce is active and compatible
 */
function rwc_coupon_exporter_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        /* translators: %s: Plugin name "WooCommerce" that needs to be installed and activated */
        wp_die(
            sprintf(
                esc_html__('RWC Coupon Exporter requires %s to be installed and activated.', 'rwc-coupon-exporter'),
                '<strong>WooCommerce</strong>'
            ),
            'Plugin Activation Error',
            array('back_link' => true)
        );
    }

    if (class_exists('WooCommerce') && defined('WC_VERSION') && version_compare(WC_VERSION, '3.0', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        /* translators: %s: Required minimum WooCommerce version number */
        wp_die(
            sprintf(
                esc_html__('RWC Coupon Exporter requires WooCommerce version %s or higher.', 'rwc-coupon-exporter'),
                '3.0'
            ),
            'Plugin Activation Error',
            array('back_link' => true)
        );
    }
}

/**
 * Load plugin text domain
 */
function rwc_coupon_exporter_load_textdomain() {
    load_plugin_textdomain('rwc-coupon-exporter', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'rwc_coupon_exporter_load_textdomain');

/**
 * Initialize plugin
 */
function rwc_coupon_exporter_init() {
    // Initialize main plugin class (handles dependency and version checks)
    $plugin = RWC_Coupon_Exporter::get_instance();
    $plugin->init();
}

// Register activation hook
register_activation_hook(__FILE__, 'rwc_coupon_exporter_check_woocommerce');

// Initialize plugin
add_action('plugins_loaded', 'rwc_coupon_exporter_init');