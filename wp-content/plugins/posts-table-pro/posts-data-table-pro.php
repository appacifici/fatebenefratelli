<?php
/**
 * The main plugin file for Posts Table Pro.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @wordpress-plugin
 * Plugin Name:       Posts Table Pro
 * Plugin URI:		  https://barn2.co.uk/wordpress-plugins/posts-table-pro/
 * Description:       Display your site's posts, pages, and custom post types in a sortable, searchable and filterable data table.
 * Version:           2.1.1
 * Author:            Barn2 Media
 * Author URI:        https://barn2.co.uk
 * Text Domain:       posts-table-pro
 * Domain Path:       /languages
 *
 * Copyright:		  2016-2018 Barn2 Media Ltd
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main plugin class.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Posts_Table_Pro_Plugin {

	const NAME	 = 'Posts Table Pro';
	const VERSION	 = '2.1.1';
	const FILE	 = __FILE__;

	public $admin_settings;
	/**
	 * Our plugin license
	 */
	private $license;
	/**
	 * The singleton instance
	 */
	private static $_instance = null;

	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		// Instantiate plugin updater / license checker
		$this->license = new Barn2_Plugin_License( self::FILE, self::NAME, self::VERSION, 'ptp' );
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function define_constants() {
		if ( ! defined( 'PTP_INCLUDES_DIR' ) ) {
			define( 'PTP_INCLUDES_DIR', plugin_dir_path( self::FILE ) . 'includes/' );
		}
		if ( ! defined( 'PTP_PLUGIN_BASENAME' ) ) {
			define( 'PTP_PLUGIN_BASENAME', plugin_basename( self::FILE ) );
		}
	}

	private function includes() {
		// License
		require_once PTP_INCLUDES_DIR . 'license/class-b2-plugin-license.php';

		// Lib
		require_once PTP_INCLUDES_DIR . 'lib/class-html-data-table.php';
		require_once PTP_INCLUDES_DIR . 'lib/class-wp-scoped-hooks.php';
		require_once PTP_INCLUDES_DIR . 'lib/class-wp-settings-api-helper.php';

		// Util
		require_once PTP_INCLUDES_DIR . 'util/class-posts-table-util.php';
		require_once PTP_INCLUDES_DIR . 'util/class-posts-table-settings.php';

		// Front-end
		//@todo: Move to front-end only? Check with visual composer etc
		require_once PTP_INCLUDES_DIR . 'class-posts-data-table.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-args.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-config-builder.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-columns.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-query.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-hook-manager.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-ajax-handler.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-frontend-scripts.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-cache.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-factory.php';
		require_once PTP_INCLUDES_DIR . 'class-posts-table-shortcode.php';
		require_once PTP_INCLUDES_DIR . 'template-functions.php';

		// Post data
		require_once PTP_INCLUDES_DIR . 'data/interface-posts-table-data.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-factory.php';
		require_once PTP_INCLUDES_DIR . 'data/class-abstract-posts-table-data.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-author.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-categories.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-content.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-custom-field.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-custom-taxonomy.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-date.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-excerpt.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-hidden-filter.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-id.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-image.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-status.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-tags.php';
		require_once PTP_INCLUDES_DIR . 'data/class-posts-table-data-title.php';

		// Admin
		if ( is_admin() ) {
			require_once PTP_INCLUDES_DIR . 'admin/class-posts-table-admin-general.php';
			require_once PTP_INCLUDES_DIR . 'admin/class-posts-table-admin-settings-page.php';
			require_once PTP_INCLUDES_DIR . 'admin/class-posts-table-admin-tinymce.php';
		}

		// Back compat
		require_once PTP_INCLUDES_DIR . 'compat/back-compat.php';
	}

	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$this->load_textdomain();

		// Add our settings page
		if ( is_admin() ) {
			new Posts_Table_Admin_General();
			new Posts_Table_Admin_Settings_Page( $this->license );
		}

		// Initialize plugin if valid
		if ( $this->license->is_valid() ) {
			if ( is_admin() ) {
				Posts_Table_Admin_TinyMCE::setup();
			}

			if ( $this->is_front_end() ) {
				Posts_Table_Shortcode::register_shortcode();
				Posts_Table_Frontend_Scripts::load_scripts();
				Posts_Table_Ajax_Handler::register_ajax_events();
			}
		}
	}

	private function is_front_end() {
		return ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	private function load_textdomain() {
		load_plugin_textdomain( 'posts-table-pro', false, dirname( PTP_PLUGIN_BASENAME ) . '/languages' );
	}

}
// end class Posts_Table_Pro_Plugin

/**
 * Helper function to return the main plugin instance.
 *
 * @return Posts_Table_Pro_Plugin The main plugin instance
 */
function Posts_Table_Pro() {
	return Posts_Table_Pro_Plugin::instance();
}
// Load the plugin
Posts_Table_Pro_Plugin::instance();
