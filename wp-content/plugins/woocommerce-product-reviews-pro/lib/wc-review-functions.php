<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woothemes.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @package   WC-Product-Reviews-Pro/Lib
 * @author    SkyVerge
 * @category  Functions
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Return correct count of reviews for products
 *
 * Wrapper around wp_count_comments
 *
 * @param  int    $product_id Optional. Product ID.
 * @return object Review stats.
 */
function wc_count_reviews( $product_id = 0 ) {

	$GLOBALS['wc_counting_reviews'] = true;

	// Hack: if no product ID was provided,
	// provide a negative ID so that the results
	// for all products are cached separately from regular comments
	//
	// 777 is just a random number
	if ( ! $product_id ) {
		$product_id = -777;
	}

	$stats = wp_count_comments( $product_id );

	$GLOBALS['wc_counting_reviews'] = false;

	return $stats;
}
