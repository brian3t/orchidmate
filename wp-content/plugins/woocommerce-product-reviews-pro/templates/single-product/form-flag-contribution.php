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

/**
 * Display flag form for a single contribution
 *
 * @since 1.0.0
 */
?>

<form method="post" class="contribution-flag-form" id="flag-contribution-<?php echo $comment->comment_ID; ?>">

	<p><?php _e( 'Something wrong with this post? Thanks for letting us know. If you can point us in the right direction...', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></p>

	<p class="form-row form-row-wide">
		<label for="comment_<?php echo $comment->comment_ID; ?>_flag_reason"><?php _e( 'This post was...', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></label>
		<input type="text" class="input-text input-flag-reason" name="flag_reason" id="comment_<?php echo $comment->comment_ID; ?>_flag_reason">
	</p>

	<p class="form-row form-row-wide">
		<button type="submit" class="button alignright"><?php _e( "Flag for removal", WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></button>
		<div class="clear"></div>
	</p>

  <input type="hidden" name="comment_id" value="<?php echo $comment->comment_ID; ?>">
	<input type="hidden" name="action" value="flag_contribution">

</form>
