<?php
/**
 * Template functions for Posts Table Pro.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ptp_get_posts_table' ) ) {

	function ptp_get_posts_table( $args = array() ) {
		// Create and return the table as HTML
		$table = Posts_Table_Factory::create( $args );
		return $table->get_table( 'html' );
	}
}

if ( ! function_exists( 'ptp_the_posts_table' ) ) {

	function ptp_the_posts_table( $args = array() ) {
		echo ptp_get_posts_table( $args );
	}
}
