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
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserManager;
use Symfony\Component\Console\Tester\CommandTester;
use Test\TestCase;
use OCP\AppFramework\Utility\ITimeFactory;

class ExpirePasswordTest extends TestCase {

	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
	protected $config;
	/** @var IUserManager | \PHPUnit_Framework_MockObject_MockObject */
	protected $userManager;
	protected $groupManager;
	/** @var ITimeFactory | \PHPUnit_Framework_MockObject_MockObject */
	private $timeFactory;
	/** @var OldPasswordMapper | \PHPUnit_Framework_MockObject_MockObject */
	private $mapper;
	/** @var CommandTester */
	private $commandTester;

	public function setUp() {
		parent::setUp();

		$this->config = $this->createMock(IConfig::class);
		$this->userManager = $this->createMock(IUserManager::class);
		$this->groupManager = $this->createMock(IGroupManager::class);
		$this->timeFactory = $this->createMock(ITimeFactory::class);
		$this->mapper = $this->createMock(OldPasswordMapper::class);
		$command = new ExpirePassword(
			$this->config,
			$this->userManager,
			$this->groupManager,
			$this->timeFactory,
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
			'--uid' => ['not-existing-uid'],
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();

		self::assertContains('Unknown user: not-existing-uid', $output);
	}

	private function getAllUsers($numberOfUsers, $groupName = null) {
		$users = [];
		for ($i = 1; $i <= $numberOfUsers; $i++) {
			$user = $this->createMock(IUser::class);
			$user->expects($this->any())
				->method('getUID')
				->willReturn('user' . $i);
			$user->expects($this->any())
				->method('canChangePassword')
				->willReturn(true);
			if ($groupName === null) {
				$users[] = $user;
			} else {
				$users[$groupName][] = $user;
			}
		}
		return $users;
	}

	public function testAllUsersExpirePassword() {
		//Lets say we have 5 users
		$this->userManager->expects($this->once())
			->method('callForAllUsers')
			->will($this->returnCallback(function ($closure) {
				for ($i = 1; $i <= 5; $i++) {
					$user = $this->createMock(IUser::class);
					$user->expects($this->any())
						->method('getUID')
						->willReturn('user' . $i);
					$user->expects($this->once())
						->method('canChangePassword')
						->willReturn(true);
					$closure($user);
				}
			}));

		$this->timeFactory
			->method('getTime')
			->willReturn(1530180780);

		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
				['password_policy', 'spv_user_password_expiration_value', 90, 10]
			]));

		$this->commandTester->execute([
			'--all' => null
		]);

		$output = $this->commandTester->getDisplay();
		$expectedOutput = "The password for user1 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user2 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user3 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user4 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user5 is set to expire on 2018-06-27 10:13:00 UTC.
";
		$this->assertEquals($expectedOutput, $output);
	}

	public function testGroupsExpirePassword() {
		$group1Users = $this->getAllUsers(2, 'group1');
		$group2Users = $this->getAllUsers(3, 'group2');
		$group4Users = $this->getAllUsers(2, 'group4,foo');

		$this->groupManager->expects($this->exactly(4))
			->method('groupExists')
			->willReturnMap([
				['group1', true],
				['group2', true],
				['group3', false],
				['group4,foo', true]
			]);

		$this->groupManager->expects($this->any())
			->method('findUsersInGroup')
			->willReturnOnConsecutiveCalls($group1Users['group1'], $group2Users['group2'], $group4Users['group4,foo']);

		$this->timeFactory
			->method('getTime')
			->willReturn(1530180780);

		$this->commandTester->execute([
			'--group' => ['group1', 'group2', 'group4,foo', 'group3'],
			'expiredate' => '2019-01-01 14:00:00 CET'
		]);

		$output = $this->commandTester->getDisplay();
		$expectedOutput = "The password for user1 is set to expire on 2019-01-01 14:00:00 UTC.
The password for user2 is set to expire on 2019-01-01 14:00:00 UTC.
The password for user3 is set to expire on 2019-01-01 14:00:00 UTC.
Ignoring missing group group3
";
		$this->assertEquals($expectedOutput, $output);
	}

	public function providesExpirePassword() {
		return [
			// expire immediately, no policy, defaults to -1 days
			[null, null, '2018-06-27 10:13:00 UTC', '2018-06-27 10:13:00 UTC'],
			// expire later, no policy
			['2018-06-28 10:13 UTC', null, '2018-06-28 10:13:00 UTC', '2018-06-28 10:13:00 UTC'],
			// expire immediately, with policy, defaults to -1 days
			[null, 7, '2018-06-20 10:13:00 UTC', '2018-06-27 10:13:00 UTC'],
			// expire later, with policy
			['2018-06-28 10:13 UTC', 7, '2018-06-21 10:13:00 UTC', '2018-06-28 10:13:00 UTC'],
		];
	}

	/**
	 * @dataProvider providesExpirePassword
	 */
	public function testExpirePassword(
		$expireArg, $expireRuleDays, $expectedHistoryTimestamp, $expectedReportedTimestamp) {
		$user = $this->createMock(IUser::class);
		$user->expects($this->any())
			->method('getUID')
			->willReturn('existing-uid');
		$user
			->expects($this->once())
			->method('canChangePassword')
			->willReturn(true);

		$this->userManager
			->expects($this->once())
			->method('get')
			->with('existing-uid')
			->willReturn($user);

		$this->timeFactory
			->method('getTime')
			->willReturn(1530180780);

		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, ($expireRuleDays !== null) ? 'on' : false],
				['password_policy', 'spv_user_password_expiration_value', 90, $expireRuleDays]
			]));

		$oldPassword = null;
		$this->mapper->expects($this->once())
			->method('insert')
			->will($this->returnCallback(function ($obj) use (&$oldPassword) {
				$oldPassword = $obj;
			}));

		$this->commandTester->execute([
			'--uid' => ['existing-uid'],
			'expiredate' => $expireArg
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertContains("The password for existing-uid is set to expire on $expectedReportedTimestamp.", $output);

		$this->assertEquals('existing-uid', $oldPassword->getUid());
		$this->assertEquals(OldPassword::EXPIRED, $oldPassword->getPassword());
		$this->assertEquals((new \DateTime($expectedHistoryTimestamp))->getTimestamp(), $oldPassword->getChangeTime());
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
			'--uid' => ['existing-uid'],
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();

		self::assertContains("The user's backend doesn't support password changes. The password cannot be expired for user: existing-uid", $output);
	}
}
