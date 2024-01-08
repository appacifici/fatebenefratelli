<?php

namespace PPT\Test;


class PPTCoreTest extends PPTTestCase {

	function testGetPrettyUrlSha256() {
		$dummyPost = $this->getDummyPost();
		$this->decorateMockLinkMethods($this->wpFunctionsMocks, $dummyPost);
		$this->wpFunctionsMocks->method('get_option')
			->willReturn($this->getDefaultOptions());


		$admin = $this->getAdminObj();
		$url = $admin->get_ppt_permalink($dummyPost, false);

		$this->assertStringEndsWith(
			'dummy-post?ppt=fb8a6c2cf2a8c84f1059e85c2ecdb2e5b4cc56d8adce76d258659812b243cc31', 
			$url
		);
	}

	function testGetShortUrSha256() {
		$dummyPost = $this->getDummyPost();
		$this->decorateMockLinkMethods($this->wpFunctionsMocks, $dummyPost);
		$this->wpFunctionsMocks->method('get_option')
			->willReturn($this->getDefaultOptions());

		$admin = $this->getAdminObj();
		$url = $admin->get_ppt_permalink($dummyPost, true);

		$this->assertStringEndsWith(
			'?p=1&ppt=fb8a6c2cf2a8c84f1059e85c2ecdb2e5b4cc56d8adce76d258659812b243cc31', 
			$url
		);
	}

	function testGetPrettyUrlMd5() {
		$dummyPost = $this->getDummyPost();
		$this->decorateMockLinkMethods($this->wpFunctionsMocks, $dummyPost);
		$this->wpFunctionsMocks
			->method('get_option')
			->willReturn(array_merge($this->getDefaultOptions(), [
				'hash_algo' => 'md5'
			]));

		$admin = $this->getAdminObj();
		$url = $admin->get_ppt_permalink($dummyPost, false);

		$this->assertStringEndsWith('dummy-post?ppt=c5acf4f3039cee585558391373591270', $url);
	}

	function testGetShortUrShaMd5() {
		$dummyPost = $this->getDummyPost();
		$this->decorateMockLinkMethods($this->wpFunctionsMocks, $dummyPost);
		$this->wpFunctionsMocks
			->method('get_option')
			->willReturn(array_merge($this->getDefaultOptions(), [
				'hash_algo' => 'md5'
			]));
	
		$admin = $this->getAdminObj();
		$url = $admin->get_ppt_permalink($dummyPost, true);

		$this->assertStringEndsWith('?p=1&ppt=c5acf4f3039cee585558391373591270', $url);
	}
}
