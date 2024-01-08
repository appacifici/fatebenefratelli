<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class handles our posts table shortcode.
 *
 * Example usage:
 *   [posts_table
 *       post_type="band"
 *       columns="title,content,tax:country,tax:genre,cf:_price,cf:stock"
 *       tag="cool",
 *       term="country:uk,artist:beatles"]
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Shortcode {

	const SHORTCODE = 'posts_table';

	public static function register_shortcode() {
		// Register posts table shortcode
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'do_shortcode' ) );

		// Back-compat with free version of plugin
		add_shortcode( 'posts_data_table', array( __CLASS__, 'do_shortcode' ) );
	}

	/**
	 * Handles our posts data table shortcode.
	 *
	 * @param array $atts The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {
		if ( ! self::can_do_shortocde() ) {
			return '';
		}

		// Fill-in missing attributes, and ensure back compat for old attribute names.
		$r = shortcode_atts( Posts_Table_Args::get_defaults(), ptp_back_compat_args( $atts ), self::SHORTCODE );

		// Return the table as HTML
		return apply_filters( 'posts_table_shortcode_output', ptp_get_posts_table( $r ) );
	}

	private static function can_do_shortocde() {
		// Don't run in the search results page.
		if ( is_search() && in_the_loop() && ! apply_filters( 'posts_table_run_in_search', false ) ) {
			return false;
		}

		return true;
	}

}
// class Posts_Data_Table_Shortcode
