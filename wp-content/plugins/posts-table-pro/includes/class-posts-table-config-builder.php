<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for creating the posts table config script.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Config_Builder {

	/**
	 * @var string The table ID.
	 */
	public $id;
	/**
	 * @var Posts_Table_Args The table args.
	 */
	public $args;
	/**
	 * @var Posts_Table_Columns The table columns.
	 */
	public $columns;

	public function __construct( $id, Posts_Table_Args $args, Posts_Table_Columns $columns ) {
		$this->id		 = $id;
		$this->args		 = $args;
		$this->columns	 = $columns;
	}

	/**
	 * Build config for this table to add as an inline script below table.
	 *
	 * @return array The posts table config
	 */
	public function get_config() {

		$config = array(
			'pageLength'	 => $this->args->rows_per_page,
			'pagingType'	 => $this->args->paging_type,
			'serverSide'	 => $this->args->lazy_load,
			'autoWidth'		 => $this->args->auto_width,
			'clickFilter'	 => $this->args->search_on_click,
			'scrollOffset'	 => $this->args->scroll_offset,
			'resetButton'	 => $this->args->reset_button
		);

		$config['lengthMenu'] = array( 10, 25, 50, 100 );

		if ( $this->args->rows_per_page > 0 && ! in_array( $this->args->rows_per_page, $config['lengthMenu'] ) ) {
			// Remove any default page lengths that are too close to 'rows_per_page'
			$config['lengthMenu'] = array_filter( $config['lengthMenu'], array( $this, 'array_filter_length_menu' ) );

			// Add 'rows_per_page' to length menu and sort
			array_push( $config['lengthMenu'], $this->args->rows_per_page );
			sort( $config['lengthMenu'] );
		}

		// All show all to menu
		if ( ! $this->args->lazy_load || -1 === $this->args->rows_per_page ) {
			$config['lengthMenu']		 = array( $config['lengthMenu'], $config['lengthMenu'] );
			$config['lengthMenu'][0][]	 = -1;
			$config['lengthMenu'][1][]	 = _x( 'All', 'show all posts option', 'posts-table-pro' );
		}

		$responsive_details = array();

		// Set responsive control column
		if ( 'column' === $this->args->responsive_control ) {
			$responsive_details		 = array( 'type' => 'column' );
			$config['columnDefs'][]	 = array( 'className' => 'control', 'orderable' => false, 'targets' => 0 );
		}

		foreach ( $this->columns->get_columns() as $column ) {
			$class = Posts_Table_Columns::get_column_class( $column );

			if ( 'date' === $column ) {
				// If date column used and date format contains no spaces, make sure we 'nowrap' this column
				$date_format = $this->args->date_format ? $this->args->date_format : get_option( 'date_format' );

				if ( false === strpos( $date_format, ' ' ) ) {
					$class .= ' nowrap';
				}
			}
			$config['columnDefs'][] = array( 'className' => $class, 'targets' => $this->columns->column_index( $column ) );
		}

		// Set responsive display function
		if ( $this->args->responsive_display !== 'child_row' ) {
			$responsive_details = array_merge( $responsive_details, array( 'display' => $this->args->responsive_display ) );
		}
		if ( $responsive_details ) {
			$config['responsive'] = array( 'details' => $responsive_details );
		}

		// Set custom messages
		if ( $this->args->no_posts_message ) {
			$config['language']['emptyTable'] = $this->args->no_posts_message;
		}
		if ( $this->args->no_posts_filtered_message ) {
			$config['language']['zeroRecords'] = $this->args->no_posts_filtered_message;
		}

		// Set initial search term
		if ( $this->args->search_term ) {
			$config['search']['search'] = $this->args->search_term;
		}

		// DOM option
		$dom_top		 = $dom_bottom		 = '';
		$display_options = array(
			'l'	 => 'page_length',
			'f'	 => 'search_box',
			'i'	 => 'totals',
			'p'	 => 'pagination'
		);

		foreach ( $display_options as $letter => $option ) {
			if ( 'top' === $this->args->$option || 'both' === $this->args->$option ) {
				$dom_top .= $letter;
			}
			if ( 'bottom' === $this->args->$option || 'both' === $this->args->$option ) {
				$dom_bottom .= $letter;
			}
		}

		$dom_top		 = '<"posts-table-above posts-table-controls"' . $dom_top . '>';
		$dom_bottom		 = $dom_bottom ? '<"posts-table-below posts-table-controls"' . $dom_bottom . '>' : '';
		$config['dom']	 = sprintf( '<"%s"%st%s>', esc_attr( Posts_Table_Util::get_wrapper_class() ), $dom_top, $dom_bottom );

		// @deprecated 2.1 - Replaced by posts_table_data_config
		$config = apply_filters( 'posts_table_inline_config', $config, $this );

		$config = apply_filters( 'posts_table_data_config', $config, $this->args );
		return $config ? $config : false;
	}

	public function get_filters() {
		if ( ! $this->args->filters ) {
			return false;
		}

		$filters = array();

		// Add drop-down values for each search filter
		foreach ( $this->args->filters as $filter ) {
			if ( ! ( $tax = Posts_Table_Columns::get_column_taxonomy( $filter ) ) ) {
				continue;
			}

			if ( ! ( $terms = $this->get_terms_for_filter( $tax ) ) ) {
				continue;
			}

			$column_name = Posts_Table_Columns::get_column_name( $filter );
			$heading	 = $this->columns->get_column_heading( array_search( $filter, $this->columns->get_columns() ), $filter );
			$heading	 = apply_filters( 'posts_table_search_filter_heading_' . $column_name, $heading, $this->args );

			// Add terms to array
			$filters[$column_name] = array(
				'taxonomy'	 => $tax,
				'heading'	 => $heading,
				'terms'		 => $terms
			);
		}

		$filters = apply_filters( 'posts_table_data_filters', $filters, $this->args );
		return $filters ? $filters : false;
	}

	private function get_terms_for_filter( $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}

		$term_args = array(
			'taxonomy'		 => $taxonomy,
			'fields'		 => 'all',
			'hide_empty'	 => true,
			'hierarchical'	 => true,
		);

		if ( 'category' === $taxonomy && ( $this->args->category || $this->args->exclude_category ) ) {
			// Getting terms for category filter and category or excluded category selected in shortcode

			if ( $exclude = Posts_Table_Util::convert_to_term_ids( $this->args->exclude_category, 'category' ) ) {
				// If we're excluding a category from table, remove this and all descendant terms from the category search filter
				$term_args['exclude_tree'] = $exclude;
			}
			if ( $this->args->category && $category_ids = Posts_Table_Util::convert_to_term_ids( $this->args->category, 'category' ) ) {
				// Find all descendents and include them in term query
				$include_ids = Posts_Table_Util::get_all_term_children( $category_ids, 'category', true );

				// Remove any excludes
				if ( $exclude ) {
					$include_ids = array_diff( $include_ids, $exclude );
				}
				$term_args['include'] = $include_ids;
			}
		} elseif ( $this->args->term && 'post_tag' !== $taxonomy ) {
			// Getting terms for custom taxonomy filter and term(s) selected in shortcode - see if we need to restrict terms in filter
			$custom_terms		 = explode( ',', str_replace( '+', ',', $this->args->term ) );
			$current_taxonomy	 = false;
			$terms_in_tax		 = array();

			foreach ( $custom_terms as $tax_term ) {
				// Split term around the colon and check valid
				$term_split = explode( ':', $tax_term, 2 );

				if ( 2 === count( $term_split ) ) {
					if ( $taxonomy !== $term_split[0] ) {
						continue;
					}
					$current_taxonomy	 = $term_split[0];
					$terms_in_tax[]		 = $term_split[1];
				} elseif ( 1 === count( $term_split ) && $taxonomy === $current_taxonomy ) {
					$terms_in_tax[] = $term_split[0];
				}
			}
			if ( $term_ids = Posts_Table_Util::convert_to_term_ids( $terms_in_tax, $taxonomy ) ) {
				$term_args['include'] = Posts_Table_Util::get_all_term_children( $term_ids, $taxonomy, true );
			}
		}

		// Get the terms
		$terms = Posts_Table_Util::get_terms( apply_filters( 'posts_table_search_filter_get_terms_args', $term_args, $taxonomy, $this->args ) );

		// Filter the terms.
		$terms	 = apply_filters( 'posts_table_search_filter_terms', $terms, $taxonomy, $this->args );
		$terms	 = apply_filters( 'posts_table_search_filter_terms_' . $taxonomy, $terms, $this->args );

		// Convert term objects to arrays, and re-key
		$result = array_map( 'get_object_vars', array_values( $terms ) );

		// Build term hierarchy so we can create the nested filter items
		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$result = $this->build_term_tree( $result );
		}

		// Just return term name, slug and child terms for the filter
		$result = Posts_Table_Util::list_pluck_array( $result, array( 'name', 'slug', 'children' ) );

		//@deprecated 2.1 - replaced by posts_table_search_filter_terms_<taxonomy>.
		$result = apply_filters( 'posts_table_filter_terms_' . $taxonomy, $result, $this->args );

		return $result;
	}

	private function build_term_tree( array &$terms, $parent_id = 0 ) {
		$branch = array();

		foreach ( $terms as $i => $term ) {
			if ( isset( $term['parent'] ) && $term['parent'] == $parent_id ) {
				$children = $this->build_term_tree( $terms, $term['term_id'] );

				if ( $children ) {
					$term['children'] = $children;
				}
				$branch[] = $term;
				unset( $terms[$i] );
			}
		}

		// If we're at the top level branch (parent = 0) and there are terms remaining, we need to
		// loop through each and build the tree for that term.
		if ( 0 === $parent_id && $terms ) {
			$remaining_term_ids = wp_list_pluck( $terms, 'term_id' );

			foreach ( $terms as $term ) {
				if ( ! isset( $term['parent'] ) ) {
					continue;
				}
				// Only build tree if term won't be 'picked up' by its parent term.
				if ( ! in_array( $term['parent'], $remaining_term_ids ) ) {
					$branch = array_merge( $branch, $this->build_term_tree( $terms, $term['parent'] ) );
				}
			}
		}

		return $branch;
	}

	private function array_filter_length_menu( $length ) {
		$diff = abs( $length - $this->args->rows_per_page );
		return $diff / $length > 0.2 || $diff > 4;
	}

}