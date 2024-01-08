<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Factory class to get the posts table data object for a given column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Factory {

	/**
	 * The full list of table args.
	 *
	 * @var Posts_Table_Args
	 */
	private $args;

	public function __construct( $args ) {
		$this->args = $args;
	}

	public function create( $column, $post ) {
		$data_obj = false;

		switch ( $column ) {
			case 'id':
			case 'title':
			case 'categories':
			case 'tags':
			case 'author':
			case 'status':
				$data_class = 'Posts_Table_Data_' . ucfirst( $column );
				if ( class_exists( $data_class ) ) {
					$data_obj = new $data_class( $post, $this->args->links );
				}
				break;
			case 'image';
				$data_obj	 = new Posts_Table_Data_Image( $post, $this->args->links, $this->args->image_size, $this->args->lightbox );
				break;
			case 'date';
				$data_obj	 = new Posts_Table_Data_Date( $post, $this->args->date_format );
				break;
			case 'content':
				$data_obj	 = new Posts_Table_Data_Content( $post );
				break;
			case 'excerpt':
				$data_obj	 = new Posts_Table_Data_Excerpt( $post );
				break;
			default:
				if ( $taxonomy	 = Posts_Table_Columns::get_custom_taxonomy( $column ) ) {
					$data_obj = new Posts_Table_Data_Custom_Taxonomy( $post, $taxonomy, $this->args->links, $this->args->date_format, $this->is_date_column( $column ) );
				} elseif ( $field = Posts_Table_Columns::get_custom_field( $column ) ) {
					$data_obj = new Posts_Table_Data_Custom_Field( $post, $field, $this->args->links, $this->args->image_size, $this->args->date_format, $this->is_date_column( $column ) );
				} elseif ( Posts_Table_Columns::is_filter_column( $column ) ) {
					$data_obj = new Posts_Table_Data_Hidden_Filter( $post, $column, $this->args->lazy_load );
				} else {
					/**
					 * Support for custom columns added by 3rd party code.
					 * Developers: this filter should return an object implementing the Posts_Table_Data interface.
					 *
					 * @see Posts_Table_Data
					 */
					$data_obj = apply_filters( 'posts_table_custom_table_data_' . $column, false, $post, $this->args );
				}
				break;
		}

		return $data_obj;
	}

	private function is_date_column( $column ) {
		return in_array( $column, $this->args->date_columns );
	}

}