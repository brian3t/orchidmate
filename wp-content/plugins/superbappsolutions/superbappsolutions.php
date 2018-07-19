<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://superbappsolutions.com
 * @since             1.0.0
 * @package           Superbappsolutions
 *
 * @wordpress-plugin
 * Plugin Name:       Superb App Woocommerce OrchidMate
 * Plugin URI:        superbappsolutions.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Brian Nguyen
 * Author URI:        http://superbappsolutions.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       superbappsolutions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-superbappsolutions-activator.php
 */
function activate_superbappsolutions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-superbappsolutions-activator.php';
	Superbappsolutions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-superbappsolutions-deactivator.php
 */
function deactivate_superbappsolutions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-superbappsolutions-deactivator.php';
	Superbappsolutions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_superbappsolutions' );
register_deactivation_hook( __FILE__, 'deactivate_superbappsolutions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-superbappsolutions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_superbappsolutions() {

	$plugin = new Superbappsolutions();
	$plugin->run();

}
run_superbappsolutions();

/**
 * Deactivate Flat Rate Shipping if products with specific shipping
 * classes are in the cart
 *
 * Add the shipping class slugs to the $shippingclass_array array
 */
add_filter( 'woocommerce_shipping_flat_rate_is_available', 'unset_woocommerce_shipping_methods_flat_rate', 10 ,2 );
function unset_woocommerce_shipping_methods_flat_rate ( $return, $package ) {

	// Setup an array of shipping classes that do not allow Flat Rate Shipping
	$shippingclass_array = array( 'free_shipping' );

	// loop through the cart checking the shipping classes
	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

		$shipping_class = get_the_terms( $values['product_id'], 'product_shipping_class' );

		if ( isset( $shipping_class[0]->slug ) && in_array( $shipping_class[0]->slug, $shippingclass_array ) ) {
			/**
			 * If a product in the cart has a shipping class that does not allow for Flat Rate Shipping
			 * then return false to unset the Flat Rate shipping method and break, no need to carry on
			 */
			return false;
			break;
		}

	}

	/**
	 * It we make it this far then
	 * Flat Rate must be available, return true
	 */
	return true;

}

// Hide standard shipping option when free shipping is available
add_filter( 'woocommerce_shipping_methods', 'hide_standard_shipping_when_orchid_gift_is_available' , 10, 1 );

/**

Hide Standard Shipping option when orchid gift shipping is available
@param array $available_methods */
function hide_standard_shipping_when_orchid_gift_is_available( $available_methods ) {
	global $woocommerce;
	$eligible = array( 'orchid_gift' );

	// get cart contents

		$cart_items = [];
	if (is_object($woocommerce->cart)){
	  $cart_items =  $woocommerce->cart->get_cart();
    }

	$free_shipping_available = false;
	// loop through the items checking to make sure they all have the right class
	foreach ( $cart_items as $key => $item ) {
		if ( isset($item['data']) AND is_object($item['data']) AND in_array( $item['data']->get_shipping_class(), $eligible ) ) {
			// this item doesn't have the right class. return false
			$free_shipping_available = true;
		}
	}

	$free_shipping_key = array_search('WC_Shipping_Free_Shipping', $available_methods);
	$flat_rate_shipping_key = array_search('WC_Shipping_Flat_Rate', $available_methods);
	if( $flat_rate_shipping_key !== false AND $free_shipping_available ) {

// if we have free shipping, remove flat rate shipping option
		unset( $available_methods[$flat_rate_shipping_key] );
	}

	return $available_methods;
}
