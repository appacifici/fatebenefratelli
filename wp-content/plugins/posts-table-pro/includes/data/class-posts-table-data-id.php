<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the ID column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Id extends Abstract_Posts_Table_Data {

	public function get_data() {
		if ( array_intersect( array( 'all', 'id' ), $this->links ) ) {
			$id = Posts_Table_Util::format_post_link( $this->post, $this->post->ID );
		} else {
			$id = $this->post->ID;
		}

		return apply_filters( 'posts_table_data_id', $id, $this->post );
	}

}