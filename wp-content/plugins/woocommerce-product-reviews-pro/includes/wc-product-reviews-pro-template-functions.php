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
 * @package   WC-Product-Reviews-Pro/Template
 * @author    SkyVerge
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Template function overrides
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'wc_product_reviews_pro_contributions' ) ) {

	/**
	 * Output the Contributions comments template.
	 *
	 * @since 1.0.0
	 * @param object $comment
	 * @param array $args
	 * @param int $depth
	 */
	function wc_product_reviews_pro_contributions( $comment, $args, $depth ) {
		$type = $comment->comment_type ? $comment->comment_type : 'review';
		wc_get_template( 'single-product/contribution.php', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth, 'type' => $type ) );
	}

}

if ( ! function_exists( 'wc_product_reviews_pro_contribution_comment_form' ) ) {

	/**
	 * Output the contribution comment form template.
	 *
	 * @since 1.0.0
	 * @param object $comment
	 * @param array $args
	 * @param int $depth
	 */
	function wc_product_reviews_pro_contribution_comment_form( $comment, $args, $depth ) {

		if ( ! $comment->comment_parent ) {
			wc_get_template( 'single-product/form-contribution.php', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth, 'type' => 'contribution_comment' ) );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contribution_flag_form' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 * @param object $comment
	 */
	function wc_product_reviews_pro_contribution_flag_form( $comment ) {

		wc_get_template( 'single-product/form-flag-contribution.php', array( 'comment' => $comment ) );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_review_qualifiers_form_controls' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 */
	function wc_product_reviews_pro_review_qualifiers_form_controls() {

		wc_get_template( 'single-product/form-control-review-qualifiers.php' );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_review_qualifiers' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 * @param object $contribution
	 */
	function wc_product_reviews_pro_review_qualifiers( $contribution ) {

		wc_get_template( 'single-product/contribution-review-qualifiers.php', array( 'contribution' => $contribution ) );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contributions_list_title' ) ) {

	/**
	 * Output the contributions list title
	 *
	 * @param  string $current_type Optional
	 * @param  int    $count        Optional
	 * @param  int    $ratiing      Optional
	 */
	function wc_product_reviews_pro_contributions_list_title( $current_type = '', $count = 0, $rating = null ) {

		if ( ! $current_type ) {
			_e( 'What others are saying', WC_Product_Reviews_Pro::TEXT_DOMAIN );
		}
		else {
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $current_type );
			echo $contribution_type->get_list_title( $count, $rating );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contributions_list_no_results_text' ) ) {

	/**
	 * Output the no results text, depending on current type context
	 *
	 * @param  string $current_type
	 */
	function wc_product_reviews_pro_contributions_list_no_results_text( $current_type = '' ) {

		if ( ! $current_type ) {
			_e( 'There are no reviews yet.', WC_Product_Reviews_Pro::TEXT_DOMAIN );
		}
		else {
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $current_type );
			echo $contribution_type->get_no_results_text();
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_get_product_rating_count' ) ) {

	/**
	 * Get the product rating count
	 *
	 * Once https://github.com/woothemes/woocommerce/pull/6284 makes
	 * it to the core, we can remove this
	 *
	 * @param  int $product_id
	 * @param  int $rating     Optional. Rating value to get the count for. By default
	 *                         returns the count for all ratings.
	 * @return int
	 */
	function wc_product_reviews_pro_get_product_rating_count( $product_id, $rating = null ) {

		$product_id = absint( $product_id );

		$rating = intval( $rating );
		$rating_suffix = $rating ? '_' . $rating : '';

		if ( false === ( $count = get_transient( 'wc_rating_count_' . $product_id . $rating_suffix ) ) ) {

			global $wpdb;

			$where_meta_value = $rating ? $wpdb->prepare( " AND meta_value = %d", $rating ) : " AND meta_value > 0";

			$count = $wpdb->get_var( $wpdb->prepare("
				SELECT COUNT(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
			", $product_id ) . $where_meta_value );

			set_transient( 'wc_rating_count_' . $product_id . $rating_suffix, $count, YEAR_IN_SECONDS );
		}

		return $count;
	}

}
