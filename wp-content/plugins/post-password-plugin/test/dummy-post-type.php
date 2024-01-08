<?php

defined('ABSPATH') || exit;

add_action('init', function() {
	register_post_type('ppt-dummy-post-type', [
		'labels' => [
			'name' => __('Post Password Tokens'),
			'singular_name' => __('Post Password Token'),
		],
		'description' => __('Post Password Token dummy post type'),
		'public' => true,
		'has_archive' => true
	]);

	// !! For testing purposes only !!
	// Don't flush rewrite rules every load
	flush_rewrite_rules();
});
