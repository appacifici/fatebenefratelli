<?php
/**
 * Backwards compatibility functions for Posts Table Pro.
 *
 * @package   Posts_Table_Pro\Compat
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ptp_back_compat_args' ) ) {

	/**
	 * Maintain support for old shortcode options.
	 *
	 * @param array $args The array of posts table attributes
	 * @return array The updated attributes with old ones replaced with their new equivalent
	 */
	function ptp_back_compat_args( $args ) {

		$compat = array(
			'ajax'					 => 'lazy_load',
			'posts_limit'			 => 'post_limit',
			'post_status'			 => 'status',
			'display_page_length'	 => 'page_length',
			'display_totals'		 => 'totals',
			'display_pagination'	 => 'pagination',
			'display_search_box'	 => 'search_box',
			'display_reset_button'	 => 'reset_button'
		);

		foreach ( $compat as $old => $new ) {
			if ( isset( $args[$old] ) ) {
				$args[$new] = $args[$old];
				unset( $args[$old] );
			}
		}

		return $args;
	}
}


if ( ! function_exists( 'wp_scripts' ) ) {

	// WP < 4.2
	function wp_scripts() {
		global $wp_scripts;
		if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
			$wp_scripts = new WP_Scripts();
		}
		return $wp_scripts;
	}
}