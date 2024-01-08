<?php

namespace PPT\Traits;

defined('ABSPATH') || exit;

use \WP_Post,
	\WP_Screen;


trait PPTMetaBox {
	static string $SIDEBAR_SCRIPT_NAME = 'post-password-token-sidebar';

	/**
	 * Runs at 'init'
	 */
	protected function init_sidebar(): void {
		/**
		 * Gutenberg Setup
		 */
		$bundle = include $this->pluginDir . '/meta-block/build/index.asset.php';

		wp_register_script(
			self::$SIDEBAR_SCRIPT_NAME,
			plugins_url('meta-block/build/index.js', $this->pluginPath),
			$bundle['dependencies'],
			$bundle['version'],
			true
		);

		wp_register_style(
			self::$SIDEBAR_SCRIPT_NAME,
			plugins_url('meta-block/build/index.css', $this->pluginPath),
			[],
			$bundle['version'],
			'all'
		);

		add_action('enqueue_block_editor_assets', [$this, 'sidebar_enqueue_assets']);
		add_action('admin_head', [$this, 'init_legacy_meta_box']);
	}

	/**
	 * Gutenberg Sidebar Meta Box
	 */

	public function sidebar_enqueue_assets(): void {
		wp_enqueue_script(self::$SIDEBAR_SCRIPT_NAME);
		wp_enqueue_style(self::$SIDEBAR_SCRIPT_NAME);
	}

	/**
	 * Legacy Meta Box
	 * 
	 * Runs at 'admin_head' to ensure that we can determine if the edit page
	 * uses the legacy editor or the new Gutenberg editor. The `current_screen`
	 * lookup method isn't available before that.
	 */
	public function init_legacy_meta_box(): void {
		/**
		 * @var WP_Screen $current_screen
		 */
		global $current_screen;

		/**
		 * Check for built in gutenberg
		 */
		if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
			return;
		}

		/**
		 * Is gutenberg loaded via a plugin?
		 */
		if (function_exists('is_gutenberg_page') && call_user_func('is_gutenberg_page') == false) {
			return;
		}

		$options = $this->wpFuncs->get_option();
	
		if (!empty($options['enable'])) {
			$post_types = get_post_types();

			foreach ($options['enable'] as $type) {
				if (in_array($type, $post_types)) {
					add_meta_box(
						'ppt-token', 
						__('Post Password Token', 'post-password-token'), 
						[$this, 'legacy_meta_box'], 
						$type, 
						'normal', 
						'high'
					);
				}
			}
		}
	}

	public function legacy_meta_box(): void {
		/**
		 * @var WP_Post
		 */
		global $post;

		if (empty($post->post_password)) { 
			include $this->pluginDir . '/templates/meta-box-no-password.php';
			return;
		}
		
		if (!in_array($post->post_status, ['publish', 'future']) && !empty($post->post_password)) {
			include $this->pluginDir . '/templates/meta-box-draft.php';
			return;
		}
	
		include $this->pluginDir . '/templates/meta-box.php';
	}
}
