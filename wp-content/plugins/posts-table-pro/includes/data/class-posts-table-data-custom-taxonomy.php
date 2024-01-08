<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for a custom taxonomy column.
 *
 * @package   Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Custom_Taxonomy extends Abstract_Posts_Table_Data {

	private $taxonomy;
	private $date_format;
	private $is_date;

	public function __construct( $post, $taxonomy, $links = '', $date_format = '', $is_date = false ) {
		parent::__construct( $post, $links );

		$this->taxonomy		 = $taxonomy;
		$this->date_format	 = $date_format;
		$this->is_date		 = $is_date;
	}

	public function get_data() {
		$terms	 = '';
		$sep	 = parent::get_separator( 'terms' );

		if ( array_intersect( array( 'all', 'terms' ), $this->links ) ) {
			$terms = Posts_Table_Util::empty_if_false( get_the_term_list( $this->post->ID, $this->taxonomy, '', $sep ) );
		} else {
			$terms = Posts_Table_Util::get_the_term_names( $this->post, $this->taxonomy, $sep );
		}

		/* @deprecated 2.0.2 - Replaced by 'posts_table_data_custom_taxonomy'. */
		$terms = apply_filters( 'posts_table_data_terms', $terms, $this->post, $this->taxonomy );

		// Filter the result
		$terms	 = apply_filters( 'posts_table_data_custom_taxonomy', $terms, $this->taxonomy, $this->post );
		$terms	 = apply_filters( 'posts_table_data_custom_taxonomy_' . $this->taxonomy, $terms, $this->post );

		return $terms;
	}

	public function get_sort_data() {
		if ( $this->is_date ) {
			$date_terms = get_the_terms( $this->post->ID, $this->taxonomy );

			if ( is_array( $date_terms ) && 1 === count( $date_terms ) ) {
				$date_term = reset( $date_terms );

				// Format the hidden date column for sorting
				if ( $timestamp = Posts_Table_Util::strtotime( $date_term->name, $this->date_format ) ) {
					return $timestamp;
				}
			}
		}
		return '';
	}

}