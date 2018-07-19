<?php
/**
 * HTML for product reviews report
 *
 */
?>
<div id="poststuff" class="woocommerce-reports-wide wc-product-reviews-pro-report">
	<table class="wp-list-table widefat fixed product-reviews">
		<thead>
			<tr>
				<th><?php _e( 'Product', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Reviews', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Highest Rating', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Lowest Rating', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Average Rating', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Actions', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $products ) ) : ?>
				<?php foreach ( $products as $product ) : ?>
					<tr>
						<td><?php echo get_the_title( $product->id ); ?></td>
						<td><?php echo $product->review_count; ?></td>
						<td><?php echo $product->highest_rating; ?></td>
						<td><?php echo $product->lowest_rating; ?></td>
						<td><?php echo $product->average_rating; ?></td>
						<td>
							<a href="<?php echo get_edit_post_link( $product->id ); ?>"><?php _e( 'View Product', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></a> |
							<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'reviews',  'p' => $product->id, 'comment_type' => 'review' ), admin_url( 'admin.php' ) ) ); ?>"><?php _e( 'View Reviews', WC_Product_Reviews_Pro::TEXT_DOMAIN ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
