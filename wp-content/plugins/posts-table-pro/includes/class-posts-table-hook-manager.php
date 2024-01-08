<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for managing the actions and filter hooks for an individual posts table.
 *
 * Hooks are registered in a temporary hook environment (@see class WP_Scoped_Hooks), and
 * only apply while the data is loaded into the table.
 *
 * @package   Posts_Table_Pro
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Hook_Manager extends WP_Scoped_Hooks {

	public $args;

	public function __construct( Posts_Table_Args $args ) {
		parent::__construct();
		$this->args = $args;
	}

	public function register() {
		// Excerpt column
		$this->add_filter( 'excerpt_length', array( $this, 'set_excerpt_length' ), 99 ); /* this only applies to excerpts auto-generated from content */
		$this->add_filter( 'excerpt_more', array( __CLASS__, 'more_content_text' ) ); /* as above */
		$this->add_filter( 'the_excerpt', array( $this, 'maybe_strip_shortcodes' ), 5 ); /* before do_shortcode (11) */
		$this->add_filter( 'the_excerpt', 'do_shortcode', 11 ); /* after wpautop (10) */
		$this->add_filter( 'the_excerpt', array( $this, 'maybe_trim_excerpt' ), 15 ); /* after wpautop (10) do_shortcode (11) */

		// Content column
		$this->add_filter( 'the_content', array( $this, 'maybe_strip_shortcodes' ), 5 ); /* before do_shortcode (11) */
		$this->add_filter( 'the_content', array( $this, 'maybe_trim_content' ), 15 ); /* after wpautop (10) do_shortcode (11) */

		// Date column
		$this->add_filter( 'posts_table_data_date', 'Posts_Table_Util::empty_if_false' );

		// Image column
		$this->add_filter( 'wp_get_attachment_image_attributes', array( $this, 'set_featured_image_class' ), 99 );

		// Custom field column
		$this->add_filter( 'posts_table_data_custom_field', array( $this, 'maybe_strip_shortcodes' ), 5 );
		$this->add_filter( 'posts_table_data_custom_field', 'do_shortcode', 6 );

		// Prevent Hero Knowledge Base content filter from running in table as it breaks everything.
		if ( class_exists( 'HT_Knowledge_Base' ) ) {
			$this->add_filter( 'stop_ht_knowledge_base_custom_content', '__return_true' );
		}

		do_action( 'posts_table_hooks_before_register', $this );

		parent::register();

		do_action( 'posts_table_hooks_after_register', $this );
	}

	public static function more_content_text() {
		return apply_filters( 'posts_table_more_content_text', ' &hellip;' );
	}

	public static function maybe_trim_text( $content, $length ) {
		if ( $length > 0 ) {
			$content = wp_trim_words( $content, $length, self::more_content_text() ); // wp_trim_words will also strip tags
		}
		return $content;
	}

	public function maybe_trim_excerpt( $excerpt ) {
		return self::maybe_trim_text( $excerpt, $this->args->excerpt_length );
	}

	public function maybe_trim_content( $content ) {
		return self::maybe_trim_text( $content, $this->args->content_length );
	}

	public function maybe_strip_shortcodes( $content ) {
		// Always strip [posts_table] shortcodes from content - processing a table shortcode within a shortcode could cause an infinite loop.
		$content = preg_replace( sprintf( '/\[(?:%s|%s).*?\]/', Posts_Table_Shortcode::SHORTCODE, 'product_table' ), '', $content );

		if ( ! $this->args->shortcodes && ! apply_filters( 'posts_table_process_shortcodes', false ) ) {
			$content = strip_shortcodes( $content );
		}
		return $content;
	}

	public function set_excerpt_length( $excerpt_length ) {
		if ( is_int( $this->args->excerpt_length ) ) {
			$excerpt_length = $this->args->excerpt_length;
		}
		return $excerpt_length;
	}

	/**
	 * Remove wp-post-image class from featured images shown in table.
	 * Prevents CSS conflicts with other plugins & themes.
	 *
	 * @param array $attr The image attributes
	 * @return array The updated attributes
	 */
	public function set_featured_image_class( $attr ) {
		if ( ! empty( $attr['class'] ) ) {
			$attr['class'] = trim( str_replace( 'wp-post-image', '', $attr['class'] ) );
		}
		return $attr;
	}

}