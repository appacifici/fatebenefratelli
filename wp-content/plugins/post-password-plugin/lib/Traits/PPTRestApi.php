<?php

namespace PPT\Traits;

defined('ABSPATH') || exit;

use PPT\PPTRestApiError;
use PPT\PPTRestApiResponse;
use \WP_Post,
	\WP_REST_Request, 
	\WP_REST_Response,
	\WP_REST_Server;


trait PPTRestApi {
	static int $REST_API_VERSION = 1;

	public function init_rest_api(): void {
		register_rest_route('post-password-token/v' . self::$REST_API_VERSION, '/post/(?P<postId>([0-9])+)/tokens', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'fetch_tokens'],
			'permission_callback' => function() { 
				return current_user_can('edit_posts'); 
			},
			'args' => []
		]);
	}

	/**
	 * Respond to Admin Ajax requests to create tokens for display.
	 * 
	 * Rest API router ensures that `$request['postId']` is present and numeric.
	 * 
	 * @return WP_REST_Response
	 */
	public function fetch_tokens(WP_REST_Request $request): WP_REST_Response {
		$postId = (int) trim($request['postId']);

		/**
		 * @var WP_Post $post
		 */
		$post = $this->wpFuncs->get_post($postId);

		if (!$post) {
			$errorMsg = sprintf(__('post `%d` not found', 'post-password-token'), $postId);
			return new WP_REST_Response(
				new PPTRestAPiError($errorMsg, 404), 
				404
			);
		}

		return new WP_REST_Response(new PPTRestAPIResponse(
			$this->get_ppt_permalink($post, true),
			$this->get_ppt_permalink($post, false)
		), 200);
	}
}