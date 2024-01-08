<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the AJAX requests for posts tables that have AJAX enabled.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Ajax_Handler {

	public static function register_ajax_events() {
		add_action( 'wp_ajax_nopriv_ptp_load_posts', array( __CLASS__, 'load_posts' ) );
		add_action( 'wp_ajax_ptp_load_posts', array( __CLASS__, 'load_posts' ) );
	}

	public static function load_posts() {
		$table_id	 = filter_input( INPUT_POST, 'table_id', FILTER_SANITIZE_STRING );
		$table		 = Posts_Table_Factory::fetch( $table_id );

		if ( ! $table ) {
			wp_die( 'Error: posts table could not be loaded.' );
		}

		// Build the args to update
		$new_args					 = array();
		$new_args['rows_per_page']	 = filter_input( INPUT_POST, 'length', FILTER_VALIDATE_INT );
		$new_args['offset']			 = filter_input( INPUT_POST, 'start', FILTER_VALIDATE_INT );

		$columns	 = filter_input( INPUT_POST, 'columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$search		 = filter_input( INPUT_POST, 'search', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$order		 = filter_input( INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$main_order	 = ! empty( $order[0] ) ? $order[0] : array();

		if ( isset( $main_order['column'] ) ) {
			$order_col_index = filter_var( $main_order['column'], FILTER_VALIDATE_INT );

			if ( false !== $order_col_index && isset( $columns[$order_col_index]['data'] ) ) {
				$new_args['sort_by'] = filter_var( $columns[$order_col_index]['data'], FILTER_SANITIZE_STRING );
			}
			if ( ! empty( $main_order['dir'] ) ) {
				$new_args['sort_order'] = filter_var( $main_order['dir'], FILTER_SANITIZE_STRING );
			}
		}

		$new_args['search_term']	 = '';
		$new_args['search_filters']	 = array();

		if ( ! empty( $search['value'] ) ) {
			$search_term = filter_var( $search['value'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );

			// Don't search unless they've typed at least 3 characters.
			if ( Posts_Table_Util::is_valid_search_term( $search_term ) ) {
				$new_args['search_term'] = $search_term;
			}
		}

		if ( ! empty( $columns ) ) {
			foreach ( $columns as $column ) {
				if ( empty( $column['data'] ) || empty( $column['search']['value'] ) ) {
					continue;
				}

				// For a filter column, we search by slug; for "click searching" we use name
				$field = false !== strpos( $column['data'], '_hfilter' ) ? 'slug' : 'name';

				if ( $taxonomy = Posts_Table_Columns::get_column_taxonomy( $column['data'] ) ) {
					$term = get_term_by( $field, $column['search']['value'], $taxonomy );

					if ( $term instanceof WP_Term ) {
						$new_args['search_filters'][$taxonomy] = $term->term_id;
					}
				}
			}
		}

		// Retrieve the new table and convert to array
		$table->update( $new_args );

		// Build output
		$output['draw']				 = filter_input( INPUT_POST, 'draw', FILTER_VALIDATE_INT );
		$output['recordsFiltered']	 = $table->query->get_total_filtered_posts();
		$output['recordsTotal']		 = $table->query->get_total_posts();

		$table_data	 = $table->get_data( 'array' );
		$data		 = array();

		if ( ! empty( $table_data ) ) {
			// We don't need the cell attributes, so flatten data and append row attributes under the key '__attributes'.
			foreach ( $table_data as $row ) {
				$data[] = array_merge( array(
					'__attributes' => $row['attributes']
					), wp_list_pluck( $row['cells'], 'data' )
				);
			}
		}

		$output['data'] = $data;

		wp_send_json( $output );
	}

}