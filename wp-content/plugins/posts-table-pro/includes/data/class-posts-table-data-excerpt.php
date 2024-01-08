<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the excerpt column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Excerpt extends Abstract_Posts_Table_Data {

	public function get_data() {
		$excerpt = apply_filters( 'the_excerpt', get_the_excerpt( $this->post ) );
		return apply_filters( 'posts_table_data_excerpt', $excerpt, $this->post );
	}

}