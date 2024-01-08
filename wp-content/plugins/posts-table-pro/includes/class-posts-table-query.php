<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for managing the posts table query, retrieving the list of posts (as an array of WP_Post objects), and finding the post totals.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Query {

	public $args;
	private $posts					 = null;
	private $total_posts			 = null;
	private $total_filtered_posts	 = null;

	public function __construct( Posts_Table_Args $args ) {
		$this->args = $args;
	}

	public function get_posts() {
		if ( is_array( $this->posts ) ) {
			return $this->posts;
		}

		// Build query args and retrieve the posts for our table
		$query	 = $this->run_posts_query( $this->build_posts_query() );
		$posts	 = ! empty( $query->posts ) ? $query->posts : array();
		$this->set_posts( $posts );

		return $this->posts;
	}

	public function set_posts( $posts ) {
		if ( ! is_array( $posts ) ) {
			$posts = null;
		}
		$this->posts = $posts;
	}

	public function get_total_posts() {
		if ( is_numeric( $this->total_posts ) ) {
			return $this->total_posts;
		}

		$total_args	 = $this->build_post_totals_query();
		$total_query = $this->run_posts_query( $total_args );

		$this->total_posts = $this->check_within_post_limit( $total_query->post_count );
		return $this->total_posts;
	}

	public function set_total_posts( $total_posts ) {
		$this->total_posts = $total_posts;
	}

	public function get_total_filtered_posts() {
		if ( is_numeric( $this->total_filtered_posts ) ) {
			// If we've already calculated it
			return $this->total_filtered_posts;
		} elseif ( empty( $this->args->search_term ) && empty( $this->args->search_filters ) ) {
			// If we have no search term, the filtered total will be same as unfiltered
			$this->total_filtered_posts = $this->get_total_posts();
		} elseif ( is_array( $this->posts ) ) {
			// If we already have posts, then this must be the filtered list, so return count from this array
			$this->total_filtered_posts = count( $this->posts );
		} else {
			// Otherwise we need to calculate total by running a new query.
			$filtered_total_args				 = $this->build_post_totals_query();
			$filtered_total_args['tax_query']	 = $this->build_search_filters_tax_query( $filtered_total_args['tax_query'] );
			$filtered_total_query				 = $this->run_posts_query( $filtered_total_args );

			$this->total_filtered_posts = $this->check_within_post_limit( $filtered_total_query->post_count );
		}

		return $this->total_filtered_posts;
	}

	public function set_total_filtered_posts( $total_filtered_posts ) {
		$this->total_filtered_posts = $total_filtered_posts;
	}

	private function build_base_posts_query() {
		$sort_by = str_replace( 'id', 'ID', $this->args->sort_by );

		$query_args = array(
			'post_type'			 => $this->args->post_type,
			'post_status'		 => $this->args->status,
			'tax_query'			 => $this->build_tax_query(),
			'meta_query'		 => $this->build_meta_query(),
			'year'				 => $this->args->year,
			'monthnum'			 => $this->args->month,
			'day'				 => $this->args->day,
			'author'			 => $this->args->author, // ID or string of IDs, not an array
			'order'				 => strtoupper( $this->args->sort_order ),
			'orderby'			 => $sort_by,
			'no_found_rows'		 => true,
			'suppress_filters'	 => false // Ensure WPML filters run on this query
		);

		if ( $this->args->include ) {
			$query_args['post__in']				 = $this->args->include;
			$query_args['ignore_sticky_posts']	 = true;
		} elseif ( $this->args->exclude ) {
			$query_args['post__not_in'] = $this->args->exclude;
		}

		if ( ! empty( $this->args->search_term ) ) {
			$query_args['s'] = $this->args->search_term;
		}

		return $query_args;
	}

	private function build_posts_query() {
		$query_args				 = $this->build_base_posts_query();
		$query_args['tax_query'] = $this->build_search_filters_tax_query( $query_args['tax_query'] );

		if ( $this->args->lazy_load ) {
			// Ensure rows per page doesn't exceed post limit
			$query_args['posts_per_page']	 = $this->check_within_post_limit( $this->args->rows_per_page );
			$query_args['offset']			 = $this->args->offset;
		} else {
			$query_args['posts_per_page'] = $this->args->post_limit;
		}

		return apply_filters( 'posts_table_query_args', $query_args, $this );
	}

	private function build_post_totals_query() {
		$query_args						 = $this->build_base_posts_query();
		$query_args['offset']			 = 0;
		$query_args['posts_per_page']	 = -1;
		$query_args['fields']			 = 'ids';

		return apply_filters( 'posts_table_query_args', $query_args, $this );
	}

	private function build_tax_query() {
		$tax_query = array();

		// Category handling
		if ( ! empty( $this->args->category ) ) {
			$tax_query[] = $this->tax_query_item( $this->args->category, 'category' );
		}
		if ( ! empty( $this->args->exclude_category ) ) {
			$tax_query[] = $this->tax_query_item( $this->args->exclude_category, 'category', 'NOT IN' );
		}

		// Tag handling
		if ( ! empty( $this->args->tag ) ) {
			$tax_query[] = $this->tax_query_item( $this->args->tag, 'post_tag' );
		}

		// Custom taxonomy/term handling
		if ( ! empty( $this->args->term ) ) {
			$term_query	 = array();
			$relation	 = 'OR';

			if ( false !== strpos( $this->args->term, '+' ) ) {
				$term_array	 = explode( '+', $this->args->term );
				$relation	 = 'AND';
			} else {
				$term_array = explode( ',', $this->args->term );
			}

			$current_taxonomy = false;

			// Custom terms are in format <taxonomy>:<term slug or id> or a list using just one taxonony, e.g. product_cat:term1,term2
			foreach ( $term_array as $term ) {
				if ( '' === $term ) {
					continue;
				}
				// Split term around the colon and check valid
				$term_split = explode( ':', $term, 2 );

				if ( 1 === count( $term_split ) ) {
					if ( ! $current_taxonomy ) {
						continue;
					}
					$term = $term_split[0];
				} elseif ( 2 === count( $term_split ) ) {
					$term				 = $term_split[1];
					$current_taxonomy	 = $term_split[0];
				}
				$term_query[] = $this->tax_query_item( $term, $current_taxonomy );
			}

			$term_query = $this->maybe_add_relation( $term_query, $relation );

			// If no tax query, set the whole tax query to the custom terms query, otherwise append terms as inner query
			if ( empty( $tax_query ) ) {
				$tax_query = $term_query;
			} else {
				$tax_query[] = $term_query;
			}
		}

		return apply_filters( 'posts_table_tax_query', $this->maybe_add_relation( $tax_query ), $this );
	}

	private function build_search_filters_tax_query( $tax_query = array() ) {
		if ( ! is_array( $tax_query ) ) {
			$tax_query = array();
		}

		if ( empty( $this->args->search_filters ) ) {
			return $tax_query;
		}

		$search_filters_query = array();

		// Add tax queries for search filter drop-downs.
		foreach ( $this->args->search_filters as $taxonomy => $term ) {
			// Search filters always use term IDs
			$search_filters_query[] = $this->tax_query_item( $term, $taxonomy, 'IN', 'term_id' );
		}

		$search_filters_query = $this->maybe_add_relation( $search_filters_query );

		if ( empty( $tax_query ) ) {
			// If no tax query, set the whole tax query to the filters query.
			$tax_query = $search_filters_query;
		} elseif ( isset( $tax_query['relation'] ) && 'OR' === $tax_query['relation'] ) {
			// If tax query is an OR, nest it with the search filters query and join with AND.
			$tax_query = array( $tax_query, $search_filters_query, 'relation' => 'AND' );
		} else {
			// Otherwise append search filters and ensure it's AND.
			$tax_query[]			 = $search_filters_query;
			$tax_query['relation']	 = 'AND';
		}

		return $tax_query;
	}

	/**
	 * Generate an inner array for the 'tax_query' arg in WP_Query.
	 *
	 * @param string $terms The list of terms as a string
	 * @param string $taxonomy The taxonomy name
	 * @param string $operator IN, NOT IN, AND, etc
	 * @return array A tax query sub-array
	 */
	private function tax_query_item( $terms, $taxonomy, $operator = 'IN', $field = '' ) {
		$and_relation = 'AND' === $operator;

		// comma-delimited list = OR, plus-delimited = AND
		if ( ! is_array( $terms ) ) {
			if ( false !== strpos( $terms, '+' ) ) {
				$terms			 = explode( '+', $terms );
				$and_relation	 = true;
			} else {
				$terms = explode( ',', $terms );
			}
		}

		// Do we have slugs or IDs?
		if ( ! $field ) {
			$using_term_ids	 = count( $terms ) === count( array_filter( $terms, 'is_numeric' ) );
			$field			 = $using_term_ids && ! $this->args->numeric_terms ? 'term_id' : 'slug';
		}

		// Strange bug when using operator => 'AND' in individual tax queries -
		// We need to separate these out into separate 'IN' arrays joined by and outer relation => 'AND'
		if ( $and_relation && count( $terms ) > 1 ) {
			$result = array( 'relation' => 'AND' );

			foreach ( $terms as $term ) {
				$result[] = array(
					'taxonomy'	 => $taxonomy,
					'terms'		 => $term,
					'operator'	 => 'IN',
					'field'		 => $field
				);
			}

			return $result;
		} else {
			return array(
				'taxonomy'	 => $taxonomy,
				'terms'		 => $terms,
				'operator'	 => $operator,
				'field'		 => $field
			);
		}
	}

	private function build_meta_query() {
		$meta_query = array();

		if ( $this->args->cf ) {
			$custom_field_query		 = array();
			$custom_field_relation	 = 'OR';

			// comma-delimited = OR, plus-delimited = AND
			if ( false !== strpos( $this->args->cf, '+' ) ) {
				$field_array			 = explode( '+', $this->args->cf );
				$custom_field_relation	 = 'AND';
			} else {
				$field_array = explode( ',', $this->args->cf );
			}

			// Custom fields are in format <field_key>:<field_value>
			foreach ( $field_array as $field ) {
				// Split custom field around the colon and check valid
				$field_split = explode( ':', $field, 2 );

				if ( count( $field_split ) === 2 ) {
					// We have a field key and value
					$field_key	 = $field_split[0];
					$field_value = $field_split[1];
					$compare	 = '=';

					// If we're selecting based on an ACF field, field value could be stored as an array, so use RLIKE with a test for serialized array pattern
					if ( Posts_Table_Util::is_acf_active() ) {
						$compare	 = 'REGEXP';
						$field_value = sprintf( '^%1$s$|s:%2$u:"%1$s";', $field_value, strlen( $field_value ) );
					}

					$custom_field_query[] = array(
						'key'		 => $field_key,
						'value'		 => $field_value,
						'compare'	 => $compare
					);
				} elseif ( count( $field_split ) === 1 ) {
					// Field key only, so do an 'exists' check instead
					$custom_field_query[] = array(
						'key'		 => $field_split[0],
						'compare'	 => 'EXISTS'
					);
				}
			}

			$meta_query['posts_table'] = $this->maybe_add_relation( $custom_field_query, $custom_field_relation );
		}

		return apply_filters( 'posts_table_meta_query', $meta_query, $this );
	}

	private function maybe_add_relation( $query, $relation = 'AND' ) {
		if ( is_array( $query ) && count( $query ) > 1 && empty( $query['relation'] ) ) {
			$query['relation'] = $relation;
		}

		return $query;
	}

	private function run_posts_query( $query_args ) {
		// Add our query hooks before running the query.
		$this->add_query_hooks();

		$query = new WP_Query( $query_args );

		// Remove the hooks to prevent them interfering with anything else.
		$this->remove_query_hooks();

		return $query;
	}

	private function add_query_hooks() {
		// Query optimisations.

		if ( apply_filters( 'posts_table_optimize_table_query', true, $this->args ) ) {
			add_filter( 'posts_fields', array( $this, 'filter_wp_posts_selected_columns' ), 10, 2 );
		}
	}

	private function remove_query_hooks() {
		if ( apply_filters( 'posts_table_optimize_table_query', true, $this->args ) ) {
			remove_filter( 'posts_fields', array( $this, 'filter_wp_posts_selected_columns' ), 10 );
		}
	}

	public function filter_wp_posts_selected_columns( $fields, $query ) {
		global $wpdb;

		if ( "{$wpdb->posts}.*" !== $fields ) {
			return $fields;
		}

		if ( array_diff( array( 'content', 'excerpt' ), $this->args->columns ) ) {
			$posts_columns = array( 'ID', 'post_author', 'post_date', 'post_date_gmt', 'post_title',
				'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged',
				'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order',
				'post_type', 'post_mime_type', 'comment_count' );

			// Only select post_content if it's definitely needed
			if ( in_array( 'content', $this->args->columns ) ) {
				$posts_columns[] = 'post_content';
			}
			// Only select post_excerpt if it's definitely needed
			if ( in_array( 'excerpt', $this->args->columns ) ) {
				$posts_columns[] = 'post_excerpt';
				// We need the content as well, in case we need to auto-generate the excerpt from the content
				$posts_columns[] = 'post_content';
			}

			$fields = sprintf( implode( ', ', array_map( array( __CLASS__, 'array_map_prefix_column' ), $posts_columns ) ), $wpdb->posts );
		}

		return $fields;
	}

	private function check_within_post_limit( $count ) {
		return is_int( $this->args->post_limit ) && $this->args->post_limit > 0 ? min( $this->args->post_limit, $count ) : $count;
	}

	private static function array_map_prefix_column( $n ) {
		return '%1$s.' . $n;
	}

}