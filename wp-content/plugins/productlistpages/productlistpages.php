<?php

/**
 * @package ProductListPages
 * @copyright creativeG.gr - Web Design Services.
 */
/*
  Plugin Name: Product List Pages for WooCommerce
  Plugin URI: http://www.creativeg.gr
  Description: Product list pages. List WordPress products into a single page, for mass buy-out.
  Author: Basilis Kanonidis.
  Author URI: http://www.creativeg.gr
  Version: 1.6.2
 */

if ( !function_exists( 'add_action' ) ) {
	echo "DIEEEE!!!!!";
	exit;
}

if (is_admin()) {
    require_once dirname(__FILE__) . '/adminpanel.php';
}

add_action('plugins_loaded', 'plpInit', 0);

function plpInit() {
    /**
    * Check if WooCommerce is active
    **/
   if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
       require_once dirname( __FILE__ ) . '/plugin.php';
   }
}