<?php

/**
 * Define a few of the internal WP functions that we need.
 * 
 * Since these are intermingled with function defs that require
 * db access and the like, it is easier just to overload the 
 * funcs we need in here.
 */


function apply_filters($hook_name, $value) {
	return $value;
}

function add_query_arg($arg, $value, $url) {
	$mod = '?';
	if (strpos($url, '?') !== false) {
		$mod = '&';
	} 
	return $url . $mod . $arg . '=' . $value;
}

function absint($int) {
	return abs($int);
}

function __($string, $domain = 'default') {
	return $string;
}

function check_admin_referer($action = -1, $query_arg = '_wpnonce') {
	return true;
}

function get_post_types($args = [], $output = 'names', $operator = 'and') {
	if ($output == 'names') {
		return [
			'post',
			'page',
			'foo'
		];
	} else {
		throw new Exception('unexpected request for post-type objects');
	}
}