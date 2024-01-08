<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets post data for a hidden filter column.
 *
 * @package	  Posts_Table_Pro\Data
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Data_Hidden_Filter extends Abstract_Posts_Table_Data {

	private $filter_column;
	private $lazy_load;

	public function __construct( $post, $filter_column, $lazy_load = false ) {
		parent::__construct( $post );

		$this->filter_column = $filter_column;
		$this->lazy_load	 = $lazy_load;
	}

	public function get_data() {
		if ( $this->lazy_load ) {
			return '';
		}

		$taxonomy = Posts_Table_Columns::get_column_taxonomy( $this->filter_column );

		if ( ! $taxonomy ) {
			return '';
		}

		$result		 = '';
		$post_terms	 = get_the_terms( $this->post, $taxonomy );

		if ( $post_terms && is_array( $post_terms ) ) {
			// If tax is hierarchical, we need to add any ancestor terms for each term this product has
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$ancestors = array();

				// Get the ancestors term IDs for all terms for this product
				foreach ( $post_terms as $term ) {
					$ancestors = array_merge( $ancestors, get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );
				}

				// Remove duplicates
				$ancestors		 = array_unique( $ancestors );
				$post_term_ids	 = wp_list_pluck( $post_terms, 'term_id' );

				// If not already in term list, convert ancestor to WP_Term object and add to results
				foreach ( $ancestors as $ancestors_term_id ) {
					if ( ! in_array( $ancestors_term_id, $post_term_ids ) ) {
						$term = get_term( $ancestors_term_id, $taxonomy );

						if ( $term instanceof WP_Term ) {
							$post_terms[] = $term;
						}
					}
				}
			}

			$result = implode( ' ', wp_list_pluck( $post_terms, 'slug' ) );
		}

		return apply_filters( 'posts_table_data_hidden_filter', $result, $this->filter_column, $this->post );
	}

}