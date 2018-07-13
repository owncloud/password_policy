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

namespace OCA\PasswordPolicy\Tests\Controller;

use OCA\PasswordPolicy\Command\ExpirePassword;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCA\PasswordPolicy\Db\OldPassword;
use OCP\IConfig;
use OCP\IUser;
use OCP\IUserManager;
use Symfony\Component\Console\Tester\CommandTester;
use Test\TestCase;

class ExpirePasswordTest extends TestCase {

	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
	protected $config;
	/** @var IUserManager | \PHPUnit_Framework_MockObject_MockObject */
	protected $userManager;
	/** @var OldPasswordMapper | \PHPUnit_Framework_MockObject_MockObject */
	private $mapper;
	/** @var CommandTester */
	private $commandTester;

	public function setUp() {
		parent::setUp();

		$this->config = $this->createMock(IConfig::class);
		$this->userManager = $this->createMock(IUserManager::class);
		$this->mapper = $this->createMock(OldPasswordMapper::class);
		$command = new ExpirePassword(
			$this->config,
			$this->userManager,
			$this->mapper
		);
		$this->commandTester = new CommandTester($command);
	}

	public function testExpirePasswordUserNotExisting() {
		$this->userManager
			->expects(self::once())
			->method('get')
			->with('not-existing-uid')
			->willReturn(null);

		$this->commandTester->execute([
			'uid' => 'not-existing-uid',
			'expiredate' => '2018-06-28 10:13 UTC'
			]);
		$output = $this->commandTester->getDisplay();
		self::assertContains('Unknown user: not-existing-uid', $output);
	}

	public function testExpirePassword() {
		$user = $this->createMock(IUser::class);
		$user
			->expects($this->once())
			->method('canChangePassword')
			->willReturn(true);

		$this->userManager
			->expects($this->once())
			->method('get')
			->with('existing-uid')
			->willReturn($user);
		$this->config
			->expects($this->once())
			->method('setUserValue')
			->with(
				'existing-uid',
				'password_policy',
				'forcePasswordChange',
				'2018-06-28T10:13:00Z'
			);

		$latestPassword = OldPassword::fromRow([
			'id' => 2233,
			'uid' => 'usertest1',
			'password' => 'randomHashedPassword',
			'changeTime' => '1515151515'
		]);

		$this->mapper->method('getLatestPassword')
			->willReturn($latestPassword);

		$this->mapper->expects($this->once())
			->method('insert');

		$this->commandTester->execute([
			'uid' => 'existing-uid',
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();
		self::assertContains('The password for existing-uid is set to expire on 2018-06-28 10:13:00 UTC.', $output);
	}

	public function testExpirePasswordDummy() {
		$user = $this->createMock(IUser::class);
		$user
			->expects($this->once())
			->method('canChangePassword')
			->willReturn(true);

		$this->userManager
			->expects($this->once())
			->method('get')
			->with('existing-uid')
			->willReturn($user);
		$this->config
			->expects($this->once())
			->method('setUserValue')
			->with(
				'existing-uid',
				'password_policy',
				'forcePasswordChange',
				'2018-06-28T10:13:00Z'
			);

		$latestPassword = OldPassword::fromRow([
			'id' => 2233,
			'uid' => 'usertest1',
			'password' => 'dummy',
			'changeTime' => '1515151515'
		]);

		$this->mapper->method('getLatestPassword')
			->willReturn($latestPassword);

		$this->mapper->expects($this->never())
			->method('insert');

		$this->commandTester->execute([
			'uid' => 'existing-uid',
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();
		self::assertContains('The password for existing-uid is set to expire on 2018-06-28 10:13:00 UTC.', $output);
	}

	public function testCannotExpirePassword() {
		$user = $this->createMock(IUser::class);
		$user
			->expects($this->once())
			->method('canChangePassword')
			->willReturn(false);

		$this->userManager
			->expects($this->once())
			->method('get')
			->with('existing-uid')
			->willReturn($user);

		$this->commandTester->execute([
			'uid' => 'existing-uid',
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();
		self::assertContains("The user's backend doesn't support password changes. The password cannot be expired for user: existing-uid", $output);
	}

}
