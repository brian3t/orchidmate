<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://superbappsolutions.com
 * @since      1.0.0
 *
 * @package    Superbappsolutions
 * @subpackage Superbappsolutions/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Superbappsolutions
 * @subpackage Superbappsolutions/includes
 * @author     Brian Nguyen <ceo@superbappsolutions.com>
 */
class Superbappsolutions_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'superbappsolutions',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
