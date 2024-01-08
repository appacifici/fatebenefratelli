<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for managing the columns for a specific Posts Table, and column utility functions.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Columns {

	/**
	 * @var Posts_Table_Args The table args.
	 */
	private $args;
	/**
	 * @var array Global column defaults.
	 */
	private static $column_defaults = null;

	public function __construct( Posts_Table_Args $args ) {
		$this->args = $args;
	}

	public function get_all_columns() {
		return array_merge( $this->get_columns(), $this->get_hidden_columns() );
	}

	public function get_columns() {
		return $this->args->columns;
	}

	public function get_hidden_columns() {
		$hidden = array();

		if ( $this->args->filters ) {
			$hidden = preg_replace( '/$/', '_hfilter', array_map( array( __CLASS__, 'unprefix_column' ), $this->args->filters ) );
		}

		return $hidden;
	}

	public function column_index( $column, $incude_hidden = false ) {
		$cols	 = $incude_hidden ? $this->get_all_columns() : $this->get_columns();
		$index	 = array_search( $column, $cols );
		$index	 = is_int( $index ) ? $index : false; // sanity check

		if ( false !== $index ) {
			if ( 'column' === $this->args->responsive_control ) {
				$index ++;
			}
		}
		return $index;
	}

	public function get_column_heading( $index, $column ) {
		$heading		 = '';
		$standard_cols	 = self::column_defaults();
		$unprefixed_col	 = self::unprefix_column( $column );

		if ( isset( $standard_cols[$column]['heading'] ) ) {
			$heading = $standard_cols[$column]['heading'];
		} elseif ( $tax = self::get_custom_taxonomy( $column ) ) {
			// If custom taxonomy, get the main taxonomy label
			if ( $tax_obj = get_taxonomy( $tax ) ) {
				$heading = $tax_obj->label;
			}
		} else {
			$heading = trim( ucwords( str_replace( array( '_', '-' ), ' ', $unprefixed_col ) ) );
		}

		$heading = apply_filters( 'posts_table_column_heading_' . $unprefixed_col, $heading );

		if ( is_int( $index ) && ! empty( $this->args->headings[$index] ) ) {
			$heading = 'blank' === $this->args->headings[$index] ? '' : $this->args->headings[$index];
		}
		return $heading;
	}

	public function get_column_heading_class( $index, $column ) {
		$class = array( self::get_column_class( $column ) );

		if ( 0 === $index && 'inline' === $this->args->responsive_control ) {
			$class[] = 'all';
		} elseif ( is_int( $index ) && isset( $this->args->column_breakpoints[$index] ) && 'default' !== $this->args->column_breakpoints[$index] ) {
			$class[] = $this->args->column_breakpoints[$index];
		}
		return implode( ' ', apply_filters( 'posts_table_column_class_' . self::unprefix_column( $column ), $class ) );
	}

	public function get_column_priority( $index, $column ) {
		$standard_cols	 = self::column_defaults();
		$priority		 = isset( $standard_cols[$column]['priority'] ) ? $standard_cols[$column]['priority'] : '';
		$priority		 = apply_filters( 'posts_table_column_priority_' . self::unprefix_column( $column ), $priority );

		if ( is_int( $index ) && isset( $this->args->priorities[$index] ) ) {
			$priority = $this->args->priorities[$index];
		}
		return $priority;
	}

	public function get_column_width( $index, $column ) {
		$width = apply_filters( 'posts_table_column_width_' . self::unprefix_column( $column ), '' );

		if ( is_int( $index ) && isset( $this->args->widths[$index] ) ) {
			$width = $this->args->widths[$index];
		}
		if ( 'auto' === $width ) {
			$width = '';
		} elseif ( is_numeric( $width ) ) {
			$width = $width . '%';
		}
		return $width;
	}

	public function is_click_filterable( $column ) {
		return in_array( $column, array( 'categories', 'tags', 'author' ) ) || self::is_custom_taxonomy( $column );
	}

	public function is_searchable( $column ) {
		$searchable = true;

		if ( 'image' === $column ) {
			$searchable = false;
		}

		// Only allow filtering if column is searchable.
		if ( $searchable ) {
			$searchable	 = apply_filters( 'posts_table_column_searchable', $searchable, self::unprefix_column( $column ) );
			$searchable	 = apply_filters( 'posts_table_column_searchable_' . self::unprefix_column( $column ), $searchable );
		}

		return $searchable;
	}

	public function is_sortable( $column ) {
		$sortable = false;

		if ( ! $this->args->lazy_load && ! in_array( $column, array( 'image' ) ) ) {
			$sortable = true;
		}
		if ( $this->args->lazy_load && in_array( $column, array( 'id', 'title', 'date', 'author' ) ) ) {
			$sortable = true;
		}

		// Only allow filtering if column is sortable.
		if ( $sortable ) {
			$sortable	 = apply_filters( 'posts_table_column_sortable', $sortable, self::unprefix_column( $column ) );
			$sortable	 = apply_filters( 'posts_table_column_sortable_' . self::unprefix_column( $column ), $sortable );
		}

		return $sortable;
	}

	public static function column_defaults() {
		// Lazy load column defaults but only do it once
		if ( ! self::$column_defaults ) {
			// Priority values are used to determine visiblity at small screen sizes (1 = highest priority).
			self::$column_defaults = apply_filters( 'posts_table_column_defaults', array(
				'id'		 => array( 'heading' => __( 'ID', 'posts-table-pro' ), 'priority' => 3 ),
				'title'		 => array( 'heading' => __( 'Title', 'posts-table-pro' ), 'priority' => 1 ),
				'content'	 => array( 'heading' => __( 'Content', 'posts-table-pro' ), 'priority' => 7 ),
				'excerpt'	 => array( 'heading' => __( 'Summary', 'posts-table-pro' ), 'priority' => 4 ),
				'date'		 => array( 'heading' => __( 'Date', 'posts-table-pro' ), 'priority' => 5 ),
				'author'	 => array( 'heading' => __( 'Author', 'posts-table-pro' ), 'priority' => 8 ),
				'categories' => array( 'heading' => __( 'Categories', 'posts-table-pro' ), 'priority' => 6 ),
				'tags'		 => array( 'heading' => __( 'Tags', 'posts-table-pro' ), 'priority' => 9 ),
				'status'	 => array( 'heading' => __( 'Status', 'posts-table-pro' ), 'priority' => 10 ),
				'image'		 => array( 'heading' => __( 'Image', 'posts-table-pro' ), 'priority' => 2 ),
				) );
		}
		return self::$column_defaults;
	}

	public static function is_custom_field( $column ) {
		return $column && 'cf:' === substr( $column, 0, 3 );
	}

	public static function get_custom_field( $column ) {
		if ( self::is_custom_field( $column ) ) {
			return substr( $column, 3 );
		}
		return false;
	}

	public static function is_custom_taxonomy( $column ) {
		return $column && 'tax:' === substr( $column, 0, 4 );
	}

	public static function get_custom_taxonomy( $column ) {
		if ( self::is_custom_taxonomy( $column ) ) {
			return substr( $column, 4 );
		}
		return false;
	}

	public static function is_filter_column( $column ) {
		return $column && '_hfilter' === substr( $column, -8 );
	}

	public static function get_filter_column( $column ) {
		if ( self::is_filter_column( $column ) ) {
			return substr( $column, 0, -8 );
		}
		return false;
	}

	public static function get_column_class( $column ) {
		return Posts_Table_Util::sanitize_class_name( 'col-' . self::unprefix_column( $column ) );
	}

	public static function get_column_data_source( $column ) {
		// '.' not allowed in data source
		return str_replace( '.', '', $column );
	}

	public static function get_column_name( $column ) {
		return self::unprefix_column( $column );
	}

	public static function get_column_taxonomy( $column ) {
		if ( $hidden = self::is_filter_column( $column ) ) {
			$column = self::get_filter_column( $column );
		}

		$tax = $column;

		if ( 'categories' === $column ) {
			$tax = 'category';
		} elseif ( 'tags' === $column ) {
			$tax = 'post_tag';
		} elseif ( self::is_custom_taxonomy( $column ) ) {
			$tax = self::get_custom_taxonomy( $column );
		}
		if ( taxonomy_exists( $tax ) ) {
			return $tax;
		}
		return false;
	}

	public static function unprefix_column( $column ) {
		if ( false !== ( $str = strstr( $column, ':' ) ) ) {
			$column = substr( $str, 1 );
		}
		return $column;
	}

}