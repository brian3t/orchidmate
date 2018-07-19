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

$contribution = wc_product_reviews_pro_get_contribution( $comment );
$rating = $contribution->get_rating();
$title  = $contribution->get_title();
$rating_enabled = $rating && get_option( 'woocommerce_enable_review_rating' ) == 'yes';
?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product" style="display:none;">
			<span itemprop="name"><?php echo $product->get_title(); ?></span>
		</div>

		<small class="contribution-karma">
		<?php if ( $contribution->get_vote_count() ) : ?>
				<?php printf( __( '%1$d out of %2$d people found this helpful', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $contribution->get_positive_votes(), $contribution->get_vote_count() ); ?>
		<?php endif; ?>
		</small>

		<div class="comment-text">

		<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '', get_comment_author() ); ?>

		<?php if ( 'question' == $contribution->get_type() || $title || $rating_enabled ) : ?>
			<h3 class="contribution-title <?php echo esc_attr( $contribution->get_type() ); ?>-title">

				<?php if ( $rating_enabled ) : ?>

					<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $rating ) ); ?>">
						<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%">
							<?php printf( __( '<strong itemprop="ratingValue">%d</strong> out of 5', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $rating ) ; ?>
						</span>
					</span>

				<?php endif; ?>

				<?php echo 'question' == $contribution->get_type() ? __( 'Question', WC_Product_Reviews_Pro::TEXT_DOMAIN ) : $title; ?>
			</h3>
		<?php endif; ?>


			<?php if ( $contribution->moderation == '0' ) : ?>

				<p class="meta"><em><?php _e( 'Your contribution is awaiting approval', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></em></p>

			<?php else : ?>

				<p class="meta">
					<strong itemprop="author"><?php comment_author(); ?></strong> <?php

						if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' )
							if ( wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID ) )
								echo '<em class="verified">(' . __( 'verified owner', WC_Product_Reviews_Pro::TEXT_DOMAIN ) . ')</em> ';

					?>&ndash; <time itemprop="datePublished" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php echo get_comment_date( __( get_option( 'date_format' ), WC_Product_Reviews_Pro::TEXT_DOMAIN ) ); ?></time>
				</p>

			<?php endif; ?>

			<?php if ( 'review' == $type ) : ?>
				<?php wc_product_reviews_pro_review_qualifiers( $contribution ); ?>
			<?php endif; ?>

			<div itemprop="description" class="description"><?php comment_text(); ?></div>

			<?php // TODO: probably should use an action hook here for photos/videos instead??? ?>

			<?php if ( 'photo' == $contribution->get_attachment_type() ) : ?>
				<?php if ( $attachment_url = $contribution->get_attachment_url() ) : ?>
					<img alt="" src="<?php echo esc_url( $attachment_url ); ?>" />
				<?php elseif ( $contribution->has_attachment() && $image = wp_get_attachment_image( $contribution->get_attachment_id(), 'large' ) ) : ?>
					<?php echo $image ?>
				<?php else : ?>
					<p class="attachment-removed"><?php _e( 'Photo has been removed', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></p>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( 'video' == $contribution->get_attachment_type() ) : ?>
				<?php if ( $attachment_url = $contribution->get_attachment_url() ) : ?>
					<?php
						$embed_code = wp_oembed_get( $attachment_url );
						echo $embed_code ? $embed_code : sprintf( '<a href="%s">%s</a>', esc_url( $attachment_url ), $attachment_url );
					?>
				<?php else : ?>
					<p class="attachment-removed"><?php _e( 'Video has been removed', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></p>
				<?php endif; ?>
			<?php endif; ?>

			<p class="contribution-actions">
				<a href="<?php echo esc_url( $contribution->get_vote_url( 'positive' ) ); ?>" class="vote vote-up js-tip <?php if ( 'positive' == $contribution->get_user_vote() ) : ?>done<?php endif; ?>" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" title="<?php _e( 'Upvote if this was helpful', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?>"></a><span class="vote-count vote-count-positive">(<?php echo absint( $contribution->get_positive_votes() ); ?>)</span>
				<a href="<?php echo esc_url( $contribution->get_vote_url( 'negative' ) ); ?>" class="vote vote-down js-tip <?php if ( 'negative' == $contribution->get_user_vote() ) : ?>done<?php endif; ?>" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" title="<?php _e( 'Downvote if this was not helpful', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?>"></a><span class="vote-count vote-count-negative">(<?php echo absint( $contribution->get_negative_votes() ); ?>)</span>
				<span class="feedback"></span>
				<a href="#flag-contribution-<?php echo esc_url( $contribution->get_id() ); ?>" class="flag js-toggle-flag-form js-tip <?php if ( $contribution->has_user_flagged() ) : ?>done<?php endif; ?>" data-comment-id="<?php echo esc_attr( $contribution->get_id() ) ?>" title="<?php _e( 'Flag for removal', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?>"></a>
			</p>

			<?php wc_product_reviews_pro_contribution_flag_form( $comment ); ?>

		</div>
	</div>
