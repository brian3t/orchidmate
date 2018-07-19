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
 * @package   WC-Product-Reviews-Pro/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

$review_qualifiers = wp_get_post_terms( $product->id, 'product_review_qualifier' );
?>

<?php if ( $review_qualifiers ) : ?>

<div class="review-qualifiers">
<?php foreach ( $review_qualifiers as $review_qualifier ) : ?>

	<?php
		$qualifier_options = array_filter( explode( "\n", get_woocommerce_term_meta( $review_qualifier->term_id, 'options' ) ) );

		$options = array();
		foreach ( $qualifier_options as $option ) {
			$options[ $option ] = $option;
		}

		if ( empty( $options ) ) {
			continue;
		}

		$key = 'wc_product_reviews_pro_review_qualifier_' . $review_qualifier->term_id;

		$args = array(
			'type'    => 'select',
			'label'   => $review_qualifier->name,
			'options' => $options
		);
	?>

	<?php woocommerce_form_field( $key, $args ); ?>

<?php endforeach; ?>
</div>

<?php endif; ?>
