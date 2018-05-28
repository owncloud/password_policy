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
use OCP\IConfig;
use OCP\IUserManager;
use Symfony\Component\Console\Tester\CommandTester;
use Test\TestCase;

class ExpirePasswordTest extends TestCase {

	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
	protected $config;
	/** @var IUserManager | \PHPUnit_Framework_MockObject_MockObject */
	protected $userManager;
	/** @var CommandTester */
	private $commandTester;

	public function setUp() {
		parent::setUp();

		$this->config = $this->createMock(IConfig::class);
		$this->userManager = $this->createMock(IUserManager::class);
		$command = new ExpirePassword(
			$this->config,
			$this->userManager
		);
		$this->commandTester = new CommandTester($command);
	}

	public function testExpirePasswordUserNotExisting() {
		$this->userManager
			->expects(self::once())
			->method('userExists')
			->with('not-existing-uid')
			->willReturn(false);

		$this->commandTester->execute([
			'uid' => 'not-existing-uid',
			'expiredate' => '2018-06-28 10:13 UTC'
			]);
		$output = $this->commandTester->getDisplay();
		self::assertContains('Unknown user: not-existing-uid', $output);
	}

	public function testExpirePassword() {
		$this->userManager
			->expects($this->once())
			->method('userExists')
			->with('existing-uid')
			->willReturn(true);
		$this->config
			->expects($this->once())
			->method('setUserValue')
			->with(
				'existing-uid',
				'password_policy',
				'forcePasswordChange',
				'2018-06-28T10:13:00Z'
			);

		$this->commandTester->execute([
			'uid' => 'existing-uid',
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();
		self::assertContains('The password for existing-uid is set to expire on 2018-06-28 10:13:00 UTC.', $output);
	}

}
