<?php

namespace PPT;

defined('ABSPATH') || exit;

use PPT\PPTWordPressFunctions;
use PPT\PPTToken;


/**
 * @todo - deprecate extension of PPTWordPressFunctions after full DI conversion
 */
abstract class PPTCore {

	protected PPTWordPressFunctions $wpFuncs;

	/**
	 * @var array<string, PPTToken> $TOKEN_CACHE
	 */
	static array $TOKEN_CACHE;

	const PPT_OPTION = 'ppt-token-options';

	public function __construct() {
		static::$TOKEN_CACHE = [];
		$this->setWPFuncs(new PPTWordPressFunctions);
	}

	/**
	 * DI Setter
	 */
	public function setWPFuncs(PPTWordPressFunctions $wpFuncs): void {
		$this->wpFuncs = $wpFuncs;
	}

	/**
	 * Build our custom permalink with token
	 *
	 * @param object $post
	 * @param bool $force_short
	 * @return string
	 */
	public function get_ppt_permalink(object $post, bool $force_short = false): string {
		$url = $force_short ? $this->wpFuncs->wp_get_shortlink($post->ID) : $this->wpFuncs->get_permalink($post->ID);
		return add_query_arg('ppt', (string) $this->make_token($post), $url);
	}

	/**
	 * Make an access hash
	 * Currently as simple as hashing $salt + $post_name + $post_password
	 *
	 * @param object $post 
	 * @return PPTToken
	 */
	public function make_token(object $post): PPTToken {
		$cacheKey = $post->ID . "--" .$post->post_password;
		if (isset(static::$TOKEN_CACHE[$cacheKey])) {
			return static::$TOKEN_CACHE[$cacheKey];
		}

		$options = $this->wpFuncs->get_option();
		$token = new PPTToken($options['hash_algo'], $options['salt'], $post->post_name . $post->post_password);

		static::$TOKEN_CACHE[$cacheKey] = $token;
		return $token;
	}

	/**
	 * @param string $salt The salt value to save
	 * @return void
	 */
	public function save_salt(string $salt): void {
		$options = $this->wpFuncs->get_option();
		$options['salt'] = $salt;
		$this->wpFuncs->update_option($options);
	}

	/**
	 * Create a default salt
	 *
	 * @return string
	 */
	public function create_salt(): string {
		return substr(base64_encode(random_bytes(32)), 0, 32);
	}
}