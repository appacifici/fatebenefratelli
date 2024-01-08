<?php

namespace PPT\Test;

use PPT\PPTAdmin;

class PPTAdminTest extends PPTTestCase {
	public $_POST;
	public $_SERVER;

	/**
	 * Create a mock of the admin that only mocks
	 * the create_salt method
	 */
	public function getAdminMock() {
		$adminMock = parent::getAdminMock()
			->onlyMethods(['create_salt'])
			->getMock();
		$adminMock->method('create_salt')
			->willReturn('ceci-n-est-pas-un-salt');

		/**
		 * @var PPTAdmin $adminMock
		 */
		$adminMock->setWPFuncs($this->wpFunctionsMocks);

		/**
		 * @var PHPUnit\Framework\MockObject\MockObject $adminMock
		 */
		return $adminMock;
	}

	public function mockRedirectExpects() {
		$this->wpFunctionsMocks->expects($this->once())
			->method('admin_url')
			->with('options-general.php?page=post-password-token');
		$this->wpFunctionsMocks->expects($this->once())
			->method('wp_redirect');
	}

	public function setUp(): void {
		parent::setUp();
		$this->_POST = $_POST;
		$this->_SERVER = $_SERVER;
	}

	public function tearDown(): void {
		parent::tearDown();
		$_POST = $this->_POST;
		$_SERVER = $this->_SERVER;
	}

	public function testProcessOptionsNoPrevEnable() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array_merge($this->_POST, [
			'ppt-save-options' => 1,
			'ppt_hide_protected' => 1,
			'ppt_enable' => [
				'post' => 1,
				'page' => 1
			]
		]);

		$this->mockRedirectExpects();
		$this->wpFunctionsMocks->expects($this->once())
			->method('get_option')
			->with(null, false)
			->willReturn([
				'salt' => 'salty-salt-salt-salt',
				'enable' => [],
				'hide_protected' => 0,
				'hash_algo' => 'sha256'
			]);
		$this->wpFunctionsMocks->expects($this->once())
			->method('update_option')
			->with([
				'salt' => 'salty-salt-salt-salt',
				'hide_protected' => 1,
				'enable' => [
					'post',
					'page'
				],
				'hash_algo' => 'sha256'
			])
			->willReturn(true);

		$admin = $this->getAdminMock();
		$admin->admin_process_form();
	}

	public function testProcessOptionsChangePrevEnable() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array_merge($this->_POST, [
			'ppt-save-options' => 1,
			'ppt_hide_protected' => 1,
			'ppt_enable' => [
				'post' => 1,
				'foo' => 1
			]
		]);

		$this->mockRedirectExpects();
		$this->wpFunctionsMocks->expects($this->once())
			->method('get_option')
			->with(null, false)
			->willReturn([
				'salt' => 'salty-salt-salt-salt',
				'enable' => [
					'post',
					'page'
				],
				'hide_protected' => 0,
				'hash_algo' => 'sha256'
			]);
		$this->wpFunctionsMocks->expects($this->once())
			->method('update_option')
			->with([
				'salt' => 'salty-salt-salt-salt',
				'hide_protected' => 1,
				'enable' => [
					'post',
					'foo'
				],
				'hash_algo' => 'sha256'
			])
			->willReturn(true);

		$admin = $this->getAdminMock();
		$admin->admin_process_form();
	}

	public function testProcessOptionsSaveSalt() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array_merge($this->_POST, [
			'ppt-save-salt' => 1,
			'ppt_salt' => 'salty!'
		]);

		$this->mockRedirectExpects();
		$this->wpFunctionsMocks->expects($this->once())
			->method('get_option')
			->with(null, false)
			->willReturn([
				'salt' => 'salty-salt-salt-salt',
				'enable' => [],
				'hide_protected' => 0,
				'hash_algo' => 'sha256'
			]);

		$this->wpFunctionsMocks->expects($this->once())
			->method('update_option')
			->with([
				'salt' => 'salty!',
				'hide_protected' => 0,
				'enable' => [],
				'hash_algo' => 'sha256'
			])
			->willReturn(true);
		
		$admin = $this->getAdminMock();
		$admin->admin_process_form();
	}

	public function testProcessOptionsSaveAlgo() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array_merge($this->_POST, [
			'ppt-save-algorithm' => 1,
			'ppt_algo' => 'md5'
		]);

		$this->mockRedirectExpects();
		$this->wpFunctionsMocks->expects($this->once())
			->method('get_option')
			->with(null, false)
			->willReturn([
				'salt' => 'salty-salt-salt-salt',
				'enable' => [],
				'hide_protected' => 0,
				'hash_algo' => 'sha256'
			]);

		$this->wpFunctionsMocks->expects($this->once())
			->method('update_option')
			->with([
				'salt' => 'salty-salt-salt-salt',
				'hide_protected' => 0,
				'enable' => [],
				'hash_algo' => 'md5'
			])
			->willReturn(true);
		
		$admin = $this->getAdminMock();
		$admin->admin_process_form();
	}
}