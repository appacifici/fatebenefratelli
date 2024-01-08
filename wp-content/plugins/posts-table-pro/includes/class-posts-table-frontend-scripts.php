<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for registering the front-end styles and scripts in Posts Table Pro.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Frontend_Scripts {

	const SCRIPT_HANDLE	 = 'posts-table-pro';
	const SCRIPT_VERSION	 = Posts_Table_Pro_Plugin::VERSION;

	public static function load_scripts() {
		// Register front-end styles and scripts
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
	}

	public static function register_styles() {
		if ( ! apply_filters( 'posts_table_load_frontend_scripts', true ) ) {
			return;
		}

		$suffix = Posts_Table_Util::get_script_suffix();

		//@todo Combine with main stylesheet
		wp_register_style( 'jquery-data-tables', Posts_Table_Util::get_asset_url( "css/datatables/datatables{$suffix}.css" ), array(), '1.10.16' );
		wp_register_style( self::SCRIPT_HANDLE, Posts_Table_Util::get_asset_url( "css/posts-table-pro{$suffix}.css" ), array( 'jquery-data-tables' ), self::SCRIPT_VERSION );

		//@todo Combine with main stylesheet
		wp_register_style( 'photoswipe', Posts_Table_Util::get_asset_url( "css/photoswipe/photoswipe{$suffix}.css" ), array(), '4.1.1' );
		wp_register_style( 'photoswipe-default-skin', Posts_Table_Util::get_asset_url( "css/photoswipe/default-skin/default-skin{$suffix}.css" ), array( 'photoswipe' ), '4.1.1' );

		wp_enqueue_style( self::SCRIPT_HANDLE );
	}

	public static function register_scripts() {
		if ( ! apply_filters( 'posts_table_load_frontend_scripts', true ) ) {
			return;
		}

		$suffix = Posts_Table_Util::get_script_suffix();

		if ( apply_filters( 'posts_table_use_fitvids', true ) ) {
			wp_register_script( 'fitvids', Posts_Table_Util::get_asset_url( 'js/jquery-fitvids/jquery.fitvids.min.js' ), array( 'jquery' ), '1.1', true );
		}

		wp_register_script( 'jquery-data-tables', Posts_Table_Util::get_asset_url( "js/datatables/datatables{$suffix}.js" ), array( 'jquery' ), '1.10.16', true );
		wp_register_script( 'jquery-blockui', Posts_Table_Util::get_asset_url( "js/jquery-blockui/jquery.blockUI{$suffix}.js" ), array( 'jquery' ), '2.70.0', true );
		wp_register_script( self::SCRIPT_HANDLE, Posts_Table_Util::get_asset_url( "js/posts-table-pro{$suffix}.js" ), array( 'jquery', 'jquery-data-tables', 'jquery-blockui' ), self::SCRIPT_VERSION, true );

		if ( ! wp_script_is( 'photoswipe', 'registered' ) ) {
			wp_register_script( 'photoswipe', Posts_Table_Util::get_asset_url( "js/photoswipe/photoswipe{$suffix}.js" ), array(), '4.1.1', true );
		}
		if ( ! wp_script_is( 'photoswipe-ui-default', 'registered' ) ) {
			wp_register_script( 'photoswipe-ui-default', Posts_Table_Util::get_asset_url( "js/photoswipe/photoswipe-ui-default{$suffix}.js" ), array( 'photoswipe' ), '4.1.1', true );
		}

		wp_enqueue_script( self::SCRIPT_HANDLE );

		$script_obj = array(
			'ajax_url'		 => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'	 => wp_create_nonce( self::SCRIPT_HANDLE ),
			'wrapper_class'	 => esc_attr( Posts_Table_Util::get_wrapper_class() ),
			'language'		 => apply_filters( 'posts_table_language_defaults', array(
				'info'			 => __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'posts-table-pro' ),
				'infoEmpty'		 => __( 'Showing 0 entries', 'posts-table-pro' ),
				'infoFiltered'	 => __( '(_MAX_ entries in total)', 'posts-table-pro' ),
				'lengthMenu'	 => __( 'Show _MENU_ entries', 'posts-table-pro' ),
				'emptyTable'	 => __( 'No data available in table.', 'posts-table-pro' ),
				'zeroRecords'	 => __( 'No matching records found.', 'posts-table-pro' ),
				'search'		 => apply_filters( 'posts_table_search_label', __( 'Search:', 'posts-table-pro' ) ),
				'paginate'		 => array(
					'first'		 => __( 'First', 'posts-table-pro' ),
					'last'		 => __( 'Last', 'posts-table-pro' ),
					'next'		 => __( 'Next', 'posts-table-pro' ),
					'previous'	 => __( 'Previous', 'posts-table-pro' ),
				),
				'thousands'		 => _x( ',', 'thousands separator', 'posts-table-pro' ),
				'decimal'		 => _x( '.', 'decimal mark', 'posts-table-pro' ),
				'aria'			 => array(
					/* translators: ARIA text for sorting column in ascending order */
					'sortAscending'	 => __( ': activate to sort column ascending', 'posts-table-pro' ),
					/* translators: ARIA text for sorting column in descending order */
					'sortDescending' => __( ': activate to sort column descending', 'posts-table-pro' ),
				),
				'filterBy'		 => apply_filters( 'posts_table_filter_label', __( 'Filter:', 'posts-table-pro' ) ),
				'resetButton'	 => apply_filters( 'posts_table_reset_button', __( 'Reset', 'posts-table-pro' ) )
			) ),
		);

		$locale				 = get_locale();
		$supported_locales	 = self::get_supported_locales();

		// Back compat: Add language file to script if locale is supported.
		if ( array_key_exists( $locale, $supported_locales ) ) {
			$script_obj['lang_url'] = $supported_locales[$locale];
		}

		wp_localize_script( self::SCRIPT_HANDLE, 'posts_table_params', $script_obj );
	}

	public static function register_table_scripts( Posts_Table_Args $args ) {
		if ( ! apply_filters( 'posts_table_load_frontend_scripts', true ) ) {
			return;
		}

		if ( $args->shortcodes ) {
			// Add fitVids.js for responsive video if we're displaying shortcodes.
			if ( apply_filters( 'posts_table_use_fitvids', true ) ) {
				wp_enqueue_script( 'fitvids' );
			}

			// Queue media element and playlist scripts/styles.
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-playlist' );

			add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
		}

		// Enqueue Photoswipe for image lightbox.
		if ( $args->lightbox ) {
			wp_enqueue_style( 'photoswipe-default-skin' );
			wp_enqueue_script( 'photoswipe-ui-default' );

			add_action( 'wp_footer', array( __CLASS__, 'load_photoswipe_template' ) );
		}
	}

	public static function load_photoswipe_template() {
		Posts_Table_Util::include_template( 'photoswipe.php' );
	}

	/**
	 * Returns an array of locales supported by the plugin.
	 * The array returned uses the locale as the array key mapped to the URL of the corresponding translation file.
	 *
	 * @deprecated 2.0 - All translation now handled by gettext functions: __(), _e(), etc.
	 * @return array The supported locales
	 */
	private static function get_supported_locales() {
		$lang_file_base_url = plugins_url( 'languages/data-tables/', Posts_Table_Pro_Plugin::FILE );

		/**
		 * @deprecated 2.0 - All translation now handled by gettext.
		 */
		return apply_filters( 'posts_table_supported_languages', array() );
	}

}