<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the title column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Title extends Abstract_Posts_Table_Data {

	public function get_data() {
		//@todo - Don't link to post if it's not viewable on front-end
		if ( array_intersect( array( 'all', 'title' ), $this->links ) ) {
			$title = Posts_Table_Util::format_post_link( $this->post );
		} else {
			$title = get_the_title( $this->post );
		}

		return apply_filters( 'posts_table_data_title', $title, $this->post );
	}

}