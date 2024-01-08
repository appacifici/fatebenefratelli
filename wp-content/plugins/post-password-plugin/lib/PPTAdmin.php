<?php

namespace PPT;

defined('ABSPATH') || exit;

use PPT\PPTCore;
use PPT\Traits\PPTInstall;
use PPT\Traits\PPTMetaBox;
use PPT\Traits\PPTRestApi;


class PPTAdmin extends PPTCore {
	use PPTInstall;
	use PPTMetaBox;
	use PPTRestApi;

	const ADMIN_PAGE_SLUG = 'post-password-token';
	const ADMIN_PAGE_HOOK  = 'settings_page_post-password-token';
	const DEFAULT_HASH_ALGO = 'sha256';
	const OBSOLETE_HASH_ALGO = 'md5';

	/**
	 * @var array<string>
	 */
	private array $hashAglos = [];

	private string $pluginDir;
	private string $pluginPath;

	public function __construct() {
		$this->pluginDir = dirname(dirname(__FILE__));
		$this->pluginPath = $this->pluginDir . '/post-password-token.php';
		$this->hashAglos = [
			self::DEFAULT_HASH_ALGO,
			self::OBSOLETE_HASH_ALGO,
		];
		parent::__construct();
	}

	/**
	 * Runs at 'init'
	 * 
	 * @return void
	 */
	public function admin_init(): void {
		$this->check_upgrade();
		$this->init_sidebar();

		add_action('admin_menu', [$this, 'admin_menu_item']);
		add_action('admin_init', [$this, 'load_textdomain']);
		add_action('rest_api_init', [$this, 'init_rest_api']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
	}

	/**
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain('post-password-token', false, basename($this->pluginDir) . '/languages');
	}

	/**
	 * @return void
	 */
	public function admin_menu_item(): void {
		$hookname = add_submenu_page(
			'options-general.php', 
			__('Post Password Token', 'post-password-token'), 
			__('Post Password Token', 'post-password-token'), 
			'manage_options', 
			self::ADMIN_PAGE_SLUG, 
			[$this, 'admin_page']
		);
		add_action('load-' . $hookname, [$this, 'admin_process_form']);
	}

	/**
	 * @param string $hook The name of the hook being called
	 * @return void
	 */
	public function admin_enqueue_scripts(string $hook): void {
		if ($hook == self::ADMIN_PAGE_HOOK) {
			wp_enqueue_script('ppt-admin', plugins_url('js/ppt-admin.js', $this->pluginPath), [], PPT_VER);
			wp_enqueue_style('ppt-admin', plugins_url('css/ppt-admin.css', $this->pluginPath), [], PPT_VER, 'screen');
		}
	}

	/**
	 * Display this plugin's admin page
	 * 
	 * @return void
	 */
	public function admin_page(): void {		
		include $this->pluginDir . '/templates/settings.php';
	}

	/**
	 * Because 2 of the plugin's settings are destructive, 
	 * the settings page contains distinct forms for:
	 * - general settings
	 * - salt settings (destructive)
	 * - hashing settings (destructive)
	 * 
	 * This helps prevent mistakes in form processing and
	 * not accidentally force new hash tokens on the site.
	 * 
	 * @return void
	 */
	public function admin_process_form(): void {
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			if (isset($_POST['ppt-save-salt'])) {
				check_admin_referer('ppt_update_salt');
				$this->admin_save_salt();
			} elseif (isset($_POST['ppt-save-algorithm'])) {
				check_admin_referer('ppt_update_algo');
				$this->admin_save_algo();
			} elseif (isset($_POST['ppt-save-options'])) {
				check_admin_referer('ppt_update_settings');
				$this->admin_save_options();
			}
		}
	}

	/**
	 * Save general settings for the plugin.
	 * 
	 * @return void
	 */
	public function admin_save_options(): void {
		$options = $this->wpFuncs->get_option();
			
		if (isset($_POST['ppt_hide_protected'])) {
			$options['hide_protected'] = intval($_POST['ppt_hide_protected']);
		} else {
			$options['hide_protected'] = 0;
		}
		
		if (!empty($_POST['ppt_enable'])) {
			$enable = [];
			$post_types = get_post_types([], 'names');
	
			foreach($_POST['ppt_enable'] as $type => $active) {
				if (in_array($type, $post_types)) {
					$enable[] = $type;
				}
			}
	
			$options['enable'] = $enable;
		} else {
			$options['enable'] = [];
		}
		
		$this->wpFuncs->update_option($options);
		$this->redirect(["options-success" => 1]);
	}

	/**
	 * Update the hashing algorithm used. Since hashes are calculated at run-time,
	 * this will force new tokens to be generated for every post.
	 * 
	 * @return void
	 */
	protected function admin_save_algo(): void {
		$options = $this->wpFuncs->get_option();

		if (!empty($_POST['ppt_algo']) && in_array($_POST['ppt_algo'], $this->hashAglos)) {
			$options['hash_algo'] = $_POST['ppt_algo'];
		}

		$this->wpFuncs->update_option($options);
		$this->redirect(["algo-success" => 1]);
	}

	/**
   	 * Update the hashing algorithm used. Since hashes are calculated at run-time,
	 * this will force new tokens to be generated for every post.
	 * 
	 * @return void
	 */
	protected function admin_save_salt(): void {
		if (!empty($_POST['ppt_salt'])) {
			$salt = strval($_POST['ppt_salt']);
			$options['salt'] = stripslashes($salt);
		}

		$this->save_salt($salt);
		$this->redirect(["salt-success" => 1]);
	}

	/**
	 * @param array<string,mixed> $params
	 */
	protected function redirect(array $params = []): void {
		$url = $this->wpFuncs->admin_url(
			'options-general.php?page=' . PPTAdmin::ADMIN_PAGE_SLUG
		);

		if (!empty($params)) {
			foreach ($params as $param => $value) {
				$url .= sprintf("&%s=%s", $param, $value);
			}
		}

  		$this->wpFuncs->wp_redirect($url);
	}
}
