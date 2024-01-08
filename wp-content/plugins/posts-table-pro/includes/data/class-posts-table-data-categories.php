<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the categories column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Categories extends Abstract_Posts_Table_Data {

	public function get_data() {
		if ( array_intersect( array( 'all', 'categories' ), $this->links ) ) {
			$categories = get_the_category_list( parent::get_separator( 'categories' ), '', $this->post->ID );
		} else {
			$the_categories = get_the_category( $this->post->ID );

			if ( $the_categories ) {
				$categories = implode( parent::get_separator( 'categories' ), wp_list_pluck( $the_categories, 'name' ) );
			} else {
				$categories = apply_filters( 'the_category', __( 'Uncategorized', 'posts-table-pro' ), parent::get_separator( 'categories' ), '' );
			}
		}

		return apply_filters( 'posts_table_data_categories', $categories, $this->post );
	}

}