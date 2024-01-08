<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class handles our plugin settings page in the admin.
 *
 * @package   Posts_Table_Pro\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Admin_Settings_Page {

	const MENU_SLUG	 = 'posts_table';
	const OPTION_GROUP = 'posts_table_pro';

	private $license;

	public function __construct( $license ) {
		$this->license = $license;

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_settings_page() {
		add_options_page(
			__( 'Posts Table Pro Settings', 'posts-table-pro' ), __( 'Posts Table Pro', 'posts-table-pro' ), 'manage_options', self::MENU_SLUG, array( $this, 'render_settings_page' )
		);
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Posts Table Pro Settings', 'posts-table-pro' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// Output the hidden form fields (_wpnonce, etc)
				settings_fields( self::OPTION_GROUP );

				// Output the sections and their settings
				do_settings_sections( self::MENU_SLUG );
				?>
				<p class="submit">
					<input name="Submit" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'posts-table-pro' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	public function register_settings() {
		// Register the settings
		register_setting( self::OPTION_GROUP, $this->license->license_key_option, array(
			'type'				 => 'string',
			'description'		 => 'Posts Table Pro license key',
			'sanitize_callback'	 => array( $this->license, 'save' )
		) );
		register_setting( self::OPTION_GROUP, Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION, array(
			'type'				 => 'string', // array type not supported, so just use string
			'description'		 => 'Posts Table Pro shortcode defaults',
			'sanitize_callback'	 => 'Posts_Table_Settings::sanitize_shortcode_settings'
		) );
		register_setting( self::OPTION_GROUP, Posts_Table_Settings::MISC_SETTINGS_OPTION, array(
			'type'				 => 'string', // array type not supported, so just use string
			'description'		 => 'Posts Table Pro miscellaneous settings',
			'sanitize_callback'	 => 'Posts_Table_Settings::sanitize_misc_settings'
		) );

		// Licence key
		WP_Settings_API_Helper::add_settings_section(
			'ptp_license_key', self::MENU_SLUG, '', array( $this, 'section_description_license_key' ), array(
			array(
				'id'	 => $this->license->license_key_option,
				'title'	 => __( 'License Key', 'posts-table-pro' ),
				'type'	 => 'text',
				'desc'	 => $this->license->get_license_key_admin_message()
			)
			)
		);

		$default_args = Posts_Table_Args::get_defaults();

		// Selecting posts
		WP_Settings_API_Helper::add_settings_section(
			'ptp_post_selection', self::MENU_SLUG, __( 'Posts selection', 'posts-table-pro' ), array( $this, 'section_description_selecting_posts' ), array(
			array(
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[post_type]',
				'title'		 => __( 'Post type', 'posts-table-pro' ),
				'type'		 => 'select',
				'desc'		 => __( 'The default post type for your tables. This option can also set via the shortcode.', 'posts-table-pro' ) . self::read_more( 'kb/ptp-include-exclude/#post-type' ),
				'options'	 => $this->get_registered_post_types(),
				'default'	 => $default_args['post_type'],
			),
		) );

		// Table content
		WP_Settings_API_Helper::add_settings_section(
			'ptp_shortcode_defaults', self::MENU_SLUG, __( 'Table content', 'posts-table-pro' ), array( $this, 'section_description_table_content' ), array(
			array(
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[columns]',
				'title'		 => __( 'Columns', 'posts-table-pro' ),
				'type'		 => 'text',
				'desc'		 => __( 'Customize the columns used in your table.', 'posts-table-pro' ) . self::read_more( 'kb/posts-table-columns/' ),
				'default'	 => '',
			),
			array(
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[image_size]',
				'title'		 => __( 'Image size', 'posts-table-pro' ),
				'type'		 => 'text',
				'desc'		 => __( "W x H in pixels. Sets the image size when using the 'image' column.", 'posts-table-pro' ) . self::read_more( 'kb/ptp-column-widths/#image-size' ),
				'default'	 => $default_args['image_size'],
			),
			array(
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[lightbox]',
				'title'		 => __( 'Image lightbox', 'posts-table-pro' ),
				'type'		 => 'checkbox',
				'label'		 => __( 'Display featured images in a lightbox when opened', 'posts-table-pro' ),
				'default'	 => $default_args['lightbox'],
			),
			array(
				'title'		 => __( 'Shortcodes', 'posts-table-pro' ),
				'type'		 => 'checkbox',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[shortcodes]',
				'label'		 => __( 'Display shortcodes, HTML and other formatting inside the table content', 'posts-table-pro' ),
				'default'	 => $default_args['shortcodes']
			),
			array(
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[content_length]',
				'title'				 => __( 'Content length', 'posts-table-pro' ),
				'type'				 => 'number',
				'class'				 => 'small-text',
				'suffix'			 => __( 'words', 'posts-table-pro' ),
				'desc'				 => __( 'Enter -1 to show the full post content.', 'posts-table-pro' ),
				'default'			 => $default_args['content_length'],
				'custom_attributes'	 => array(
					'min' => -1
				)
			),
			array(
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[excerpt_length]',
				'title'				 => __( 'Excerpt length', 'posts-table-pro' ),
				'type'				 => 'number',
				'class'				 => 'small-text',
				'suffix'			 => __( 'words', 'posts-table-pro' ),
				'desc'				 => __( 'Enter -1 to show the full excerpt.', 'posts-table-pro' ),
				'default'			 => $default_args['excerpt_length'],
				'custom_attributes'	 => array(
					'min' => -1
				)
			),
			array(
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[links]',
				'title'		 => __( 'Links', 'posts-table-pro' ),
				'type'		 => 'text',
				'desc'		 => __( 'Include links to the relevant post, category, tag, or term.', 'posts-table-pro' ) . self::read_more( 'kb/links-posts-table/' ),
				'default'	 => $default_args['links'],
			)
		) );

		// Loading posts
		WP_Settings_API_Helper::add_settings_section(
			'ptp_post_loading', self::MENU_SLUG, __( 'Table loading', 'posts-table-pro' ), '__return_false', array(
			array(
				'title'				 => __( 'Lazy load', 'posts-table-pro' ),
				'type'				 => 'checkbox',
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[lazy_load]',
				'label'				 => __( 'Load the posts one page at a time', 'posts-table-pro' ),
				'desc'				 =>
				sprintf( __( 'Enable this if you have many posts or experience slow page load times. %sWarning:%s Lazy load limits the searching and sorting features in the table. Only use it if you definitely need it.', 'posts-table-pro' ), '<br/><strong>', '</strong>' )
				. self::read_more( 'kb/posts-table-lazy-load/' ),
				'default'			 => $default_args['lazy_load'],
				'class'				 => 'toggle-parent',
				'custom_attributes'	 => array(
					'data-child-class'	 => 'post-limit',
					'data-toggle-val'	 => 0
				)
			),
			array(
				'title'				 => __( 'Post limit', 'posts-table-pro' ),
				'type'				 => 'number',
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[post_limit]',
				'desc'				 => __( 'The maximum (total) number of posts to display in one table.', 'posts-table-pro' ),
				'default'			 => $default_args['post_limit'],
				'class'				 => 'small-text post-limit',
				'custom_attributes'	 => array(
					'min' => -1
				)
			),
			array(
				'title'				 => __( 'Posts per page', 'posts-table-pro' ),
				'type'				 => 'number',
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[rows_per_page]',
				'desc'				 => __( 'The number of posts per page of results. Enter -1 to display all posts on one page.', 'posts-table-pro' ),
				'default'			 => $default_args['rows_per_page'],
				'custom_attributes'	 => array(
					'min' => -1
				)
			),
			array(
				'title'				 => __( 'Caching', 'posts-table-pro' ),
				'type'				 => 'checkbox',
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[cache]',
				'label'				 => __( 'Cache table contents to improve load time', 'posts-table-pro' ),
				'default'			 => $default_args['cache'],
				'class'				 => 'toggle-parent',
				'custom_attributes'	 => array(
					'data-child-class' => 'expires-after'
				)
			),
			array(
				'title'				 => __( 'Cache expires after', 'posts-table-pro' ),
				'type'				 => 'number',
				'id'				 => Posts_Table_Settings::MISC_SETTINGS_OPTION . '[cache_expiry]',
				'suffix'			 => __( 'hours', 'posts-table-pro' ),
				'desc'				 => __( 'Your table data will be refreshed after this length of time.', 'posts-table-pro' ),
				'default'			 => 6,
				'class'				 => 'expires-after',
				'custom_attributes'	 => array(
					'min'	 => 1,
					'max'	 => 9999
				)
			)
		) );

		// Sorting
		WP_Settings_API_Helper::add_settings_section(
			'ptp_sorting', self::MENU_SLUG, __( 'Sorting', 'posts-table-pro' ), '__return_false', array(
			array(
				'title'				 => __( 'Sort by', 'posts-table-pro' ),
				'type'				 => 'select',
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[sort_by]',
				'options'			 => array(
					'title'			 => __( 'Title', 'posts-table-pro' ),
					'id'			 => __( 'ID', 'posts-table-pro' ),
					'date'			 => __( 'Date published', 'posts-table-pro' ),
					'modified'		 => __( 'Date modified', 'posts-table-pro' ),
					'menu_order'	 => __( 'Page order (menu order)', 'posts-table-pro' ),
					'name'			 => __( "Post 'slug'", 'posts-table-pro' ),
					'author'		 => __( 'Author', 'posts-table-pro' ),
					'comment_count'	 => __( 'Comment count', 'posts-table-pro' ),
					'rand'			 => __( 'Random', 'posts-table-pro' ),
					'custom'		 => __( 'Custom', 'posts-table-pro' )
				),
				'desc'				 => __( 'The initial sort order applied to the table.', 'posts-table-pro' ) . self::read_more( 'kb/posts-table-sort-options/' ),
				'default'			 => $default_args['sort_by'],
				'class'				 => 'toggle-parent',
				'custom_attributes'	 => array(
					'data-child-class'	 => 'custom-sort',
					'data-toggle-val'	 => 'custom'
				)
			),
			array(
				'title'	 => __( 'Sort column', 'posts-table-pro' ),
				'type'	 => 'text',
				'id'	 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[sort_by_custom]',
				'class'	 => 'regular-text custom-sort',
				'desc'	 => __( 'Enter any column in your table. Will only work when lazy load is disabled.', 'posts-table-pro' )
			),
			array(
				'title'		 => __( 'Sort direction', 'posts-table-pro' ),
				'type'		 => 'select',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[sort_order]',
				'options'	 => array(
					''		 => __( 'Automatic', 'posts-table-pro' ),
					'asc'	 => __( 'Ascending (A to Z, 1 to 99)', 'posts-table-pro' ),
					'desc'	 => __( 'Descending (Z to A, 99 to 1)', 'posts-table-pro' )
				),
				'default'	 => $default_args['sort_order']
			)
		) );

		// Table controls
		WP_Settings_API_Helper::add_settings_section(
			'ptp_table_controls', self::MENU_SLUG, __( 'Table controls', 'posts-table-pro' ), '__return_false', array(
			array(
				'title'				 => __( 'Search filters', 'posts-table-pro' ),
				'type'				 => 'select',
				'id'				 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[filters]',
				'options'			 => array(
					'false'	 => __( 'Disabled', 'posts-table-pro' ),
					'true'	 => __( 'Show based on columns in table', 'posts-table-pro' ),
					'custom' => __( 'Custom', 'posts-table-pro' )
				),
				'desc'				 => __( 'Dropdown lists to filter the table by category, tag, attribute, or custom taxonomy.', 'posts-table-pro' ) . self::read_more( 'kb/posts-table-filters/' ),
				'default'			 => $default_args['filters'],
				'class'				 => 'toggle-parent',
				'custom_attributes'	 => array(
					'data-child-class'	 => 'custom-search-filter',
					'data-toggle-val'	 => 'custom'
				)
			),
			array(
				'title'	 => __( 'Custom filters', 'posts-table-pro' ),
				'type'	 => 'text',
				'id'	 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[filters_custom]',
				'desc'	 => __( 'Enter the filters as a comma-separated list.', 'posts-table-pro' ),
				'class'	 => 'regular-text custom-search-filter'
			),
			array(
				'title'		 => __( 'Page length', 'posts-table-pro' ),
				'type'		 => 'select',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[page_length]',
				'options'	 => array(
					'top'	 => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'	 => __( 'Above and below table', 'posts-table-pro' ),
					'false'	 => __( 'Hidden', 'posts-table-pro' )
				),
				'desc'		 => __( "The position of the 'Show [x] entries' dropdown list.", 'posts-table-pro' ),
				'default'	 => $default_args['page_length']
			),
			array(
				'title'		 => __( 'Search box', 'posts-table-pro' ),
				'type'		 => 'select',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[search_box]',
				'options'	 => array(
					'top'	 => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'	 => __( 'Above and below table', 'posts-table-pro' ),
					'false'	 => __( 'Hidden', 'posts-table-pro' )
				),
				'default'	 => $default_args['search_box']
			),
			array(
				'title'		 => __( 'Totals', 'posts-table-pro' ),
				'type'		 => 'select',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[totals]',
				'options'	 => array(
					'top'	 => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'	 => __( 'Above and below table', 'posts-table-pro' ),
					'false'	 => __( 'Hidden', 'posts-table-pro' )
				),
				'desc'		 => __( "The position of the post totals, e.g. 'Showing 1 to 5 of 10 entries'.", 'posts-table-pro' ),
				'default'	 => $default_args['totals']
			),
			array(
				'title'		 => __( 'Pagination buttons', 'posts-table-pro' ),
				'type'		 => 'select',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[pagination]',
				'options'	 => array(
					'top'	 => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'	 => __( 'Above and below table', 'posts-table-pro' ),
					'false'	 => __( 'Hidden', 'posts-table-pro' )
				),
				'desc'		 => __( 'The position of the paging buttons which scroll between results.', 'posts-table-pro' ),
				'default'	 => $default_args['pagination']
			),
			array(
				'title'		 => __( 'Pagination type', 'posts-table-pro' ),
				'type'		 => 'select',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[paging_type]',
				'options'	 => array(
					'numbers'		 => __( 'Numbers only', 'posts-table-pro' ),
					'simple'		 => __( 'Prev|Next', 'posts-table-pro' ),
					'simple_numbers' => __( 'Prev|Next + Numbers', 'posts-table-pro' ),
					'full'			 => __( 'Prev|Next|First|Last', 'posts-table-pro' ),
					'full_numbers'	 => __( 'Prev|Next|First|Last + Numbers', 'posts-table-pro' )
				),
				'default'	 => $default_args['paging_type']
			),
			array(
				'title'		 => __( 'Reset button', 'posts-table-pro' ),
				'type'		 => 'checkbox',
				'id'		 => Posts_Table_Settings::SHORTCODE_DEFAULTS_OPTION . '[reset_button]',
				'label'		 => __( 'Show the reset button above the table', 'posts-table-pro' ),
				'default'	 => $default_args['reset_button']
			)
		) );
	}

	// SECTION DESCRIPTIONS

	public function section_description_license_key() {
		$this->settings_page_support_links();

		if ( filter_input( INPUT_GET, 'license_debug', FILTER_VALIDATE_BOOLEAN ) ) {
			echo '<input type="hidden" name="license_debug" value="1" />';
		}
		if ( $override = filter_input( INPUT_GET, 'license_override', FILTER_SANITIZE_STRING ) ) {
			echo '<input type="hidden" name="license_override" value="' . esc_attr( $override ) . '" />';
		}
	}

	public function section_description_selecting_posts() {
		?>
		<p><?php
			printf(
				__( 'By default, your tables display all published posts for the selected post type. To restrict the posts by category, tag, taxonomy, date, custom field, etc. you need to add the relevant option to your shortcode. Please see the %sKnowledge Base%s for details.', 'posts-table-pro' ), self::barn2_link_open( 'kb/ptp-include-exclude/' ), '</a>'
			);
			?>
		</p>
		<?php
	}

	public function section_description_table_content() {
		?>
		<p><?php
			printf(
				__( 'The following options set defaults for the [posts_table] shortcode. You can override these by setting the relevant option in the shortcode. See the %sfull list of shortcode options%s for details.', 'posts-table-pro' ), self::barn2_link_open( 'kb/posts-table-options/' ), '</a>'
			);
			?>
		</p>
		<?php
	}

	// OTHER

	public function settings_page_support_links() {
		?>
		<p>
			<?php
			echo self::barn2_link( 'kb-categories/ptp-getting-started/', __( 'Getting Started', 'posts-table-pro' ) );
			echo ' | ';
			echo self::barn2_link( 'kb-categories/posts-table-pro-kb/', __( 'Knowledge Base', 'posts-table-pro' ) );
			?>
		</p>
		<?php
	}

	private function get_registered_post_types() {
		$post_types = get_post_types();

		// Internal WP post types.
		$internal_post_types = array(
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset',
			'oembed_cache'
		);

		// CPTs added by plugins which are not relevant (e.g. internal CPT) or not supported.
		$plugin_post_types_not_supported = array(
			'acf-field',
			'acf-field-group',
			'nf_sub',
			'edd_log',
			'edd_payment',
			'edd_discount',
			'product_variation',
			'shop_order_refund',
			'shop_coupon',
			'tribe-ea-record',
			'deleted_event'
		);

		foreach ( (array) apply_filters( 'posts_table_settings_page_post_types', array_merge( $internal_post_types, $plugin_post_types_not_supported ) ) as $type ) {
			unset( $post_types[$type] );
		}

		return $post_types;
	}

	private static function barn2_url( $path ) {
		return esc_url( 'https://barn2.co.uk/' . ltrim( $path, '/' ) );
	}

	private static function barn2_link_open( $path ) {
		return sprintf( '<a href="%s" target="_blank">', self::barn2_url( $path ) );
	}

	private static function barn2_link( $path, $link_text ) {
		return sprintf( '<a href="%s" target="_blank">%s</a>', self::barn2_url( $path ), $link_text );
	}

	private static function read_more( $path ) {
		return sprintf( ' %s', self::barn2_link( $path, __( 'Read more', 'posts-table-pro' ) ) );
	}

}