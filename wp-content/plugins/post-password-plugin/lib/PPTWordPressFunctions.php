<?php

namespace PPT;

defined('ABSPATH') || exit;

use PPT\PPTAdmin;


/**
 * Internal proxy functions for core WordPress functions
 * to allow for mocking during tests.
 */
class PPTWordPressFunctions {

	/**
	 * get_option
	 * Allows selecting the option name to accommodate the upgrade function
	 * 
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 */
	public function get_option($option = null, $default = false) {
		$option = !is_null($option) ?: PPTAdmin::PPT_OPTION;
		return get_option($option, $default);
	}

	/**
	 * update_option
	 * Default the option to the plugin option name
	 * 
	 * @param mixed $value
	 * @param mixed $autoload
	 * @return bool
	 */
	public function update_option($value, $autoload = null) {
		return update_option(PPTAdmin::PPT_OPTION, $value, $autoload);
	}

	/**
	 * add_option
	 * Default the option to the plugin option name
	 * 
	 * @param mixed $value
	 * @param string $d - Deprecation function parameter
	 * @param mixed $autoload
	 * @return bool
	 */
	public function add_option($value, $d = '', $autoload = null) {
		return add_option(PPTAdmin::PPT_OPTION, $value, $d, $autoload);
	}

	/**
	 * delete_option
	 * Allows selecting the option name to accommodate the upgrade function
	 * 
	 * @param string $option
	 * @return bool
	 */
	public function delete_option(string $option) {
		return delete_option($option);
	}

	/**
	 * get_post
	 * 
	 * @param int $postId
	 * @return mixed
	 */
	public function get_post(int $postId) {
		return get_post($postId);
	}

	/**
	 * @param int $postId
	 * @return string|false
	 */
	public function get_permalink(int $postId) {
		return get_permalink($postId);
	}

	/**
	 * @param int $postId
	 * @return string|false
	 */
	public function wp_get_shortlink(int $postId) {
		return wp_get_shortlink($postId);
	}

	/**
	 * @param string $path
	 * @param string $scheme
	 * @return string
	 */
	public function admin_url(string $path = '', string $scheme = 'admin'): string {
		return admin_url($path, $scheme);
	}

	/**
	 * @param string $redirect_url
	 * @return bool
	 */
	public function wp_redirect(string $redirect_url = '', int $status = 302, string $x_redirect_by = 'WordPress'): bool {
		return wp_redirect($redirect_url, $status, $x_redirect_by);
	}
}
