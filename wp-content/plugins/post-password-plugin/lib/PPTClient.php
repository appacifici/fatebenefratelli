<?php

namespace PPT;

defined('ABSPATH') || exit;

use PPT\PPTCore;
use \PasswordHash,
	\WP_Post,
	\WP_Query;


class PPTClient extends PPTCore {

	const COOKIE_PREFIX = 'wp-post-token_';
	const EXCLUDES_CACHE_KEY = 'ppt_wp_list_pages_excludes';

	public function __construct() {
		parent::__construct();
	}

	public function client_init() {
		add_action('wp_loaded', [$this, 'set_protected_post_exclusion']);
		add_action('template_redirect', [$this, 'template_redirect']);
	}

	/**
	 * check the post_password token at template redirect
	 *
	 * @return void
	 */
	public function template_redirect(): void {
		global $wp_query;

		if ((is_single() || is_page()) && 
			isset($wp_query->post->post_password) && 
			isset($_GET['ppt']) &&
			$this->cookie_match($wp_query->post, $_GET['ppt'])) {
				$this->set_cookie($wp_query->post);
		}
	}

	/**
	 * Attempt a token match against a browser
	 * supplied input token.
	 *
	 * @param object $post 
	 * @param string $inputToken 
	 * @return bool
	 */
	public function cookie_match(object $post, string $inputToken): bool {
		$expectedToken = $this->make_token($post);
		
		/**
		 * if we don't already have a PPT cookie, or the current
		 * PPT cookie doesn't match (it could be for a different
		 * post) try the match again.
		 */
		if(!isset($_COOKIE[self::COOKIE_PREFIX . COOKIEHASH]) ||
			!hash_equals((string) $expectedToken, $_COOKIE[self::COOKIE_PREFIX . COOKIEHASH])) {
			return hash_equals((string) $expectedToken, $inputToken);
		}

		return false;
	}

	/**
	 * Set the cookie 
	 * Functionality duplicated from WordPress' post password submit in wp-login.php
	 *
	 * @param object $post 
	 * @return void
	 */
	public function set_cookie(object $post): void {
		global $wp_hasher;

		$token = $this->make_token($post);

		if (empty($wp_hasher)) {
			require_once(ABSPATH . 'wp-includes/class-phpass.php');
			$wp_hasher = new PasswordHash(8, true);
		}
		
		$expire  = apply_filters( 'post_password_expires', time() + 10 * DAY_IN_SECONDS );
		$secure = is_ssl();

		$referer = wp_get_referer();
		if ($referer) {
			$secure = ('https' === parse_url($referer, PHP_URL_SCHEME));
		}

		/**
		 * Set PPT cookie for plugin evaluation
		 */
		setcookie(
			self::COOKIE_PREFIX . COOKIEHASH, 
			$token, 
			$expire, 
			COOKIEPATH, 
			COOKIE_DOMAIN, 
			$secure
		);
		/**
		 * Set a WP post password cookie so that we pass internal 
		 * WP post password checks as well.
		 * 
		 * It would be nice if `wp-postpass_` were defined as a 
		 * constant somewhere in WP, or that this functionality 
		 * were available in a function.
		 */
		setcookie(
			'wp-postpass_' . COOKIEHASH,
			$wp_hasher->HashPassword(wp_unslash($post->post_password)), 
			$expire, 
			COOKIEPATH, 
			COOKIE_DOMAIN, 
			$secure
		);
		
		wp_safe_redirect($this->get_ppt_permalink($post));
		exit;
	}

	/**
	 * Init the post exclusion filter based on site option
	 * 
	 * @TODO: See if this protected posts filtering is easier now in newer WP versions
	 *
	 * @return void
	 */
	public function set_protected_post_exclusion(): void {
		$options = $this->wpFuncs->get_option();

		if ($options['hide_protected'] == 1 && !is_admin()) {
			add_action('parse_request', [$this, 'set_conditional_protected_posts_filter']);
			add_filter('posts_where_paged', [$this, 'exclude_protected_posts_filter']);
			add_filter('wp_list_pages_excludes', [$this, 'wp_list_pages_excludes']);
			add_filter('get_pages', [$this, 'get_pages_filter']);
		}
	}

	/**
	 * VERY general filter to exclude password protected pages from `get_pages`
	 * This feels heavy handed and inneficient, but there's no other applicable 
	 * filters to apply to the `get_pages` function.
	 * 
	 * @param array<WP_Post>
	 * @return array<WP_Post>
	 */
	public function get_pages_filter($pages) {
		return array_filter($pages, function($page) {
			return empty($page->post_password);
		});
	}

	/**
	 * General filter to exclude all password protected posts via the where clause
	 *
	 * @param string $clause 
	 * @return string
	 */
	public function exclude_protected_posts_filter(string $clause): string {
		global $wpdb;
		return $clause . ' AND ' . $wpdb->posts . '.post_password = ""';
	}

	/**
	 * Add to the where clause and only pull protected posts
	 *
	 * @param string $clause 
	 * @return string
	 */
	public function only_protected_posts_filter(string $clause): string {
		global $wpdb;
		return $clause . ' AND '.$wpdb->posts.'.post_password <> ""';
	}

	/**
	 * toggle between the general filter and the WP (main query) specific filter
	 *
	 * @param object $query_obj
	 * @return void
	 */
	public function set_conditional_protected_posts_filter(object $query_obj): void {
		remove_filter('posts_where_paged', [$this, 'exclude_protected_posts_filter'], 10);
		add_filter('posts_where_paged', [$this, 'conditional_exclude_protected_posts_filter']);
	}

	/**
	 * WP specific filter that enables loading of password protected posts on the main
	 * query and on single pages (direct access of those posts) only
	 *
	 * @param string $clause 
	 * @return string
	 */
	public function conditional_exclude_protected_posts_filter(string $clause): string {
		if (!is_singular()) {
			$clause = $this->exclude_protected_posts_filter($clause);
		}

		add_filter('posts_where_paged', [$this, 'exclude_protected_posts_filter']);
		remove_filter('posts_where_paged', [$this, 'conditional_exclude_protected_posts_filter'], 10);
		
		return $clause;
	}

	/**
	 * Get a list of protected posts and append it to a passed in list of 
	 * excluded posts.
	 * 
	 * @TODO: See if this protected posts filtering is easier now in newer WP versions
	 * @TODO: Test this caching with memcached or redis, it probably breaks as we don't
	 *        clear the cache on post-save.
	 *
	 * @param array<int> $excludes 
	 * @return array<int>
	 */
	public function wp_list_pages_excludes(array $excludes): array {
		if ($protected_posts = wp_cache_get(self::EXCLUDES_CACHE_KEY)) {
			return array_unique(array_merge($excludes, $protected_posts));
		}
		
		remove_filter('posts_where_paged', [$this, 'exclude_protected_posts_filter']);
		add_filter('posts_where_paged', [$this, 'only_protected_posts_filter']);

		$query = new WP_Query(array(
			'post_type' => 'page',
			'post_status' => 'publish'
		));

		add_filter('posts_where_paged', [$this, 'exclude_protected_posts_filter']);
		remove_filter('posts_where_paged', [$this, 'only_protected_posts_filter']);
		
		$protected_posts = array();

		if (!empty($query->posts)) {
			foreach ($query->posts as $post) {
				/**
				 * @var WP_Post $post
				 */
				array_push($protected_posts, $post->ID);
			}
		}

		wp_cache_set(self::EXCLUDES_CACHE_KEY, $protected_posts);
		return array_unique(array_merge($excludes, $protected_posts));
	}
}