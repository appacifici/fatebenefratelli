<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the date column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Date extends Abstract_Posts_Table_Data {

	private $date_format;

	public function __construct( $post, $date_format ) {
		parent::__construct( $post );

		$this->date_format = $date_format;
	}

	public function get_data() {
		$date = get_the_date( $this->date_format, $this->post );
		return apply_filters( 'posts_table_data_date', $date, $this->post );
	}

	public function get_sort_data() {
		return get_the_date( 'U', $this->post );
	}

}