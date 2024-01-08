<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   Posts_Table_Pro\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Admin_General {

	public function __construct() {
		// Extra links on Plugins page
		add_filter( 'plugin_action_links_' . PTP_PLUGIN_BASENAME, array( $this, 'plugin_page_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_page_row_meta' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'settings_page_scripts' ) );
	}

	public function plugin_page_action_links( $links ) {
		array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=posts_table' ) . '">' . __( 'Settings', 'posts-table-pro' ) . '</a>' );
		return $links;
	}

	public function plugin_page_row_meta( $links, $file ) {
		if ( PTP_PLUGIN_BASENAME == $file ) {
			$link_fmt	 = '<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>';
			$row_meta	 = array(
				'docs' => sprintf( $link_fmt, esc_url( 'https://barn2.co.uk/kb-categories/posts-table-pro-kb/' ), esc_attr__( 'View Posts Table Pro documentation', 'posts-table-pro' ), esc_html__( 'Docs', 'posts-table-pro' ) )
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	public function settings_page_scripts( $hook ) {
		if ( 'settings_page_posts_table' === $hook ) {
			$suffix = Posts_Table_Util::get_script_suffix();
			wp_enqueue_script( 'ptp-admin', Posts_Table_Util::get_asset_url( "js/admin/posts-table-pro-admin{$suffix}.js" ), array( 'jquery' ), Posts_Table_Pro_Plugin::VERSION, true );
		}
	}

}