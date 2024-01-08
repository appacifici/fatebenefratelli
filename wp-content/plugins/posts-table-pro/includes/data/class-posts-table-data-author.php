<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the post author column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Author extends Abstract_Posts_Table_Data {

	public function get_data() {
		if ( array_intersect( array( 'all', 'author' ), $this->links ) ) {
			$author = get_the_author_posts_link();
		} else {
			$author = get_the_author();
		}
		return apply_filters( 'posts_table_data_author', $author, $this->post );
	}

}