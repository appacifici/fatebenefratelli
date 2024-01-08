<?php

$capture = tmpfile();
ini_set('error_log', stream_get_meta_data($capture)['uri']);

define('PLUGIN_DIR', dirname(dirname(__FILE__)));

// We need a valid WP install so that we don't have to duplicate internal classes
// This makes running tests in CI harder, but :shrug:
$wpPath = PLUGIN_DIR . "/" . getenv("WORDPRESS_PATH");
if (!is_dir($wpPath)) {
	throw new Exception('WordPress install directory: ' .$wpPath . ' does not exist');
} elseif (!is_file($wpPath . "/wp-settings.php")) {
	throw new Exception('Directory is not a valid WordPress install: ' . $wpPath);
}
define('WORDPRESS_PATH', $wpPath);

// WP Constants
require_once PLUGIN_DIR . "/test/constants.php";

// Load required WP internal classes
$wp_includes = [
	"/class-wp-http-response.php",
	"/rest-api/class-wp-rest-request.php",
	"/rest-api/class-wp-rest-response.php"
];

foreach ($wp_includes as $include) {
	require_once WORDPRESS_PATH . "/wp-includes" . $include;
}

/**
 * Since we can't mock naked functions with PHPUnit,
 * load stubs of those functions to avoid having to
 * load the full, database-connected core functions.
 */
require_once PLUGIN_DIR . "/test/functions.dummy.php";

require_once PLUGIN_DIR . "/vendor/autoload.php";
require_once PLUGIN_DIR . "/test/tests/PPTTestCase.php";