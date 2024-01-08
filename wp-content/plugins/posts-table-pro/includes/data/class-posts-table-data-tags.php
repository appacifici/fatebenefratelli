<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for the tags column.
 *
 * @package	  Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Tags extends Abstract_Posts_Table_Data {

	public function get_data() {
		$tags = '';

		if ( array_intersect( array( 'all', 'tags' ), $this->links ) ) {
			$tags = Posts_Table_Util::empty_if_false( get_the_tag_list( '', $this->get_separator( 'tags' ), '', $this->post->ID ) );
		} else {
			$the_tags = get_the_tags( $this->post->ID );

			if ( $the_tags && is_array( $the_tags ) ) {
				$tags = implode( parent::get_separator( 'tags' ), wp_list_pluck( $the_tags, 'name' ) );
			}
		}

		return apply_filters( 'posts_table_data_tags', $tags, $this->post );
	}

}