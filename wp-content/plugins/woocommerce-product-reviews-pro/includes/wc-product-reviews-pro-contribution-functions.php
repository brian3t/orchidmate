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
 * @package   WC-Product-Reviews-Pro/Functions
 * @author    SkyVerge
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Global functions for review contributions
 *
 * @since 1.0.0
 */

/**
 * Main function for returning contributions, uses the WC_Product_Reviews_Pro_Contribution_Factory class.
 *
 * @since 1.0.0
 * @param mixed $the_contribution Comment object or comment ID of the contribution.
 * @param array $args (default: array()) Contains all arguments to be used to get this contribution.
 * @return WC_Contribution
 */
function wc_product_reviews_pro_get_contribution( $the_contribution = false, $args = array() ) {
	return wc_product_reviews_pro()->contribution_factory->wc_product_reviews_pro_get_contribution( $the_contribution, $args );
}


/**
 * Get number of comments, optionally filtered by type
 */
function wc_product_reviews_pro_get_comment_count( $comments, $type = null ) {

	if ( ! $type ) {
		return count( $comments );
	}

	$count = 0;

	foreach ( $comments as $comment ) {

		if ( $type == $comment->comment_type ) {
		  $count++;
		}
	}

	return $count;
}


/**
 * Get number of comments, filtered by type(s)
 *
 * @since 1.0.0
 * @param string|array $types
 * @return int
 */
function wc_product_reviews_pro_get_comments_number( $post_id = 0, $types = array() ) {

	// Cast single type to array
	if ( ! is_array( $types ) ) {
		$types = array( $types );
	}

	global $wpdb;
	$select = $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1", $post_id );

	$where_type = '';
	if ( ! empty( $types ) ) {
		$where_type = $wpdb->prepare( " AND comment_type IN(" . implode( ', ', array_fill( 0, count( $types ), '%s' ) ) . ")", $types );
	}

	return $wpdb->get_var( $select . $where_type );
}


/**
 * Get a contribution type class instance
 *
 * @since 1.0.0
 * @param string $type Contribution type
 * @return object Instance of WC_Product_Reviews_Pro_Contribution_Type
 */
function wc_product_reviews_pro_get_contribution_type( $type ) {
	return new WC_Product_Reviews_Pro_Contribution_Type( $type );
}


/**
 * Get product review count
 *
 * @since 1.0.0
 * @param  int $product_id Product ID
 * @return int Number of reviews for this product
 */
function wc_product_reviews_pro_get_review_count( $product_id ) {

	if ( false === ( $count = get_transient( 'wc_product_reviews_pro_review_count_' . $product_id ) ) ) {

		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare( "
		  SELECT COUNT(comment_ID) FROM $wpdb->comments
		  WHERE comment_post_ID = %d
		  AND comment_approved = '1'
		  AND comment_type = 'review'
		", $product_id ) );

		set_transient( 'wc_product_reviews_pro_review_count_' . $product_id, $count, YEAR_IN_SECONDS );
	}

	return $count;
}


/**
 * Get product's highest rating count
 *
 * @since 1.0.0
 * @param  int $product_id Product ID
 * @return int Highest rating for this product
 */
function wc_product_reviews_pro_get_highest_rating( $product_id ) {

	if ( false === ( $rating = get_transient( 'wc_product_reviews_pro_highest_rating_' . $product_id ) ) ) {

		global $wpdb;

		$rating = $wpdb->get_var( $wpdb->prepare("
		  SELECT MAX(meta_value) FROM $wpdb->commentmeta
		  LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		  WHERE comment_post_ID = %d
		  AND comment_approved = '1'
		  AND comment_type = 'review'
		  AND meta_key = 'rating'
		  AND meta_value > 0
		", $product_id ) );

		set_transient( 'wc_product_reviews_pro_highest_rating_' . $product_id, $rating, YEAR_IN_SECONDS );
	}

	return $rating;
}


/**
 * Get product's lowest rating count
 *
 * @since 1.0.0
 * @param  int $product_id Product ID
 * @return int Lowest rating for this product
 */
function wc_product_reviews_pro_get_lowest_rating( $product_id ) {

	if ( false === ( $rating = get_transient( 'wc_product_reviews_pro_lowest_rating_' . $product_id ) ) ) {

		global $wpdb;

		$rating = $wpdb->get_var( $wpdb->prepare("
		  SELECT MIN(meta_value) FROM $wpdb->commentmeta
		  LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		  WHERE comment_post_ID = %d
		  AND comment_approved = '1'
		  AND comment_type = 'review'
		  AND meta_key = 'rating'
		  AND meta_value > 0
		", $product_id ) );

		set_transient( 'wc_product_reviews_pro_lowest_rating_' . $product_id, $rating, YEAR_IN_SECONDS );
	}

	return $rating;
}


/**
 * Get the currently applied comment filters
 *
 * @since 1.0.0
 * @return array|null
 */
function wc_product_reviews_pro_get_current_comment_filters() {

	$comments_filter = isset( $_REQUEST['comments_filter'] ) ? $_REQUEST['comments_filter'] : null;
	$filters = array();

	if ( $comments_filter ) {
		parse_str( $comments_filter, $filters );
	}

	return array_filter( $filters );
}


/**
 * Get the form field value from session
 *
 * @since 1.0.0
 * @return mixed|null
 */
function wc_product_reviews_pro_get_form_field_value( $key ) {
	return isset( $_POST[ $key ] ) ? $_POST[ $key ] : null;
}
