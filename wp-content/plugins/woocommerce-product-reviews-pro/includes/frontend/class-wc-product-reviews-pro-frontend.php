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
 * @category  Frontend
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Frontend class
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Frontend {


	/** @var bool indicator, if we are inserting a new contribution **/
	private $_inserting_contribution = false;


	/**
	 * Add hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load contributions comments template
		add_filter( 'comments_template', array( $this, 'comments_template_loader' ) );

		// Try to load WooCommerce templates from our plugin first
		add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 20, 3 );

		// Maybe force enable myaccount registration when rendering Ajax modal
		add_filter( 'pre_option_woocommerce_enable_myaccount_registration', array( $this, 'maybe_force_enable_myaccount_registration' ) );

		// Load frontend styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// Add file type support to woocommerce_form_field
		add_filter( 'woocommerce_form_field_wc_product_reviews_pro_file',   array( $this, 'form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_wc_product_reviews_pro_radio',  array( $this, 'form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_wc_product_reviews_pro_hidden', array( $this, 'form_field' ), 10, 4 );

		// Process posted comment data
		add_action( 'pre_comment_on_post', array( $this, 'process_posted_comment_data' ), 0 );

		// Set comment type based on contribution type
		add_filter( 'preprocess_comment', array( $this, 'preprocess_comment_data' ), -1 );

		// Save contribution data
		add_action( 'comment_post', array( $this, 'add_contribution_data' ), 1 );

		// Filter comment_moderation option for contributions
		add_filter( 'pre_option_comment_moderation', array( $this, 'contribution_moderation' ) );

		// Add contribution types as avatar comment types
		add_filter( 'get_avatar_comment_types', array( $this, 'add_contribution_avatar_types' ) );

		// Add employee badges if enabled
		add_filter( 'comment_author', array( $this, 'add_admin_badges' ), 10, 2 );

		// Support flagging without AJAX
		add_action( 'woocommerce_init', array( $this, 'flag_contribution' ) );

		// Support voting without AJAX
		add_action( 'woocommerce_init', array( $this, 'vote_for_contribution' ) );

		// Filter & order contributions on frontend
		add_filter( 'comments_array', array( $this, 'filter_comments' ), 10, 2 );
		add_filter( 'comments_array', array( $this, 'order_comments' ), 10, 2 );

		add_filter( 'login_message', array( $this, 'login_message' ) );


		add_filter( 'woocommerce_product_tabs', array( $this, 'customize_review_tab' ) );

		add_action( 'woocommerce_login_form', array( $this, 'add_redirect_to_field' ) );

		add_action( 'woocommerce_init', array( $this, 'handle_postdata_from_session' ) );
	}


	/**
	 * Loads the product contributions comments template
	 *
	 * @since 1.0.0
	 * @param mixed $template
	 * @return string
	 */
	public function comments_template_loader( $template ) {

		if ( get_post_type() !== 'product' ) {
			return $template;
		}

		$wc_template_path = SV_WC_Plugin_Compatibility::is_wc_version_gte_2_2() ? WC()->template_path() : WC_TEMPLATE_PATH;

		if ( file_exists( STYLESHEETPATH . '/' . $wc_template_path . 'single-product/contributions.php' ) ) {
			return STYLESHEETPATH . '/' . $wc_template_path . 'single-product/contributions.php';
		} elseif ( file_exists( TEMPLATEPATH . '/' . $wc_template_path . 'single-product/contributions.php' ) ) {
			return TEMPLATEPATH . '/' . $wc_template_path . 'single-product/contributions.php';
		} elseif ( file_exists( STYLESHEETPATH . '/' . 'single-product/contributions.php' ) ) {
			return STYLESHEETPATH . '/' . 'single-product/contributions.php';
		} elseif ( file_exists( TEMPLATEPATH . '/' . 'single-product/contributions.php' ) ) {
			return TEMPLATEPATH . '/' . 'single-product/contributions.php';
		} else {
			return wc_product_reviews_pro()->get_plugin_path() . '/templates/single-product/contributions.php';
		}
	}

	/**
	 * Locates the WooCommerce template files from our templates directory
	 *
	 * @since 1.0.0
	 * @param  string $template      Already found template
	 * @param  string $template_name Searchable template name
	 * @param  string $template_path Template path
	 * @return string                Search result for the template
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		// Tmp holder
		$_template = $template;

		if ( ! $template_path ) {
			$template_path = SV_WC_Plugin_Compatibility::is_wc_version_gte_2_2() ? WC()->template_path() : WC_TEMPLATE_PATH;
		}

		// Set our base path
		$plugin_path = wc_product_reviews_pro()->get_plugin_path() . '/templates/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		// Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		// Use default template
		if ( ! $template ) {
			$template = $_template;
		}

		// Return what we found
		return $template;
	}


	/**
	 * Maybe force enable My Account registration on the product page so
	 * the registration form is rendered properly in the Ajax modal window
	 *
	 * @since 1.0.0
	 * @param $enabled
	 * @return string
	 */
	public function maybe_force_enable_myaccount_registration( $enabled ) {

		if ( ! is_product() ) {
			return $enabled;
		}

		return 'yes';
	}


	/**
	 * Loads frontend styles and scripts on product page
	 *
	 * @since 1.0.0
	 */
	public function load_styles_scripts() {
		global $post;

		// Bail out if not on product page or if the post content doesn't include the [product_page] shortcode
		if ( ! ( is_product() || ( is_object( $post ) && isset( $post->post_content ) && has_shortcode( $post->post_content, 'product_page' ) ) ) ) {
			return;
		}

		// jQuery tipTip from WC
		if ( ! wp_script_is( 'jquery-tiptip', 'registered' ) ) {
			wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		}

		// frontend CSS
		wp_enqueue_style( 'wc-product-reviews-pro-frontend', wc_product_reviews_pro()->get_plugin_url() . '/assets/css/frontend/wc-product-reviews-pro-frontend.min.css', array( 'dashicons' ), WC_Product_Reviews_Pro::VERSION );

		// frontend scripts
		wp_enqueue_script( 'wc-product-reviews-pro-frontend', wc_product_reviews_pro()->get_plugin_url() . '/assets/js/frontend/wc-product-reviews-pro-frontend.min.js', array( 'jquery', 'jquery-tiptip' ), WC_Product_Reviews_Pro::VERSION );

		wp_localize_script( 'wc-product-reviews-pro-frontend', 'wc_product_reviews_pro', array(
			'is_user_logged_in'    => is_user_logged_in(),
			'comment_registration' => get_option('comment_registration'),
			'product_id'   => $post->ID,
			'ajax_url'     => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'wc-product-reviews-pro' ),
			'comment_type' => isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null,
			'i18n' => array(
				'loading'           => __( 'Loading...', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'attach_a_photo'    => __( 'Attach a photo', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'attach_a_video'    => __( 'Attach a video', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'attach_photo_url'  => __( 'Rather attach photo from another website?', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'attach_photo_file' => __( 'Rather attach photo from your computer?', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'attach_video_url'  => __( 'Rather attach video from another website?', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'attach_video_file' => __( 'Rather attach video from your computer?', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'flag_failed'       => __( 'Could not flag contribution. Please try again later.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'vote_failed'       => __( 'Could not cast your vote. Please try again later.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'comment_karma'     => __( '%1$d out of %2$d people found this helpful', WC_Product_Reviews_Pro::TEXT_DOMAIN ),

				'error_attach_file'       => __( 'Please attach a file.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'error_required'          => __( 'This is a required field.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'error_too_short'         => __( 'Please enter at least %d words.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'error_too_long'          => __( 'Please enter less than %d words.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'error_file_not_allowed'  => __( 'Only jpg, png, gif, bmp and tiff files, please', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
				'error_login_signup'      => __( 'An error occurred, please try again.', WC_Product_Reviews_Pro::TEXT_DOMAIN ),
			),
		) );
	}


	/**
	 * Add support for extra field types to woocommerce_form_field
	 *
	 * Adds support for radio, file
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @param array $args
	 * @param mixed $value
	 * @return string $field HTML
	 */
	public function form_field( $field, $key, $args, $value ) {

		if ( ( ! empty( $args['clear'] ) ) ) {
			$after = '<div class="clear"></div>';
		} else {
			$after = '';
		}

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', WC_Product_Reviews_Pro::TEXT_DOMAIN  ) . '">*</abbr>';
		} else {
			$required = '';
		}

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		switch ( $args['type'] ) {

			case "wc_product_reviews_pro_radio" :

				if ( ! empty( $args['options'] ) ) {
					$field .= '<div class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

					if ( $args['label'] ) {
						$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required  . '</label>';
					}

					$field .= '<fieldset>';

					foreach ( $args['options'] as $option_key => $option_text ) {

						$field .= '<input type="radio" class="input-checkbox" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';

						$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label> ';

					}

					$field .= '</fieldset>';
					$field .= '</div>' . $after;
				}

			break;

			case "wc_product_reviews_pro_hidden" :

				$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" style="display:none;">';

					$field .= '<input type="hidden" class="input-hidden ' . implode( ' ', $args['input_class'] ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				$field .= '</p>' . $after;

			break;

			case "wc_product_reviews_pro_file" :

				$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

				if ( $args['label'] ) {
					$field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required  . '</label>';
				}

				$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

				if ( $args['label'] )
					$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

				$field .= '<input type="file" class="input-file ' . implode( ' ', $args['input_class'] ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' />
					</p>' . $after;

				break;

			break;
		}

		return $field;
	}


	/**
	 * Remove contribution type prefix from posted keys
	 *
	 * @since 1.0.0
	 */
	public function process_posted_comment_data() {

		$type = isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null;

		// Bail out if not contribution type is set. This probably means
		// that this wasn't a contribution form anyway.
		if ( ! $type ) {
			return;
		}

		// Loop over POST data and remove type prefix
		foreach ( $_POST as $key => $value ) {

			// Check if the key is prefixed with type
			if ( strpos( $key, $type . '_' ) === 0 ) {

				// Add posted value under cleaned (unprefixed) key
				$clean_key = substr( $key, strlen( $type ) + 1 );
				$_POST[ $clean_key ] = $value;

			}
		}

		// Process fields
		$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
		foreach ( $contribution_type->get_fields() as $key => $field ) {

			// Get Value
			switch ( $field['type'] ) {
				case "checkbox" :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;
				break;
				default :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
				break;
			}

			/**
			 * Filter the POST value for $key.
			 *
			 * @since 1.0.0
			 * @param mixed $value The POST value for $key.
			 */
			$_POST[ $key ] = apply_filters( 'wc_product_reviews_pro_process_contribution_form_field_' . $key, $_POST[ $key ] );

			// Validation: Required fields
			if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				wc_add_notice( sprintf( __( '%s is a required field.', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $field['label'] ), 'error' );
			}

			// Validation rules
			if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
				foreach ( $field['validate'] as $rule ) {
					switch ( $rule ) {
						case 'email' :
							$_POST[ $key ] = strtolower( $_POST[ $key ] );

							if ( ! is_email( $_POST[ $key ] ) ) {
								wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', WC_Product_Reviews_Pro::TEXT_DOMAIN ), 'error' );
							}
						break;
					}
				}
			}
		}

		// Check if rating is required
		if ( 'review' == $type && get_option( 'woocommerce_review_rating_required' ) === 'yes' && isset( $_POST[ $type . '_rating'] ) && empty( $_POST[ $type . '_rating'] ) ) {
			wc_add_notice( __( 'Please rate the product.', WC_Product_Reviews_Pro::TEXT_DOMAIN ), 'error' );
		}

		// Save/handle attachments (photos, videos)
		$attachment_type = isset( $_POST[ 'attachment_type' ] ) ? $_POST[ 'attachment_type' ] : null;

		if ( $attachment_type ) {

			$key = $type . '_attachment_file';

			if ( isset( $_FILES[ $key ] ) && $_FILES[ $key ][ 'size' ] > 0 ) {

				// Only photo uploads are supported at the moment
				if ( 'photo' == $attachment_type ) {

					// These files need to be included as dependencies when on the front end.
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );

					$attachment_id = media_handle_upload( $key, 0, array(), array(
						'test_form' => false,
						'mimes' => array(
							'jpg|jpeg|jpe' => 'image/jpeg',
							'gif'          => 'image/gif',
							'png'          => 'image/png',
							'bmp'          => 'image/bmp',
							'tif|tiff'     => 'image/tiff',
						),
					) );

					// Bail out if file upload did not succeed
					if ( is_wp_error( $attachment_id ) ) {

						wc_add_notice( sprintf( __( 'Unable to upload file: %s', WC_Product_Reviews_Pro::TEXT_DOMAIN ), $attachment_id->get_error_message() ), 'error' );

					} else {

						// Keep a reference to attachment_id and type
						$this->_uploaded_attachment_id   = $attachment_id;
						$this->_uploaded_attachment_type = $attachment_type;
					}
				} else {
					wc_add_notice( __( 'Only photo uploads are supprted at the moment', WC_Product_Reviews_Pro::TEXT_DOMAIN ), 'error' );
				}

			}

			// Make sure that at least one of file or url is submitted
			if ( 'photo' == $type &&
					! ( isset( $_FILES[ $key ] ) && $_FILES[ $key ][ 'size' ] > 0 ) &&
					! ( isset( $_POST[ $type . '_attachment_url' ] ) && $_POST[ $type . '_attachment_url'] )
				) {
				wc_add_notice( __( 'Please attach a photo.', WC_Product_Reviews_Pro::TEXT_DOMAIN ), 'error' );
			}
		}


		// Redirect back to product page if there are errors
		if ( wc_notice_count( 'error' ) > 0 ) {

			WC()->session->wc_product_reviews_pro_posted_data = $_POST;

			// Provide a hash so that page scrolls to form on load
			$hash = 'contribution_comment' == $type ? '#comment-' . $_POST['comment_parent'] : '#reviews';

			wp_safe_redirect( wp_get_referer() . $hash );
			exit;
		}

	}


	/**
	 * Preprocess comment data
	 *
	 * @since 1.0.0
	 * @param array $commentdata
	 * @return array $commentdata
	 */
	public function preprocess_comment_data( $commentdata ) {

		// Set comment_type in commentdata so that the comment is saved with
		// the correct comment type. WP itself does not read it from $_POST,
		// so we need to set it manually.
		$commentdata['comment_type'] = isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null;

		// Indicate that we are in the process of inserting a new contribution.
		// This flag will be used by the pre_option_comment_moderation filter later
		if ( $commentdata['comment_type'] ) {
			$this->_inserting_contribution = true;
		}

		return $commentdata;
	}


	/**
	 * Save contribution data
	 *
	 * @since 1.0.0
	 * @param mixed $comment_id
	 */
	public function add_contribution_data( $comment_id ) {

		// Save title
		if ( isset( $_POST['title'] ) && $_POST['title'] ) {

			add_comment_meta( $comment_id, 'title', $_POST['title'], true );
		}

		// Save/handle attachments (photos, videos)
		$attachment_type = isset( $_POST['attachment_type'] ) ? $_POST['attachment_type'] : null;

		if ( $attachment_type ) {

			if ( isset ( $_POST[ 'attachment_url' ] ) && $_POST[ 'attachment_url' ] ) {

				add_comment_meta( $comment_id, 'attachment_type', $attachment_type );
				add_comment_meta( $comment_id, 'attachment_url', $_POST[ 'attachment_url' ] );
			}

			elseif ( isset ( $this->_uploaded_attachment_type ) && $attachment_type == $this->_uploaded_attachment_type ) {

				add_comment_meta( $comment_id, 'attachment_type', $this->_uploaded_attachment_type );
				add_comment_meta( $comment_id, 'attachment_id',   $this->_uploaded_attachment_id );
			}

		}

		if ( isset( $_POST['comment_type'] ) && 'review' == $_POST['comment_type'] ) {
			$this->clear_transients( $comment_id );
		}

	}


	/**
	 * Filter comment_moderation option for contributions
	 *
	 * @since 1.0.0
	 * @return bool True, if manual contribution moderation is on, false otherwise
	 */
	public function contribution_moderation( $moderation ) {

		if ( $this->_inserting_contribution ) {
			$moderation = ( 'yes' == get_option('wc_product_reviews_pro_contribution_moderation') ) ? 1 : '';
		}

		return $moderation;
	}


	/**
	 * Clear transients for a contribution.
	 *
	 * @since 1.0.0
	 * @param mixed $comment_id
	 */
	public function clear_transients( $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( ! empty( $comment->comment_post_ID ) ) {
			delete_transient( 'wc_product_reviews_pro_review_count_'   . absint( $comment->comment_post_ID ) );
			delete_transient( 'wc_product_reviews_pro_highest_rating_' . absint( $comment->comment_post_ID ) );
			delete_transient( 'wc_product_reviews_pro_lowest_rating_'  . absint( $comment->comment_post_ID ) );
		}
	}


	/**
	 * Add contribution types as allowed comment types for avatars
	 *
	 * @since 1.0.0
	 * @param array $allowed_types
	 * @return array
	 */
	public function add_contribution_avatar_types( $allowed_types ) {

		$contribution_types = array_keys( wc_product_reviews_pro()->get_contribution_types() );

		return array_unique( array_merge( $allowed_types, $contribution_types ) );
	}


	/**
	 * Flag a contribution (non-AJAX)
	 *
	 * @since 1.0.0
	 */
	public function flag_contribution() {

		// Ensure we are actually flagging a contribution
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['action'] ) || 'flag_contribution' != $_POST['action'] ) {
			return;
		}

		// Bail out if no comment ID was provided
		if ( ! isset( $_POST['comment_id'] ) || ! $_POST['comment_id'] ) {
			return;
		}

		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );
		$reason = isset( $_POST['flag_reason'] ) ? $_POST['flag_reason'] : null;

		if ( ! $contribution ) {
			return;
		}

		// Flag contribution
		if ( $contribution->flag( $reason ) ) {

			wc_add_notice( __( 'Contribution was flagged. Thanks!', WC_Product_Reviews_Pro::TEXT_DOMAIN ) );

		} else {

			$message = $contribution->get_failure_message();
			wc_add_notice( $message ? $message : __( 'Could not flag contribution. Please try again later.', WC_Product_Reviews_Pro::TEXT_DOMAIN ), 'error' );
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}


	/**
	 * Vote for a contribution (non-AJAX)
	 *
	 * @since 1.0.0
	 */
	public function vote_for_contribution() {

		// Ensure we are actually voting for a contribution
		if ( 'GET' != $_SERVER['REQUEST_METHOD'] || ! isset( $_GET['action'] ) || 'vote_for_contribution' != $_GET['action'] ) {
			return;
		}

		// Bail out if no comment ID was provided
		if ( ! isset( $_GET['comment_id'] ) || ! $_GET['comment_id'] ) {
			return;
		}

		$contribution = wc_product_reviews_pro_get_contribution( $_GET['comment_id'] );

		if ( ! $contribution ) {
			return;
		}

		$type = isset( $_GET['type'] ) ? $_GET['type'] : null;

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			$redirect_to = add_query_arg( 'redirect_to', urlencode( $_SERVER['REQUEST_URI'] ), get_permalink( wc_get_page_id( 'myaccount' ) ) );
			wp_redirect( $redirect_to );
			exit;
		}

		// Cas the vote for contribution
		if ( $contribution->cast_vote( $type ) ) {

			wc_add_notice( __( 'Vote has been cast. Thanks!', WC_Product_Reviews_Pro::TEXT_DOMAIN ) );

		} else {

			$message = $contribution->get_failure_message();
			wc_add_notice( $message ? $message : __( 'Could not cast your vote. Please try again later.', WC_Product_Reviews_Pro::TEXT_DOMAIN ), 'error' );
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}


	/**
	 * Filter comments (contributions) on frontend
	 *
	 * Filters contributions by specified type and/or rating in query args
	 *
	 * @since 1.0.0
	 * @param  array $comments
	 * @return array
	 */
	public function filter_comments( $comments ) {
		global $post;

		if ( 'product' == $post->post_type ) {

			$filters = wc_product_reviews_pro_get_current_comment_filters();

			if ( $filters && ! empty( $filters ) ) {
				foreach ( $filters as $filter => $value ) {

					switch ( $filter ) {


						# Filter by comment type
						case 'comment_type':
							$_comments = array();

							foreach ( $comments as $comment ) {

								switch ( $comment->comment_type ) {

									case $value:
										$_comments[] = $comment;
									break;

									case 'contribution_comment':
										foreach ( $comments as $parent ) {
											if ( $parent->comment_ID == $comment->comment_parent && $value == $parent->comment_type ) {
												$_comments[] = $comment;
											}
										}
									break;
								}

							}

							$comments = $_comments;
							break;


						# Filter by review rating
						case 'rating':
							$_comments = array();

							foreach ( $comments as $comment ) {

								switch ( $comment->comment_type ) {

									// Include reviews with matching rating
									case 'review':
										$rating = get_comment_meta( $comment->comment_ID, 'rating', true );

										if ( $rating == $value ) {
											$_comments[] = $comment;
										}

									break;

									// Include comments that have a parent with matching rating
									case 'contribution_comment':

										foreach ( $_comments as $parent ) {
											if ( $parent->comment_ID == $comment->comment_parent ) {
												$_comments[] = $comment;
											}
										}

									break;
								}
							}

							$comments = $_comments;
							break;


						# Filter by review qualifier
						case 'review_qualifier':
							$_comments = array();

							$parts = explode( ':', $value );

							// Make sure we actually have a qualifier value
							if ( ! isset( $parts[1] ) ) {
								break;
							}

							$filter_qualifier_value  = $parts[1];

							foreach ( $comments as $comment ) {

								$qualifier_value = get_comment_meta( $comment->comment_ID, 'wc_product_reviews_pro_review_qualifier_' . $parts[0], true );

								if ( $qualifier_value == $filter_qualifier_value ) {
									$_comments[] = $comment;
								}
							}

							$comments = $_comments;
						break;


						# Filter by unanswered
						case 'unanswered':
							$_comments = array();
							global $wpdb;

							foreach ( $comments as $comment ) {

								if ( ! $comment->comment_parent ) {

									$answers_count = $wpdb->get_var( $wpdb->prepare( "
										SELECT COUNT(comment_ID) FROM $wpdb->comments
										WHERE comment_parent = %d
									", $comment->comment_ID ) );

									if ( ! $answers_count ) {
										$_comments[] = $comment;
									}
								}
							}

							$comments = $_comments;
						break;


						# Filter by classification (positive/negative)
						case 'classification':
							$_comments = array();
							global $wpdb;

							foreach ( $comments as $comment ) {

								$rating = get_comment_meta( $comment->comment_ID, 'rating', true );

								if ( $value == 'positive' && $rating >= 3 ) {
									$_comments[] = $comment;
								}

								if ( $value == 'negative' && $rating < 3 ) {
									$_comments[] = $comment;
								}
							}

							$comments = $_comments;
						break;


						# Filter by helpfulness
						case 'helpful':
							$_comments = array();

							foreach ( $comments as $comment ) {

								$contribution = wc_product_reviews_pro_get_contribution( $comment );
								$ratio = $contribution->get_helpfulness_ratio();

								if ( $ratio >= 0.66 ) {
									$_comments[] = $comment;
								}
							}

							$comments = $_comments;
						break;


						# Apply filters if this is an unknown filter
						default:

							/**
							 * Allow plugins to filter comments using a custom filter
							 *
							 * @since 1.0.0
							 * @param array $comments The comments array.
							 * @param array $args Associative array of arguments including the filter and value.
							 */
							$comments = apply_filters( 'wc_product_reviews_pro_filter_comments', $comments, array( 'filter' => $filter, 'value' => $value ) );
						break;
					}

				}
			}

		}

		return $comments;
	}


	/**
	 * Order comments (contributions) on frontend
	 *
	 * @since 1.0.0
	 * @param  array $comments
	 * @return array $comments
	 */
	public function order_comments( $comments ) {
		global $post;

		if ( 'product' == $post->post_type ) {

			$orderby = get_option( 'wc_product_reviews_pro_contributions_orderby' );

			switch ( $orderby ) {

				// Order contributions by most helpful ratio
				// TODO: implement a better algorithm for determining usefulness
				case 'most_helpful':

					foreach ( $comments as $key => $comment ) {

						$contribution = wc_product_reviews_pro_get_contribution( $comment );
						$comment->helpfulness_ratio = $contribution->get_helpfulness_ratio();

						$comments[$key] = $comment;
					}

					usort( $comments, array( $this, 'compare_helpfulness_ratio' ) );
				break;

				// The comments template defaults to oldest first
				case 'newest':
					$comments = array_reverse( $comments );
				break;
			}
		}

		return $comments;
	}


	/**
	 * Add a badge for admin / shop manager comments if set.
	 *
	 * @since 1.0.6
	 *
	 * @param string $author	The comment author's username
	 * @param int $comment_id	The comment ID.
	 * @return string $author	The updated author username preceded by badge.
	 */
	public function add_admin_badges( $author, $comment_id ) {

		if ( ! is_product() ) {
			return $author;
		}

		$badge_text = get_option( 'wc_product_reviews_pro_contribution_badge' );

		$user_id = get_comment( $comment_id )->user_id;

		if ( $user_id && ! empty( $badge_text ) ) {

			$roles = get_userdata( $user_id )->roles;

			if ( in_array( 'administrator', $roles ) || in_array( 'shop_manager', $roles ) ) {

				$author =  '<span class="contribution-admin-badge">' . esc_html( $badge_text ) . '</span>' . $author;
			}

		}

		return $author;
	}


	/**
	 * Compare contributions based on helpfulness ratios
	 *
	 * @since 1.0.0
	 * @param  object $a
	 * @param  object $b
	 * @return bool
	 */
	private function compare_helpfulness_ratio( $a, $b ) {

		return strcmp( $b->helpfulness_ratio, $a->helpfulness_ratio );
	}


	/**
	 * Adjust the login message
	 *
	 * @param  string $message
	 * @return string
	 */
	public function login_message( $message ) {

		$redirect_to = isset( $_GET['redirect_to'] ) ? urldecode( $_GET['redirect_to'] ) : '';

		// Display a message when trying to vote for a contribution
		if ( $redirect_to ) {

			$params = array();
			parse_str( parse_url( $redirect_to, PHP_URL_QUERY ), $params );

			if ( isset( $params['action'] ) && 'vote_for_contribution' == $params['action'] ) {
				$message = '<p class="message">' . __( 'You must be logged in to vote' ) . '</p>';
			}

		}

		return $message;
	}


	/**
	 * Customize the review product tab
	 *
	 * Will replace the review tab title with a more generic
	 * one if multiple contribution types are enabled, or
	 * with a specific title, if only one type is enabled.
	 *
	 * @since 1.0.0
	 * @param array $tabs
	 * @return array
	 */
	public function customize_review_tab( $tabs ) {
		global $post;

		if ( isset( $tabs['reviews'] ) ) {

			$enabled_contribution_types = wc_product_reviews_pro()->get_enabled_contribution_types();

			// Do not take contribution_comments into account
			if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types ) ) !== false ) {
				unset( $enabled_contribution_types[$key] );
			}

			// Hide reviews tab if none of the types are enabled
			if ( empty( $enabled_contribution_types ) ) {
				unset( $tabs['reviews'] );
			}

			// For single types, get their type-specific tab title
			elseif ( count( $enabled_contribution_types ) == 1 ) {

				$type = $enabled_contribution_types[0];
				$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
				$count = wc_product_reviews_pro_get_comments_number( $post->ID, $type );
				$tabs['reviews']['title'] = $contribution_type->get_tab_title( $count );

			}

			// Otherwise, display the Discussions title and correct number of contributions
			else {

				$count = wc_product_reviews_pro_get_comments_number( $post->ID, $enabled_contribution_types );
				$contribution_type = wc_product_reviews_pro_get_contribution_type( null );
				$tabs['reviews']['title'] = $contribution_type->get_tab_title( $count );
			}
		}

		return $tabs;
	}


	/**
	 * Add redirect field to my-account/form-login.php
	 *
	 * Allows specifying the page to redirect to afetr logging in
	 *
	 * @since 1.0.0
	 */
	public function add_redirect_to_field() {
		if ( is_account_page() && isset( $_REQUEST['redirect_to'] ) ) {
			?>
				<input type="hidden" name="redirect" value="<?php echo esc_attr( $_REQUEST['redirect_to'] ); ?>" />
			<?php
		}
	}


	/**
	 * Handle posted comment/contribution data from session
	 *
	 * @since 1.0.0
	 */
	public function handle_postdata_from_session() {

		if ( empty( $_POST ) && isset( WC()->session->wc_product_reviews_pro_posted_data ) ) {

			// Mimick $_POST data by getting the post data from WC session
			$_POST = WC()->session->wc_product_reviews_pro_posted_data;

			// Unset data from session, because we only need it once
			WC()->session->wc_product_reviews_pro_posted_data = null;

			// Handle displaying errors
			$type = isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null;

			if ( $type ) {

				// Unhook wc_print_notices from product page top
				remove_action( 'woocommerce_before_single_product', 'wc_print_notices' );

				// Print notices just before the contributions form
				if ( 'contribution_comment' == $type ) {

					add_action( 'wc_product_reviews_pro_before_' . $type .'_' . $_POST['comment_parent'] . '_form', 'wc_print_notices', 10 );
				} else {

					add_action( 'wc_product_reviews_pro_before_' . $type .'_form', 'wc_print_notices', 10 );
				}
			}
		}
	}


}
