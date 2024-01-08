<?php

namespace PPT\Test;

use \PHPUnit\Framework\TestCase;
use PPT\PPTAdmin;
use PPT\PPTWordPressFunctions;


abstract class PPTTestCase extends TestCase {
	public $wpFunctionsMocks;

	public function setUp(): void {		
		 $this->wpFunctionsMocks = $this->createMock(PPTWordPressFunctions::class);
	}

	function getDefaultOptions(): array {
		return [
			'salt' => 'dummy-salt',
			'enable' => [
				'page', 
				'post'
			],
			'hide_protected' => 0,
			'hash_algo' => 'sha256'
		];
	}

	function getAdminObj() {
		$admin = new PPTAdmin;
		$admin->setWPFuncs($this->wpFunctionsMocks);

		return $admin;
	}

	function getAdminMock() {
		return $this->getMockBuilder(PPTAdmin::class);
	}

	function getDummyPost($params = []) {
		return (object) array_merge([
			'ID' => 1,
			'post_name' => 'dummy-post',
			'post_password' => 'dummy-password'
		], $params);
	}

	function decorateMockLinkMethods($mock, $dummyPost) {
		$mock->method('get_permalink')
			->willReturn('https://dummy.com/dummy/0000/00/00/' . $dummyPost->post_name);
		$mock->method('wp_get_shortlink')
			->willReturn('https://dummy.com?p=' . $dummyPost->ID);
	}
}
