<?php

namespace PPT\Test;

use PPT\PPTAdmin;


class PPTInstallTest extends PPTTestCase {

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

	/**
	 * Test that a standard install puts the default
	 * options in the database
	 */
	public function testInstallDefaultOptions() {
		$expectedOptions = [
			'salt' => 'ceci-n-est-pas-un-salt',
			'enable' => [
				'page', 
				'post'
			],
			'hide_protected' => 0,
			'hash_algo' => 'sha256'
		];

		$pptAdmin = $this->getAdminMock();
		
		$this->wpFunctionsMocks->expects($this->once())
			->method('get_option')
			->willReturn(null);
		$this->wpFunctionsMocks->expects($this->once())
			->method('add_option')
			->with($expectedOptions, '', 'no');
		
		/**
		 * @var PPTAdmin $pptAdmin
		 */
		$pptAdmin->install();
	}

	/**
	 * Test that we don't upgrade up-to-date options
	 */
	public function testCheckUpgradeNoop() {
		$pptAdmin = $this->getAdminMock();

		$options = [
			'salt' => 'ceci-n-est-pas-un-salt',
			'enable' => [
				'page', 
				'post'
			],
			'hide_protected' => 0,
			'hash_algo' => 'sha256'
		];

		$this->wpFunctionsMocks->expects($this->once())
			->method('get_option')
			->willReturn($options);
		$this->wpFunctionsMocks->expects($this->never())
			->method('update_option');
		$this->wpFunctionsMocks->expects($this->never())
			->method('add_option');
		$this->wpFunctionsMocks->expects($this->never())
			->method('delete_option');

		$pptAdmin->check_upgrade();
	}

	/**
	 * Test that we add the old hashing algorithm to 
	 * options that don't have them
	 */
	public function testCheckUpgradeAddAlgo() {
		$pptAdmin = $this->getAdminMock();

		$options = [
			'salt' => 'ceci-n-est-pas-un-salt',
			'enable' => [
				'page', 
				'post'
			],
			'hide_protected' => 0
		];

		$this->wpFunctionsMocks->expects($this->exactly(2))
			->method('get_option')
			->willReturn($options);
		$this->wpFunctionsMocks->expects($this->never())
			->method('add_option');
		$this->wpFunctionsMocks->expects($this->never())
			->method('delete_option');
		$this->wpFunctionsMocks->expects($this->once())
			->method('update_option')
			->with(array_merge($options, [
				'hash_algo' => 'md5'
			]));

		$pptAdmin->check_upgrade();
	}

	/**
	 * Test that we upgrade the original 2-option system
	 * to the modern one option, array-based option
	 */
	public function testCheckUpgradeFromOriginal() {
		$pptAdmin = $this->getAdminMock();

		$this->wpFunctionsMocks->expects($this->any())
			->method('get_option')
			->willReturnMap([
				[null, false, 'old-salt'],
				['ppt_hide_protected', 0, 1]
			]);
		$this->wpFunctionsMocks->expects($this->exactly(2))
			->method('delete_option')
			->willReturnMap([
				['ppt_hide_protected', 1],
				['ppt-token-options', 1]
			]);
		$this->wpFunctionsMocks->expects($this->once())
			->method('add_option')
			->with([
				'salt' => 'old-salt',
				'enable' => [
					'page', 
					'post'
				],
				'hide_protected' => 1,
				'hash_algo' => 'md5'
			], '', 'no');
		
			$pptAdmin->check_upgrade();
	}

		/**
	 * Test that we upgrade the original 2-option system
	 * to the modern one option, array-based option
	 */
	public function testCheckUpgradeFromOriginalNoSavedSalt() {
		$pptAdmin = $this->getAdminMock();

		$this->wpFunctionsMocks->expects($this->any())
			->method('get_option')
			->willReturnMap([
				[null, false, ''],
				['ppt_hide_protected', 0, 1]
			]);
		$this->wpFunctionsMocks->expects($this->exactly(2))
			->method('delete_option')
			->willReturnMap([
				['ppt_hide_protected', 1],
				['ppt-token-options', 1]
			]);
		$this->wpFunctionsMocks->expects($this->once())
			->method('add_option')
			->with([
				'salt' => 'ceci-n-est-pas-un-salt',
				'enable' => [
					'page', 
					'post'
				],
				'hide_protected' => 1,
				'hash_algo' => 'md5'
			], '', 'no');
		
			$pptAdmin->check_upgrade();
	}
}