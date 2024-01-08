<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       marco-ilari
 * @since      1.0.0
 *
 * @package    Fbf_Attività_Ospedaliere
 * @subpackage Fbf_Attività_Ospedaliere/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fbf_Attività_Ospedaliere
 * @subpackage Fbf_Attività_Ospedaliere/includes
 * @author     marco ilari <ilari.marco@fbfgz.it>
 */
class Fbf_Attività_Ospedaliere_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fbf-attività-ospedaliere',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
