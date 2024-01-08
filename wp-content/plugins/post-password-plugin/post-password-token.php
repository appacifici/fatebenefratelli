<?php
/**
 * Plugin Name:       Post Password Token
 * Plugin URI:        http://top-frog.com/projects/post-password-token/
 * Description:       Allow tokens to be supplied in the URL to negate the post_password requirement. Mimics the Guest Pass functionality on Flickr. <a href="options-general.php?page=post-password-token">Configure plugin options</a>
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Version:           2.0.3
 * Author:            shawnparker, gordonbrander
 * Author URI:        http://top-frog.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-password-token
 *
 * @package           create-block
 */

// Copyright (c) 2009-2022 Shawn Parker, Gordon Brander. All rights reserved.

define('PPT_VER', '2.0.3');

require_once "vendor/autoload.php";

use PPT\PPTAdmin;
use PPT\PPTClient;

defined('ABSPATH') || exit;

$ppt_admin = new PPTAdmin;
$ppt_client = new PPTClient;

add_action('plugins_loaded', [$ppt_admin, 'admin_init']);
add_action('plugins_loaded', [$ppt_client, 'client_init']);

register_activation_hook(__FILE__, [$ppt_admin, 'install']);
