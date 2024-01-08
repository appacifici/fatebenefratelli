<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility functions for the posts table settings.
 *
 * @package   Posts_Table_Pro\Util
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Posts_Table_Settings {

	const TABLE_STYLING_OPTION		 = 'ptp_table_styling';
	const SHORTCODE_DEFAULTS_OPTION	 = 'ptp_shortcode_defaults';
	const MISC_SETTINGS_OPTION		 = 'ptp_misc_settings';

	public static function get_setting_table_styling() {
		return self::get_setting( self::TABLE_STYLING_OPTION, array( 'use_theme' => 'theme' ) );
	}

	public static function get_setting_shortcode_defaults() {
		return self::settings_to_shortcode_args( self::get_setting( self::SHORTCODE_DEFAULTS_OPTION, array() ) );
	}

	public static function get_setting_misc() {
		$default = array(
			'cache_expiry' => 6
		);
		return self::get_setting( self::MISC_SETTINGS_OPTION, $default );
	}

	public static function settings_to_shortcode_args( $settings ) {
		if ( empty( $settings ) ) {
			return $settings;
		}

		// Check free text settings are not empty
		foreach ( array( 'columns', 'image_size', 'links' ) as $arg ) {
			if ( empty( $settings[$arg] ) && isset( Posts_Table_Args::$default_args[$arg] ) ) {
				$settings[$arg] = Posts_Table_Args::$default_args[$arg];
			}
		}

		// Custom filter option
		if ( isset( $settings['filters'] ) && 'custom' === $settings['filters'] ) {
			if ( empty( $settings['filters_custom'] ) ) {
				$settings['filters'] = Posts_Table_Args::$default_args['filters'];
			} else {
				$settings['filters'] = $settings['filters_custom'];
			}
		}

		// Custom sort by option
		if ( isset( $settings['sort_by'] ) && 'custom' === $settings['sort_by'] ) {
			if ( empty( $settings['sort_by_custom'] ) ) {
				$settings['sort_by'] = Posts_Table_Args::$default_args['sort_by'];
			} else {
				$settings['sort_by'] = $settings['sort_by_custom'];
			}
		}

		unset( $settings['filters_custom'] );
		unset( $settings['sort_by_custom'] );

		// Convert 'true' or 'false' options to booleans
		$settings = array_map( 'Posts_Table_Util::maybe_parse_bool', $settings );

		return $settings;
	}

	public static function sanitize_shortcode_settings( $args ) {
		// Check for empties
		foreach ( array( 'image_size', 'links' ) as $arg ) {
			if ( empty( $args[$arg] ) ) {
				$args[$arg] = Posts_Table_Args::$default_args[$arg];
			}
		}

		// Sanitize image size
		if ( isset( $args['image_size'] ) ) {
			$args['image_size'] = preg_replace( '/[^\wx\-]/', '', $args['image_size'] );
		}

		// Check ints
		foreach ( array( 'rows_per_page', 'content_length', 'excerpt_length', 'post_limit' ) as $arg ) {
			if ( ! isset( $args[$arg] ) ) {
				continue;
			}
			if ( false === ( $int_val = filter_var( $args[$arg], FILTER_VALIDATE_INT ) ) ) {
				$args[$arg] = Posts_Table_Args::$default_args[$arg];
			}
			// These can be a positive int or -1 only
			if ( 0 === $int_val || $int_val < -1 ) {
				$args[$arg] = -1;
			}
		}

		// Check bools
		foreach ( array( 'lightbox', 'shortcodes', 'lazy_load', 'cache', 'reset_button' ) as $arg ) {
			if ( ! isset( $args[$arg] ) ) {
				$args[$arg] = false;
			}
			$args[$arg] = filter_var( $args[$arg], FILTER_VALIDATE_BOOLEAN );
		}

		return $args;
	}

	public static function sanitize_misc_settings( $args ) {
		if ( isset( $args['cache_expiry'] ) ) {
			$args['cache_expiry'] = filter_var( $args['cache_expiry'], FILTER_VALIDATE_INT, array( 'options' => array( 'default' => 6 ) ) );
		}
		return $args;
	}

	private static function get_setting( $option, $default ) {
		$option_value = get_option( $option, $default );

		if ( empty( $option_value ) || ! is_array( $option_value ) ) {
			$option_value = $default;
		}

		return $option_value;
	}

}