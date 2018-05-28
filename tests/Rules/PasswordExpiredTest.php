<?php
/**
 * @author Jörn Friedrich Dreyer <jfd@butonic.de>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license GPL-2.0
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\PasswordPolicy\Tests\Rules;

use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCA\PasswordPolicy\Db\OldPassword;
use OCA\PasswordPolicy\Rules\PasswordExpired;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IL10N;
use OCP\ILogger;
use OCP\Security\IHasher;
use Test\TestCase;

class PasswordExpiredTest extends TestCase {

	/** @var ILogger | \PHPUnit_Framework_MockObject_MockObject */
	protected $logger;
	/** @var OldPasswordMapper | \PHPUnit_Framework_MockObject_MockObject */
	protected $mapper;
	/** @var IHasher | \PHPUnit_Framework_MockObject_MockObject */
	protected $hasher;
	/** @var ITimeFactory | \PHPUnit_Framework_MockObject_MockObject */
	protected $timeFactory;
	/** @var PasswordExpired */
	private $r;

	public function setUp() {
		parent::setUp();

		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10n */
		$l10n = $this->createMock(IL10N::class);
		$l10n
			->method('t')
			->will($this->returnCallback(function($text, $parameters = array()) {
				return vsprintf($text, $parameters);
			}));
		$this->logger = $this->createMock(ILogger::class);
		$this->mapper = $this->createMock(OldPasswordMapper::class);
		$this->hasher = $this->createMock(IHasher::class);
		$this->timeFactory = $this->createMock(ITimeFactory::class);
		$this->r = new PasswordExpired(
			$l10n,
			$this->logger,
			$this->mapper,
			$this->hasher,
			$this->timeFactory
		);
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testNoPasswordLogsDebug() {
		$this->mapper
			->expects($this->once())
			->method('getLatestPassword')
			->willReturn(null);
		$this->hasher
			->expects($this->never())
			->method('verify');
		$this->timeFactory
			->expects($this->never())
			->method('getTime');
		$this->logger
			->expects($this->once())
			->method('debug');

		$this->r->verify('secret', 1, 'testuid');
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testWrongPasswordLogsWarning() {
		$password = new OldPassword();
		$password->setChangeTime(1234);
		$this->mapper
			->expects($this->once())
			->method('getLatestPassword')
			->willReturn($password);
		$this->hasher
			->expects($this->once())
			->method('verify')
			->willReturn(false);
		$this->timeFactory
			->expects($this->never())
			->method('getTime');
		$this->logger
			->expects($this->once())
			->method('warning');

		$this->r->verify('secret', 1, 'testuid');
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testMissingChangeTimeLogsWarning() {
		$password = new OldPassword();
		$this->mapper
			->expects($this->once())
			->method('getLatestPassword')
			->willReturn($password);
		$this->hasher
			->expects($this->once())
			->method('verify')
			->willReturn(true);
		$this->timeFactory
			->expects($this->never())
			->method('getTime');
		$this->logger
			->expects($this->once())
			->method('warning');

		$this->r->verify('secret', 1, 'testuid');
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password is older than 2 days.
	 */
	public function testFail() {
		$password = new OldPassword();
		$password->setChangeTime(1);
		$this->mapper
			->expects($this->once())
			->method('getLatestPassword')
			->willReturn($password);
		$this->hasher
			->expects($this->once())
			->method('verify')
			->willReturn(true);
		$this->timeFactory
			->expects($this->once())
			->method('getTime')
			->willReturn(10000000);
		$this->logger
			->expects($this->never())
			->method('warning');
		$this->logger
			->expects($this->never())
			->method('debug');
		$this->logger
			->expects($this->never())
			->method('error');

		$this->r->verify('secret', 2, 'testuid');
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testSuccess() {
		$now = 1234;
		$password = new OldPassword();
		$password->setChangeTime($now);
		$this->mapper
			->expects($this->once())
			->method('getLatestPassword')
			->willReturn($password);
		$this->hasher
			->expects($this->once())
			->method('verify')
			->willReturn(true);
		$this->timeFactory
			->expects($this->once())
			->method('getTime')
			->willReturn($now);
		$this->logger
			->expects($this->once())
			->method('debug');

		$this->r->verify('secret', 1, 'testuid');
	}
}
