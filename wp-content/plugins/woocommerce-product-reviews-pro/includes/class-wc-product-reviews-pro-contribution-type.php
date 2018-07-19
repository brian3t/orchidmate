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
 * @package   WC-Product-Reviews-Pro/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC Product Reviews Pro Contribution Type class
 *
 * Handles contribution type specifics, such as title, call to action, fields, etc.
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Contribution_Type {


	/** @public string contribution type */
	public $type;


	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @param string $type
	 */
	public function __construct( $type ) {

		$this->type = $type;
	}


	/**
	 * Get the title for the contribution type
	 *
	 * @return string
	 */
	public function get_title() {

		switch ( $this->type ) {

			case 'review':

				$title = __( 'Review', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'question':

				$title = __( 'Question', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'photo':

				$title = __( 'Photo', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$title = __( 'Video', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'contribution_comment':

				$title = __( 'Comment', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			// Default behaviour is to capitalize the first letter of the type
			default:

				$title = ucfirst( $this->type );
			break;

		}

		/**
		 * Filter contribution type title
		 *
		 * @since 1.0.0
		 * @param string $title The title
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_title', $title, $this->type );
	}


	/**
	 * Get the call to action for the contribution type
	 *
	 * @return string
	 */
	public function get_call_to_action() {

		$cta = '';

		switch ( $this->type ) {

			case 'review':

				$cta = __( 'Leave a Review', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'question':

				$cta = __( 'Ask a Question', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'photo':

				$cta = __( 'Post a Photo', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$cta = __( 'Post a Video', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

		}

		/**
		 * Filter contribution type call to action
		 *
		 * @since 1.0.0
		 * @param string $cta The call to action
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_call_to_action', $cta, $this->type );
	}


	/**
	 * Get the contribution list title for the contribution type
	 *
	 * @param  int    $count  Number of contributions
	 * @param  int    $rating Optional. Review rating (applies only to reviews)
	 * @return string
	 */
	public function get_list_title( $count, $rating = null ) {

		$list_title = '';

		switch ( $this->type ) {

			case 'review':

				if ( $rating > 0 ) {
					$list_title = sprintf( _n( 'One review with a %2$d-star rating', '%1$d reviews with a %2$d-star rating', $count, WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count, $rating );
				} else {
					$list_title = sprintf( _n( 'One review', '%d reviews', $count, WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
				}
			break;

			case 'question':

				$list_title = sprintf( _n( 'One question', '%d questions', $count, WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

			case 'photo':

				$list_title = sprintf( _n( 'One photo', '%d photos', $count, WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

			case 'video':

				$list_title = sprintf( _n( 'One video', '%d videos', $count, WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

		}

		/**
		 * Filter the list title for the contribution type
		 *
		 * @since 1.0.0
		 * @param string $list_title The list title for the contribution type
		 * @param string $type The contribution type
		 * @param int $count The number of contributions
		 * @param int $rating Review rating (applies only to reviews)
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_list_title', $list_title, $this->type, $count, $rating );
	}


	/**
	 * Get the tab title for the contribution type
	 *
	 * @param int $count  Number of contributions
	 * @return string
	 */
	public function get_tab_title( $count ) {

		switch ( $this->type ) {

			case 'review':

				$tab_title = sprintf( __( 'Reviews (%d)', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

			case 'question':

				$tab_title = sprintf( __( 'Questions (%d)', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

			case 'photo':

				$tab_title = sprintf( __( 'Photos (%d)', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

			case 'video':

				$tab_title = sprintf( __( 'Videos (%d)', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;

			default:

				$tab_title = sprintf( __( 'Discussion (%d)', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $count );
			break;
		}

		/**
		 * Filter the tab title for the contribution type
		 *
		 * @since 1.0.0
		 *
		 * @param string $tab_title The tab title
		 * @param string $type The contribution type
		 * @param int $count The number of contributions
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_tab_title', $tab_title, $this->type, $count );
	}


	/**
	 * Get the frontend filter title for the contribution type
	 *
	 * @return string
	 */
	public function get_filter_title() {

		$filter_title = '';

		switch ( $this->type ) {

			case 'review':

				$filter_title = __( 'Show all reviews', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'question':

				$filter_title = __( 'Show all questions', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'photo':

				$filter_title = __( 'Show all photos', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$filter_title = __( 'Show all videos', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;
		}

		/**
		 * Filter contribution type filter title
		 *
		 * @since 1.0.0
		 * @param string $filter_title The frontend filter title for the contribution type
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_filter_title', $filter_title, $this->type );
	}


	/**
	 * Get the button text for the contribution type
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_button_text() {

		switch ( $this->type ) {

			case 'review':

				$button_text = __( 'Save Review', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'question':

				$button_text = __( 'Save Question', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'photo':

				$button_text = __( 'Save Photo', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$button_text = __( 'Save Video', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			default:

				$button_text = sprintf( __( 'Save %s', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $this->get_title() );
			break;

		}

		/**
		 * Filter contribution type button text
		 *
		 * @since 1.0.0
		 * @param string $button_text The button text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_button_text', $button_text, $this->type );
	}


	/**
	 * Get the no results text for the contribution type
	 *
	 * @return string
	 */
	public function get_no_results_text() {

		$text = __( 'There are no reviews yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );

		$filters = wc_product_reviews_pro_get_current_comment_filters();

		switch ( $this->type ) {

			case 'review':

				if ( ! empty( $filters ) && isset( $filters['helpful'] ) && $filters['helpful'] ) {

					$text = __( 'There are no helpful reviews yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );

					if ( isset( $filters['classification'] ) && 'positive' == $filters['classification'] ) {

						$text = __( 'There are no helpful positive reviews yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );
					}
					else if ( isset( $filters['classification'] ) && 'negative' == $filters['classification'] ) {

						$text = __( 'There are no helpful negative reviews yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );
					}

				} else if ( ! empty( $filters ) && isset( $filters['rating'] ) && $filters['rating'] ) {

					$text = sprintf( __(' There are no reviews with a %d-star rating yet', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $filters['rating'] );
				} else {

					$text = __( 'There are no reviews yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );
				}
			break;

			case 'question':

				if ( ! empty( $filters ) && isset( $filters['unanswered'] ) && $filters['unanswered'] ) {

					$text = __( 'There are no unanswered questions', WC_Product_Reviews_Pro::TEXT_DOMAIN );
				} else {

					$text = __( 'There are no questions yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );
				}
			break;

			case 'photo':

				$text = __( 'There are no photos yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$text = __( 'There are no videos yet', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

		}

		/**
		 * Filter contribution type no results text
		 *
		 * @since 1.0.0
		 * @param string $text The text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_no_results_text', $text, $this->type );
	}


	/**
	 * Get the edit text for the contribution type
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_edit_text() {

		switch ( $this->type ) {

			case 'review':

				$text = __( 'Edit Review', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'question':

				$text = __( 'Edit Question', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'photo':

				$text = __( 'Edit Photo', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$text = __( 'Edit Video', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			default:

				$text = sprintf( __( 'Edit %s', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $this->get_title() );
			break;

		}

		/**
		 * Filter contribution type edit text
		 *
		 * @since 1.0.0
		 * @param string $edit The edit text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_edit_text', $text, $this->type );
	}


	/**
	 * Get the moderate text for the contribution type
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_moderate_text() {

		switch ( $this->type ) {

			case 'review':

				$text = __( 'Moderate Review', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'question':

				$text = __( 'Moderate Question', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'photo':

				$text = __( 'Moderate Photo', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			case 'video':

				$text = __( 'Moderate Video', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;

			default:

				$text = sprintf( __( 'Moderate %s', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $this->get_title() );
			break;

		}

		/**
		 * Filter contribution type moderate text
		 *
		 * @since 1.0.0
		 * @param string $edit The moderate text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_moderate_text', $text, $this->type );
	}


	/**
	 * Returns form fields for the given contribution type
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_fields() {

		// Get default contribution fields
		$fields = $this->get_default_fields();

		// Add type-specific fields
		switch ( $this->type ) {

			case 'review' :

				if ( 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) {

					// Add rating field to beginning of fields
					$fields = array_merge( array(
						'rating' => array(
							'type'    => 'wc_product_reviews_pro_radio',
							'label'   => __( 'How would you rate this product?', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
							'class'   => array( 'star-rating-selector' ),
							'options' => array(
								'5' => __( 'Perfect', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
								'4' => __( 'Good', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
								'3' => __( 'Average', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
								'2' => __( 'Mediocre', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
								'1' => __( 'Poor', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
							),
							'required' => get_option( 'woocommerce_review_rating_required' ) === 'yes',
						)
					), $fields );
				}

				// Review title placeholder
				$fields['title']['placeholder'] = __( 'What is the title of your review?', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Review content label
				$fields['comment']['label'] = __( 'Review', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;


			case 'question' :

				// Remove title from question fields
				unset( $fields['title'] );

				// Question content label
				$fields['comment']['label'] = __( 'Question', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Question content placeholder
				$fields['comment']['placeholder'] = __( 'What is your question?', WC_Product_Reviews_Pro::TEXT_DOMAIN );
			break;


			case 'photo' :

				// Photo title placeholder
				$fields['title']['placeholder'] = __( 'What is the title of your photo?', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Photo content label
				$fields['comment']['label'] = __( 'Description', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Photo content placeholder
				$fields['comment']['placeholder'] = __( 'Your photo\'s description', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Set attachment type explicitly
				$fields['attachment_type'] = array(
					'type'    => 'wc_product_reviews_pro_hidden',
					'default' => 'photo',
					'class'   => array( 'attachment-type' ),
				);

				if ( isset( $fields['comment']['custom_attributes']['data-min-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-min-word-count'] );
				}

				if ( isset( $fields['comment']['custom_attributes']['data-max-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-max-word-count'] );
				}
			break;


			case 'video' :

				// Video title placeholder
				$fields['title']['placeholder'] = __( 'What is the title of your video?', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Video content label
				$fields['comment']['label'] = __( 'Description', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Video content placeholder
				$fields['comment']['placeholder'] = __( 'Your video\'s description', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Set attachment type explicitly
				$fields['attachment_type'] = array(
					'type'    => 'wc_product_reviews_pro_hidden',
					'default' => 'video',
					'class'   => array( 'attachment-type' ),
				);

				$fields['attachment_url']['required'] = true;

				if ( isset( $fields['comment']['custom_attributes']['data-min-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-min-word-count'] );
				}

				if ( isset( $fields['comment']['custom_attributes']['data-max-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-max-word-count'] );
				}

				unset( $fields['attachment_file'] );
			break;


			case 'contribution_comment' :

				// Comment content placeholder
				$fields['comment']['placeholder'] = __( 'What is your comment?', WC_Product_Reviews_Pro::TEXT_DOMAIN );

				// Unset unnecessary fields
				unset( $fields['title'] );
				unset( $fields['attachment_type'] );
				unset( $fields['attachment_file'] );
				unset( $fields['attachment_url'] );

				if ( isset( $fields['comment']['custom_attributes']['data-min-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-min-word-count'] );
				}

				if ( isset( $fields['comment']['custom_attributes']['data-max-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-max-word-count'] );
				}
			break;

		}

		/**
		 * Filter contribution form fields
		 *
		 * @since 1.0.0
		 * @param array $fields Associative array of contribution form fields
		 * @param string $type The contribution type
		 */
		$fields = apply_filters( 'wc_product_reviews_pro_contribution_type_fields', $fields, $this->type );

		$contribution_fields = array();

		// Prefix field keys with contribution type to avoid duplicate IDs
		// when using woocommerce_form_field
		$prefix = $this->type . '_';

		foreach ( $fields as $key => $value ) {

			$contribution_fields[ $prefix . $key ] = $value;
		}

		return $contribution_fields;
	}


	/**
	 * Returns the default contribution fields, can be filtered
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_default_fields() {

		$fields = array(
			'title' => array(
				'type'        => 'text',
				'label'       => __( 'Title', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
			),
			'comment' => array(
				'type'              => 'textarea',
				'label'             => __( 'Comment', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'placeholder'       => __( 'Tell us what you think of this product...', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'required'          => true,
				'custom_attributes' => array(
					'data-min-word-count' => get_option( 'wc_product_reviews_pro_min_word_count' ),
					'data-max-word-count' => get_option( 'wc_product_reviews_pro_max_word_count' ),
				),
			),
			'attachment_type' => array(
				'type'       => 'wc_product_reviews_pro_radio',
				'label'      => __( 'Attach a photo or video', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'class'      => array( 'attachment-type' ),
				'options'    => array(
					'photo' => __( 'Photo', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
					'video' => __( 'Video', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				),
			),
			'attachment_url' => array(
				'type'        => 'text',
				'label'       => __( 'Enter a URL', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'placeholder' => 'http://',
				'class'       => array( 'attachment-url', 'attachment-source' ),
			),
			'attachment_file' => array(
				'type'       => 'wc_product_reviews_pro_file',
				'label'      => __( 'Choose a file', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'class'      => array( 'attachment-file', 'attachment-source' ),
				'custom_attributes' => array(
					'accept' => 'image/*'
				),
			),
		);

		/**
		 * Filter the default contribution fields.
		 *
		 * @since 1.0.0
		 * @param array $fields The default contribution fields.
		 */
		return apply_filters( 'wc_product_reviews_pro_default_fields', $fields );
	}


}
