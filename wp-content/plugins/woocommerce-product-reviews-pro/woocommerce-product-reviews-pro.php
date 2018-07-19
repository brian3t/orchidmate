<?php
/**
 * Plugin Name: WooCommerce Product Reviews Pro
 * Plugin URI: http://www.woothemes.com/products/woocommerce-product-reviews-pro/
 * Description: Extend WooCommerce product reviews to add video, photo, comment, and question contribution types, as well as review filtering, voting, and flagging.
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 1.0.6
 * Text Domain: woocommerce-product-reviews-pro
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2015 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Product-Reviews-Pro
 * @author    SkyVerge
 * @category  Reviews
 * @copyright Copyright (c) 2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '43662c2508f9242c6ba1da8c535510a0', '570800' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '3.1.0', __( 'WooCommerce Product Reviews Pro', 'woocommerce-product-reviews-pro' ), __FILE__, 'init_woocommerce_product_reviews_pro', array( 'minimum_wc_version' => '2.1', 'backwards_compatible' => '3.1.0' ) );

function init_woocommerce_product_reviews_pro() {


/**
 * # WooCommerce Product Reviews Pro Main Plugin Class
 *
 * ## Plugin Overview
 *
 * ## Features
 *
 * ## Frontend Considerations
 *
 * ## Admin Considerations
 *
 * ## Database
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro extends SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.0.6';

	/** @var WC_Product_Reviews_Pro single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'product_reviews_pro';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_product_reviews_pro_';

	/** plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-product-reviews-pro';

	/** @var \WC_Product_Reviews_Pro_Admin instance */
	public $admin;

	/** @var \WC_Product_Reviews_Pro_Frontend instance */
	public $frontend;

	/** @var \WC_Product_Reviews_Pro_AJAX instance */
	public $ajax;


	/**
	 * Initializes the plugin
	 *
	 * @since 1.0.0
	 * @return \WC_Product_Reviews_Pro
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			self::TEXT_DOMAIN
		);

		// Admin
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// delay standard install so we can use get_comments()
			remove_action( 'wp_loaded', array( $this, 'do_install' ) );
			add_action( 'admin_init', array( $this, 'do_install' ) );
		}

		// Include required files
		$this->includes();

		add_action( 'wp_update_comment_count', array( $this, 'clear_transients' ) );

		add_filter( 'pre_option_woocommerce_enable_review_rating', array( $this, 'filter_enable_review_rating' ) );

		// Points and Rewards compatability - ensure only allowed contibutions are counted towards earned review points
		add_filter( 'wc_points_rewards_review_post_comments_args',    array( $this, 'points_rewards_review_get_comments_args' ), 10, 2 );
		add_filter( 'wc_points_rewards_review_approve_comments_args', array( $this, 'points_rewards_review_get_comments_args' ), 10, 2 );

		// Points and Rewards compatability - ensure points are only added for allowed contribution types
		add_filter( 'wc_points_rewards_pro_post_add_product_review_points',    array( $this, 'points_rewards_review_add_product_review_points' ), 10, 2 );
		add_filter( 'wc_points_rewards_pro_approve_add_product_review_points', array( $this, 'points_rewards_review_add_product_review_points' ), 10, 2 );
	}


	/**
	 * Include required files
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		require_once( 'includes/class-wc-product-reviews-pro-review-qualifiers.php' );

		$this->review_qualifiers = new WC_Product_Reviews_Pro_Review_Qualifiers();

		require_once( 'includes/class-wc-product-reviews-pro-contribution-factory.php' );

		$this->contribution_factory = new WC_Product_Reviews_Pro_Contribution_Factory();

		require_once( 'includes/class-wc-product-reviews-pro-contribution-type.php' );

		require_once( 'includes/abstract-wc-contribution.php' );
		require_once( 'includes/class-wc-contribution-review.php' );
		require_once( 'includes/class-wc-contribution-question.php' );
		require_once( 'includes/class-wc-contribution-video.php' );
		require_once( 'includes/class-wc-contribution-photo.php' );
		require_once( 'includes/class-wc-contribution-comment.php' );

		require_once( 'includes/wc-product-reviews-pro-contribution-functions.php' );

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			$this->frontend_includes();
		}

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			$this->admin_includes();
		}

		require_once( 'includes/class-wc-product-reviews-pro-ajax.php' );
		$this->ajax = new WC_Product_Reviews_Pro_AJAX();
	}


	/**
	 * Include required frontend files
	 *
	 * @since 1.0.0
	 */
	private function frontend_includes() {

		require_once( 'includes/wc-product-reviews-pro-template-functions.php' );

		require_once( 'includes/frontend/class-wc-product-reviews-pro-frontend.php' );
		$this->frontend = new WC_Product_Reviews_Pro_Frontend();
	}


	/**
	 * Include required admin files
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		require_once( 'lib/class-wc-reviews.php' );

		require_once( 'includes/admin/class-wc-product-reviews-pro-admin.php' );
		$this->admin = new WC_Product_Reviews_Pro_Admin();
	}


	/**
	 * Load plugin text domain.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		load_plugin_textdomain( 'woocommerce-product-reviews-pro', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
	}


	/**
	 * Get contribution types
	 *
	 * @since 1.0.0
	 * @return array of contribution type names, ie ['review', 'question', 'video', 'photo', 'contribution_comment']
	 */
	public function get_contribution_types() {

		/**
		 * Filter the contribution types
		 *
		 * @since 1.0.0
		 * @param array $contribution_types The contribution types.
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_types', array(
			'review',
			'question',
			'video',
			'photo',
			'contribution_comment',
		) );
	}


	/**
	 * Get enabled contribution types
	 *
	 * @since 1.0.0
	 * @return array of contribution type names, ie ['review', 'question', 'video', 'photo', 'contribution_comment']
	 */
	public function get_enabled_contribution_types() {

		if ( 'all' == get_option( 'wc_product_reviews_pro_enabled_contribution_types' ) ) {
			return $this->get_contribution_types();
		} else {
			return (array) get_option( 'wc_product_reviews_pro_specific_enabled_contribution_types' );
		}

	}


	/**
	 * Filter the woocommerce_enable_review_rating option
	 *
	 * Checks if reviews have been enabled. If disabled, returns 'no'.
	 *
	 * @since 1.0.0
	 * @param  string $enabled
	 * @return string 'no' if reviews are disabled, pass-thru otherwise
	 */
	public function filter_enable_review_rating( $enabled ) {

		return ! in_array( 'review', $this->get_enabled_contribution_types() ) ? 'no' : $enabled;
	}


	/**
	 * Filter the get_comments arguments when a comment is posted or approved
	 *
	 * This ensures that only allowed contrubution types are counted towards
	 * previously awarded points
	 *
	 * @since 1.0.6
	 * @param array $args The get_comments array of arguments
	 * @return array
	 */
	public function points_rewards_review_get_comments_args( $args ) {

		/**
		 * Filter the array of contibution types which should award points
		 * Note: This filter should only be used if running WP 4.1+ only
		 * {@link https://core.trac.wordpress.org/ticket/21571}
		 *
		 * @since 1.0.6
		 * @param array $contribution_types The array of contibution types which
		 */
		$comment_types = apply_filters( 'wc_product_reviews_pro_review_points_contribution_types', array( 'review' ) );

		// backwards compatability with < WP 4.1 as the `type` argument must be a sting in older versions
		if ( count( $comment_types ) <= 1 ) {
			$comment_types = explode( '', $comment_types );
		}

		return array_merge( $args, array( 'type' => $comment_types ) );
	}


	/**
	 * Filter if points should be added for a particular comment id on posting
	 * or approving a review.
	 *
	 * This ensures that points are only rewarded for the review contibution type
	 * but allows users to filter the types if needed.
	 *
	 * @since 1.0.6
	 * @param bool $add_points True if points should be awarded for this contribution (default), false otherwise
	 * @param int $comment_id The comment ID
	 * @return bool True if points should be awarded for this contribution, false otherwise
	 */
	public function points_rewards_review_add_product_review_points( $add_points, $comment_id ) {

		$comment = get_comment( $comment_id );

		// bail if there is an issue with retreiving the comment object
		if ( ! $comment ) {
			return $add_points;
		}

		/**
		 * Filter the array of contibution types which should award points
		 * Note: This filter should only be used if running WP 4.1+ only
		 * {@link https://core.trac.wordpress.org/ticket/21571}
		 *
		 * @since 1.0.6
		 * @param array $contribution_types The array of contibution types which
		 */
		$comment_types = apply_filters( 'wc_product_reviews_pro_review_points_contribution_types', array( 'review' ) );

		return in_array( $comment->comment_type, $comment_types );
	}


	/** Admin methods ******************************************************/


	/**
	 * Render a notice for the user to read the docs before adding add-ons
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$this->get_admin_notice_handler()->add_admin_notice(
			sprintf( __( 'Thanks for installing Product Reviews Pro! Before getting started, please take a moment to %sread the documentation%s :) ', self::TEXT_DOMAIN ), '<a href="http://docs.woothemes.com/document/woocommerce-product-reviews-pro/" target="_blank">', '</a>' ),
			'read-the-docs-notice',
			array( 'always_show_on_settings' => false, 'notice_class' => 'updated' )
		);
	}


	/**
	 * Clear transients for a review.
	 *
	 * TODO: Once https://github.com/woothemes/woocommerce/pull/6284 makes it to the core, we can remove this
	 *
	 * @param mixed $post_id the post identifier
	 */
	public function clear_transients( $post_id ) {
		$post_id = absint( $post_id );
		delete_transient( 'wc_rating_count_' . $post_id . '_1' );
		delete_transient( 'wc_rating_count_' . $post_id . '_2' );
		delete_transient( 'wc_rating_count_' . $post_id . '_3' );
		delete_transient( 'wc_rating_count_' . $post_id . '_4' );
		delete_transient( 'wc_rating_count_' . $post_id . '_5' );
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Product Reviews Pro Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.0.0
	 * @see wc_product_reviews_pro()
	 * @return WC_Product_Reviews_Pro
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Product Reviews Pro', $this->text_domain );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'] ) && 'reviews' == $_GET['page'];
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Installation routine
	 *
	 * @since 1.0.0
	 * @see SV_WC_Plugin::install()
	 */
	protected function install() {

		global $wpdb;

		// Default settings
		update_option( 'wc_product_reviews_pro_enabled_contribution_types', 'all' );
		update_option( 'wc_product_reviews_pro_contributions_orderby',      'most_helpful' );
		update_option( 'wc_product_reviews_pro_contribution_moderation',    get_option( 'comment_moderation' ) ? 'yes' : 'no' );

		// Set comment_type to 'review' on all comments that have a product as
		// their parent and no type set.  Page through comments in blocks to
		// avoid out of memory errors
		$offset           = (int) get_option( 'wc_product_reviews_pro_install_offset', 0 );
		$records_per_page = 500;

		do {

			$record_ids = get_comments( array(
				'post_type' => 'product',
				'type'      => '',
				'fields'    => 'ids',
				'offset'    => $offset,
				'number'    => $records_per_page,
			) );

			// some sort of bad database error: deactivate the plugin and display an error
			if ( is_wp_error( $record_ids ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( 'woocommerce-product-reviews-pro/woocommerce-product-reviews-pro.php' );

				wp_die(
					sprintf(
						__( 'Error activating and installing %s: %s', self::TEXT_DOMAIN ),
						$this->get_plugin_name(),
						'<ul><li>' . implode( '</li><li>', $record_ids->get_error_messages() ) . '</li></ul>' ) .
					'<a href="' . admin_url( 'plugins.php' ) . '">' . __( '&laquo; Go Back', self::TEXT_DOMAIN ) . '</a>'
				);
			}

			if ( is_array( $record_ids ) ) {
				foreach ( $record_ids as $id ) {
					$wpdb->query( "UPDATE {$wpdb->comments} SET comment_type = 'review' WHERE comment_type = '' AND comment_ID = {$id}" );
				}
			}

			// increment offset
			$offset += $records_per_page;
			// and keep track of how far we made it in case we hit a script timeout
			update_option( 'wc_product_reviews_pro_install_offset', $offset );

		} while( count( $record_ids ) == $records_per_page );  // while full set of results returned  (meaning there may be more results still to retrieve)
	}


}

/**
 * Returns the One True Instance of Product Reviews Pro
 *
 * @since 1.0.0
 * @return WC_Product_Reviews_Pro
 */
function wc_product_reviews_pro() {
	return WC_Product_Reviews_Pro::instance();
}

// fire it up!
wc_product_reviews_pro();

} // init_woocommerce_product_reviews_pro()
