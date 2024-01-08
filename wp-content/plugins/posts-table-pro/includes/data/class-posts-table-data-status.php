<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the post status column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Status extends Abstract_Posts_Table_Data {

	public function get_data() {
		return apply_filters( 'posts_table_data_status', ucfirst( $this->post->post_status ), $this->post );
	}

}