<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for storing and validating the posts table arguments.
 * Parses an array of args into the corresponding properties.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Args {

	// The original args array
	private $args = array();

	/* Table params */
	public $columns;
	public $headings;
	public $widths;
	public $auto_width;
	public $priorities;
	public $column_breakpoints;
	public $responsive_control;
	public $responsive_display;
	public $wrap;
	public $show_footer;
	public $search_on_click;
	public $filters;
	public $scroll_offset;
	public $content_length;
	public $excerpt_length;
	public $links;
	public $lazy_load;
	public $cache;
	public $image_size;
	public $lightbox;
	public $shortcodes;
	public $date_format;
	public $date_columns;
	public $no_posts_message;
	public $no_posts_filtered_message;
	public $paging_type;
	public $page_length;
	public $search_box;
	public $totals;
	public $pagination;
	public $reset_button;

	/* Query params */
	public $rows_per_page;
	public $post_limit;
	public $sort_by;
	public $sort_order;
	public $post_type;
	public $status;
	public $category;
	public $exclude_category;
	public $tag;
	public $term;
	public $numeric_terms;
	public $cf;
	public $year;
	public $month;
	public $day;
	public $author;
	public $exclude;
	public $include;
	public $search_term;

	/* Lazy load params */
	public $offset;
	public $search_filters = array();

	/* Internal params */
	public $show_hidden;
	/**
	 * @var array The default table parameters
	 */
	public static $default_args = array(
		'columns'					 => 'image,title,excerpt,categories,author,date', // allowed: id, title, content, excerpt, date, categories (or category), tags, author, status, image, tax:<taxonomy_name>, cf:<custom_field>
		'widths'					 => '',
		'auto_width'				 => true,
		'priorities'				 => '',
		'column_breakpoints'		 => '',
		'responsive_control'		 => 'inline', // inline or column
		'responsive_display'		 => 'child_row', // child_row, child_row_visible, or modal
		'wrap'						 => true,
		'show_footer'				 => false,
		'search_on_click'			 => true,
		'filters'					 => false,
		'scroll_offset'				 => 15,
		'content_length'			 => 15,
		'excerpt_length'			 => -1,
		'links'						 => 'title,categories,tags,terms,author', // set to all or none, or any combination of id, title, terms, tags, categories, author, image
		'lazy_load'					 => false,
		'cache'						 => false,
		'image_size'				 => '70x70',
		'lightbox'					 => false,
		'shortcodes'				 => false,
		'date_format'				 => '',
		'date_columns'				 => '',
		'no_posts_message'			 => '',
		'no_posts_filtered_message'	 => '',
		'paging_type'				 => 'simple_numbers',
		'page_length'				 => 'top',
		'search_box'				 => 'top',
		'totals'					 => 'bottom',
		'pagination'				 => 'bottom',
		'reset_button'				 => true,
		'rows_per_page'				 => 25,
		'post_limit'				 => 500,
		'sort_by'					 => 'date',
		'sort_order'				 => '', // no default set - see parse_args()
		'post_type'					 => 'post',
		'status'					 => 'publish',
		'category'					 => '', // list of slugs or IDs
		'exclude_category'			 => '', // list of slugs or IDs
		'tag'						 => '', // list of slugs or IDs
		'term'						 => '', // list of terms of the form <taxonomy>:<term>
		'numeric_terms'				 => false, // set to true if using categories, tags or terms with numeric slugs
		'cf'						 => '', // list of custom fields of the form <field_key>:<field_value>
		'year'						 => '',
		'month'						 => '',
		'day'						 => '',
		'author'					 => '', // list of author IDs
		'exclude'					 => '', // list of post IDs
		'include'					 => '', // list of post IDs
		'search_term'				 => '',
		'show_hidden'				 => false
	);

	public function __construct( array $args = array() ) {
		$this->set_args( $args );
	}

	public function get_args() {
		return $this->args;
	}

	public function set_args( array $args ) {
		// Lazy load args need to be merged in
		$hidden = array(
			'offset'		 => $this->offset,
			'search_filters' => $this->search_filters
		);

		// Update args
		$this->args = array_merge( $hidden, $this->args, $args );

		// Parse/validate args & update properties
		$this->parse_args( $this->args );
	}

	public static function get_defaults() {
		return wp_parse_args( Posts_Table_Settings::get_setting_shortcode_defaults(), self::$default_args );
	}

	private function parse_args( array $args ) {
		$defaults	 = self::get_defaults();
		$args		 = wp_parse_args( $args, $defaults );

		// Setup validation callbacks
		$sanitize_list			 = array(
			'filter'	 => FILTER_CALLBACK,
			'options'	 => 'Posts_Table_Util::sanitize_list_arg'
		);
		$sanitize_numeric_list	 = array(
			'filter'	 => FILTER_CALLBACK,
			'options'	 => 'Posts_Table_Util::sanitize_numeric_list_arg'
		);
		$sanitize_string_array	 = array(
			'filter' => FILTER_SANITIZE_STRING,
			'flags'	 => FILTER_REQUIRE_ARRAY
		);

		$validation = array(
			'columns'					 => is_array( $args['columns'] ) ? $sanitize_string_array : FILTER_SANITIZE_STRING,
			'widths'					 => $sanitize_list,
			'auto_width'				 => FILTER_VALIDATE_BOOLEAN,
			'priorities'				 => $sanitize_numeric_list,
			'column_breakpoints'		 => $sanitize_list,
			'responsive_control'		 => FILTER_SANITIZE_STRING,
			'responsive_display'		 => FILTER_SANITIZE_STRING,
			'wrap'						 => FILTER_VALIDATE_BOOLEAN,
			'show_footer'				 => FILTER_VALIDATE_BOOLEAN,
			'search_on_click'			 => FILTER_VALIDATE_BOOLEAN,
			'filters'					 => is_bool( $args['filters'] ) ? FILTER_VALIDATE_BOOLEAN : $sanitize_list,
			'scroll_offset'				 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default' => $defaults['scroll_offset']
				)
			),
			'content_length'			 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['content_length'],
					'min_range'	 => -1
				)
			),
			'excerpt_length'			 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['excerpt_length'],
					'min_range'	 => -1
				)
			),
			'links'						 => $sanitize_list,
			'lazy_load'					 => FILTER_VALIDATE_BOOLEAN,
			'cache'						 => FILTER_VALIDATE_BOOLEAN,
			'image_size'				 => $sanitize_list,
			'lightbox'					 => FILTER_VALIDATE_BOOLEAN,
			'shortcodes'				 => FILTER_VALIDATE_BOOLEAN,
			'date_format'				 => FILTER_SANITIZE_STRING,
			'date_columns'				 => $sanitize_list,
			'no_posts_message'			 => FILTER_SANITIZE_STRING,
			'no_posts_filtered_message'	 => FILTER_SANITIZE_STRING,
			'paging_type'				 => FILTER_SANITIZE_STRING,
			'page_length'				 => FILTER_SANITIZE_STRING,
			'search_box'				 => FILTER_SANITIZE_STRING,
			'totals'					 => FILTER_SANITIZE_STRING,
			'pagination'				 => FILTER_SANITIZE_STRING,
			'reset_button'				 => FILTER_VALIDATE_BOOLEAN,
			'rows_per_page'				 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['rows_per_page'],
					'min_range'	 => -1
				)
			),
			'post_limit'				 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['post_limit'],
					'min_range'	 => -1,
					'max_range'	 => 5000,
				)
			),
			'sort_by'					 => FILTER_SANITIZE_STRING,
			'sort_order'				 => FILTER_SANITIZE_STRING,
			'post_type'					 => $sanitize_list,
			'status'					 => $sanitize_list,
			'category'					 => $sanitize_list,
			'exclude_category'			 => $sanitize_list,
			'tag'						 => $sanitize_list,
			'term'						 => $sanitize_list,
			'numeric_terms'				 => FILTER_VALIDATE_BOOLEAN,
			'cf'						 => $sanitize_list,
			'year'						 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['year'],
					'min_range'	 => 1
				)
			),
			'month'						 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['month'],
					'min_range'	 => 1,
					'max_range'	 => 12
				)
			),
			'day'						 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => $defaults['day'],
					'min_range'	 => 1,
					'max_range'	 => 31
				)
			),
			'author'					 => $sanitize_numeric_list,
			'exclude'					 => $sanitize_numeric_list,
			'include'					 => $sanitize_numeric_list,
			'search_term'				 => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'	 => FILTER_FLAG_NO_ENCODE_QUOTES
			),
			// Internal params
			'show_hidden'				 => FILTER_VALIDATE_BOOLEAN,
			// Lazy load params
			'offset'					 => array(
				'filter'	 => FILTER_VALIDATE_INT,
				'options'	 => array(
					'default'	 => 0,
					'min_range'	 => 0,
				)
			),
			'search_filters'			 => $sanitize_string_array
		);

		// Sanitize/validate all args and set object properties from them
		Posts_Table_Util::set_object_vars( $this, filter_var_array( $args, $validation ) );

		// Fill in any blank properties
		foreach ( array( 'columns', 'post_type', 'status', 'image_size', 'sort_by', 'links' ) as $arg ) {
			if ( empty( $this->$arg ) ) {
				$this->$arg = $defaults[$arg];
			}
		}

		// Convert some of the list-based args to arrays
		foreach ( array( 'columns', 'widths', 'priorities', 'column_breakpoints', 'date_columns', 'post_type', 'status', 'exclude', 'include', 'exclude_category' ) as $arg ) {
			if ( is_array( $this->$arg ) || '' === $this->$arg ) {
				continue;
			}
			$this->$arg = array_map( 'trim', explode( ',', $this->$arg ) );
		}

		// Make sure boolean args are definitely booleans - sometimes filter_var_array doesn't convert them properly
		foreach ( array_filter( $validation, array( $this, 'array_filter_validate_boolean' ) ) as $arg => $val ) {
			$this->$arg = ( $this->$arg === true || $this->$arg === 'true' ) ? true : false;
		}

		// Check args that can be bools return as either a bool or a non-bool string
		foreach ( array( 'filters', 'page_length', 'search_box', 'totals', 'pagination' ) as $arg ) {
			$this->$arg = Posts_Table_Util::maybe_parse_bool( $this->$arg );
		}

		// Validate and parse the columns and headings to use in posts table
		$cols		 = array();
		$headings	 = array();
		$col_search	 = array( 'category', 'post_status' ); // common user errors
		$col_replace = array( 'categories', 'status' );

		foreach ( $this->columns as $raw_column ) {
			$prefix	 = strtok( $raw_column, ':' );
			$col	 = false;

			if ( in_array( $prefix, array( 'cf', 'tax' ) ) ) {
				$suffix = trim( strtok( ':' ) );

				if ( ! $suffix ) {
					continue; // no custom field or taxonomy specified
				} elseif ( 'tax' === $prefix && ! taxonomy_exists( $suffix ) ) {
					continue; // invalid taxonomy
				}

				$col = $prefix . ':' . $suffix;
			} else {
				// Standard column - do search & replace to fix common typos
				$col = str_replace( $col_search, $col_replace, $prefix );
			}

			// Only add column if valid and not added already (duplicate columns not allowed)
			if ( $col && ! in_array( $col, $cols ) ) {
				$cols[]		 = $col;
				$headings[]	 = strtok( '' ); // fetch rest of heading, even if it includes a ':'
			}
		}

		$this->columns	 = $cols;
		$this->headings	 = $headings;

		// Validate widths and priorities
		if ( $this->widths ) {
			$this->widths = Posts_Table_Util::array_pad_and_slice( $this->widths, count( $this->columns ), 'auto' );
		}
		if ( $this->priorities ) {
			$this->priorities = Posts_Table_Util::array_pad_and_slice( $this->priorities, count( $this->columns ), 'default' );
		}

		// Responsive stuff
		if ( ! in_array( $this->responsive_control, array( 'inline', 'column' ) ) ) {
			$this->responsive_control = $defaults['responsive_control'];
		}
		if ( ! in_array( $this->responsive_display, array( 'child_row', 'child_row_visible', 'modal' ) ) ) {
			$this->responsive_display = $defaults['responsive_display'];
		}
		if ( $this->column_breakpoints ) {
			$this->column_breakpoints = Posts_Table_Util::array_pad_and_slice( $this->column_breakpoints, count( $this->columns ), 'default' );
		}

		// Sanitize search filters
		if ( true === $this->filters ) {
			// Work out which filters to display based on table contents
			$this->filters = self::sanitize_search_filters( $this->columns );
		} elseif ( is_string( $this->filters ) ) {
			// Otherwise, use filters specified by user
			$this->filters = self::sanitize_search_filters( explode( ',', $this->filters ) );
		} else {
			$this->filters = false;
		}

		// Display options (page length, etc)
		foreach ( array( 'page_length', 'search_box', 'totals', 'pagination' ) as $display_option ) {
			if ( ! in_array( $this->$display_option, array( 'top', 'bottom', 'both', false ) ) ) {
				$this->$display_option = $defaults[$display_option];
			}
		}

		// Check links arg - used to control whether certain data items are links or plain text
		$this->links = str_replace( $col_search, $col_replace, strtolower( $this->links ) );

		if ( in_array( $this->links, array( 'false', 'none', 'all' ), false ) ) {
			$this->links = (array) $this->links;
		} else {
			$this->links = array_intersect( explode( ', ', $this->links ), array( 'id', 'author', 'terms', 'tags', 'categories', 'title', 'image' ) );

			if ( empty( $this->links ) ) {
				$this->links = (array) $defaults['links'];
			}
		}

		// Paging type
		if ( ! in_array( $this->paging_type, array( 'numbers', 'simple', 'simple_numbers', 'full', 'full_numbers' ) ) ) {
			$this->paging_type = $defaults['paging_type'];
		}

		// Image size
		$this->image_size	 = str_replace( array( ' ', ', ' ), array( '', 'x' ), $this->image_size );
		$size_arr			 = explode( 'x', $this->image_size );
		$size_numeric_count	 = count( array_filter( $size_arr, 'is_numeric' ) );

		if ( 1 === $size_numeric_count ) {
			// One number, so use for both width and height
			$this->image_size = array( $size_arr[0], $size_arr[0] );
		} elseif ( 2 === $size_numeric_count ) {
			// Width and height specified
			$this->image_size = $size_arr;
		} // otherwise assume it's a text-based image size, e.g. 'thumbnail'

		$this->set_image_column_width();

		// Validate date columns
		if ( $this->date_columns && is_array( $this->date_columns ) ) {
			foreach ( $this->date_columns as $key => $column ) {
				// Date column must be present in table, otherwise remove it
				if ( ! in_array( $column, $this->columns ) ) {
					unset( $this->date_columns[$key] );
				}
			}
			$this->date_columns = array_values( $this->date_columns ); // re-key
		} else {
			$this->date_columns = array();
		}

		// Sort by
		$this->sort_by = str_replace( $col_search, $col_replace, $this->sort_by );

		// Sort order - set default if not specified or invalid
		$this->sort_order = strtolower( $this->sort_order );

		if ( ! in_array( $this->sort_order, array( 'asc', 'desc' ) ) ) {
			// Default to descending order for date sorting, ascending for everything else
			$this->sort_order = in_array( $this->sort_by, array_merge( array( 'date', 'modified' ), $this->date_columns ) ) ? 'desc' : 'asc';
		}

		// Search term
		if ( ! Posts_Table_Util::is_valid_search_term( $this->search_term ) ) {
			$this->search_term = '';
		}

		// Filter post limit
		$this->post_limit = apply_filters( 'posts_table_max_posts_limit', $this->post_limit, $this );

		// Content length, exceprt length, rows per page and post limit - can be positive int or -1
		foreach ( array( 'content_length', 'excerpt_length', 'rows_per_page', 'post_limit' ) as $arg ) {
			// Sanity check in case filter set an invalid value
			if ( ! is_int( $this->$arg ) || $this->$arg < -1 ) {
				$this->$arg = $defaults[$arg];
			}
			if ( 0 === $this->$arg ) {
				$this->$arg = -1;
			}
		}

		// Ignore post limit if lazy loading and the default post limit is used.
		if ( $this->lazy_load && $defaults['post_limit'] === $this->post_limit ) {
			$this->post_limit = -1;
		}

		// Validate post type
		$this->post_type = is_array( $this->post_type ) && ( 1 === count( $this->post_type ) ) ? reset( $this->post_type ) : $this->post_type;

		// Attachments have a status of 'inherit' so we need to set status otherwise no results will be returned
		if ( 'attachment' === $this->post_type ) {
			$this->status = array( 'inherit' );
		}

		// Ensure private posts and pages are hidden if the current user doesn't have the required capability.
		//@todo: Apply this to other post types
		if ( in_array( 'private', $this->status ) ) {
			$post_types = (array) $this->post_type;

			if ( ( in_array( 'post', $post_types ) && ! current_user_can( 'read_private_posts' ) ) || ( in_array( 'page', $post_types ) && ! current_user_can( 'read_private_pges' ) ) ) {
				$this->status = array_diff( $this->status, array( 'private' ) );
			}
		}

		// Validate post types, if not using 'any'
		if ( 'any' !== $this->post_type ) {
			$types = array_filter( (array) $this->post_type, 'post_type_exists' );

			if ( empty( $types ) ) {
				$this->post_type = $defaults['post_type'];
			} else {
				$this->post_type = ( 1 === count( $types ) ) ? reset( $types ) : $types;
			}
		}

		// If enabling shortcodes, display the full content
		if ( $this->shortcodes ) {
			$this->content_length = -1;
		}

		// If auto width disabled, must use inline responsive control otherwise control column is always shown
		if ( ! $this->auto_width ) {
			$this->responsive_control = 'inline';
		}

		do_action( 'posts_table_parse_args', $this );
	}

	private function set_image_column_width() {

		if ( false === ( $image_col = array_search( 'image', $this->columns ) ) ) {
			return;
		}

		if ( $this->widths && isset( $this->widths[$image_col] ) && 'auto' !== $this->widths[$image_col] ) {
			return;
		}

		if ( $image_col_width = Posts_Table_Util::get_image_size_width( $this->image_size ) ) {
			if ( ! $this->widths ) {
				$this->widths = array_fill( 0, count( $this->columns ), 'auto' );
			}
			$this->widths[$image_col] = $image_col_width . 'px';
		}
	}

	private function array_filter_validate_boolean( $var ) {
		return $var === FILTER_VALIDATE_BOOLEAN;
	}

	private static function sanitize_search_filters( $filters ) {
		$result = array();

		if ( $filters && is_array( $filters ) ) {
			foreach ( $filters as $filter ) {
				if ( in_array( $filter, array( 'categories', 'tags' ) ) ) {
					$result[] = $filter;
				} elseif ( $tax = Posts_Table_Columns::get_custom_taxonomy( $filter ) ) {
					if ( taxonomy_exists( $tax ) ) {
						$result[] = 'tax:' . $tax;
					}
				}
			}
		}
		return $result ? array_unique( $result ) : false;
	}

}