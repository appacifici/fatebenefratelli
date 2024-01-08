<?php
namespace PPT\Traits;

defined("ABSPATH") || exit;

use PPT\PPTAdmin;
use PPT\PPTCore;


trait PPTInstall {

	/**
	 * @return array<string, mixed>
	 */
	public function get_default_options(): array {
		return [
			'salt' => $this->create_salt(),
			'enable' => [
				'page', 
				'post'
			],
			'hide_protected' => 0,
			'hash_algo' => PPTAdmin::DEFAULT_HASH_ALGO
		];
	}

	public function install(): void {
		$options = $this->wpFuncs->get_option();
		if (!$options) {
			$opts = $this->get_default_options();
			$this->wpFuncs->add_option($opts, '', 'no');
		}
	}

	public function check_upgrade(): void {
		$options = $this->wpFuncs->get_option();

		if ($options === false) {
			/**
			 * Yes, this is a magic, undeletable option,
			 * but we made this mistake long, long ago... 
			 * So we're gonna keep it.
			 */
			$this->install();
		} else if (!is_array($options)) {
			$this->upgrade_plugin_options();
		} else if (!isset($options['hash_algo'])) {
			$this->upgrade_add_hash_algo();
		}
	}

	/**
	 * Upgrade the plugin options from the old-style dual
	 * option format to the new single option, array format
	 */
	public function upgrade_plugin_options(): void {
		$prev_opts = $this->wpFuncs->get_option();

		/**
		 * should never run in to this (<-- sign of a bad programmer)
		 * but I'm just damn paranoid and don't want to nuke a user's private url set
		 * 
		 * Known bug: if upgrading from v1 install, where the option contained
		 * the salt, and the salt is empty, this will generate a salt for you.
		 * 
		 */
		if (is_array($prev_opts) && isset($prev_opts['salt'])) {
			$salt = $prev_opts['salt'];
		}
		else {
			$salt = (empty($prev_opts) ? $this->create_salt() : $prev_opts);
		}
		
		$options = array_merge($this->get_default_options(), [
			'salt' => $salt,
			'hide_protected' => $this->wpFuncs->get_option('ppt_hide_protected', 0),
			'hash_algo' => PPTAdmin::OBSOLETE_HASH_ALGO
		]);
	
		$this->wpFuncs->delete_option('ppt_hide_protected');
		$this->wpFuncs->delete_option(PPTCore::PPT_OPTION);
		$this->wpFuncs->add_option($options, '', 'no');
	}

	/**
	 * Function name is a misnomer - if we already have options, we
	 * preserve the old functionality to avoid obsoleting the old
	 * post tokens, so this doesn't replace md5, it just makes it the
	 * selected hashing algorithm option.
	 */
	public function upgrade_add_hash_algo(): void {
		$prev_opts = $this->wpFuncs->get_option();
		$options = array_merge($prev_opts, [
			'hash_algo' => PPTAdmin::OBSOLETE_HASH_ALGO
		]);
		$this->wpFuncs->update_option($options);
	}
}