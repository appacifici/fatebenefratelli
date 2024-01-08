<?php

namespace PPT\Test;

use WP_REST_Request;


class PPTRestApiTest extends PPTTestCase {

	public function testApiFetchTokens() {
		$dummyPost = $this->getDummyPost();
		$this->decorateMockLinkMethods($this->wpFunctionsMocks, $dummyPost);
		$this->wpFunctionsMocks->method('get_option')
			->willReturn($this->getDefaultOptions());
		$this->wpFunctionsMocks->method('get_post')
			->willReturn($dummyPost);

		$request = new WP_Rest_Request('POST', '/wp-json/post-password-token/v1/post/' . $dummyPost->ID . '/tokens');
		$request->set_url_params(['postId' => $dummyPost->ID]);

		$admin = $this->getAdminObj();
		$response = $admin->fetch_tokens($request);

		$this->assertEquals(200, $response->get_status());
		$this->assertObjectHasAttribute('prettyUrl', $response->data);
		$this->assertObjectHasAttribute('shortUrl', $response->data);
		$this->assertStringContainsString('?ppt=', $response->data->prettyUrl);
		$this->assertStringContainsString('&ppt=', $response->data->shortUrl);
	}

	public function testApiFetchTokens404() {
		$this->wpFunctionsMocks->method('get_post')
			->willReturn(null);
		
		$request = new WP_Rest_Request("POST", "/wp-json/post-password-token/v1/post/1/tokens");
		$request->set_url_params(['postId' => 5]);

		$admin = $this->getAdminObj();
		$response = $admin->fetch_tokens($request);

		$this->assertEquals(404, $response->get_status());
		$this->assertObjectHasAttribute('code', $response->data);
		$this->assertObjectHasAttribute('message', $response->data);
		$this->assertEquals(404, $response->data->code);
		$this->assertEquals('post `5` not found', $response->data->message);
	}
}
