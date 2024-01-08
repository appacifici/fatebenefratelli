<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract post data class used to fetch data for a post in the table.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
abstract class Abstract_Posts_Table_Data implements Posts_Table_Data {

	protected $post;
	protected $links;

	public function __construct( $post, $links = '' ) {
		$this->post	 = $post;
		$this->links = $links ? (array) $links : array();
	}

	public function get_filter_data() {
		return '';
	}

	public function get_sort_data() {
		return '';
	}

	protected static function get_separator( $item_type ) {
		$sep = ', ';

		if ( 'custom_field_row' === $item_type ) {
			$sep = '<br/>';
		}

		return apply_filters( 'posts_table_separator', apply_filters( "posts_table_separator_{$item_type}", $sep ) );
	}

}