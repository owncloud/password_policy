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
	/** @var IConfig | \PHPUnit\Framework\MockObject\MockObject */
	protected $config;
	/** @var IUserManager | \PHPUnit\Framework\MockObject\MockObject */
	protected $userManager;
	protected $groupManager;
	/** @var ITimeFactory | \PHPUnit\Framework\MockObject\MockObject */
	private $timeFactory;
	/** @var OldPasswordMapper | \PHPUnit\Framework\MockObject\MockObject */
	private $mapper;
	/** @var CommandTester */
	private $commandTester;

	public function setUp(): void {
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
		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
			]));

		$this->commandTester->execute([
			'--uid' => ['not-existing-uid'],
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();

		self::assertStringContainsString('Unknown user: not-existing-uid', $output);
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

		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
				['password_policy', 'spv_user_password_expiration_value', 90, 10]
			]));

		$this->commandTester->execute([
			'--group' => ['group1', 'group2', 'group4,foo', 'group3'],
			'expiredate' => '2019-01-01 14:00:00 CET'
		]);

		$output = $this->commandTester->getDisplay();
		$expectedOutput = "The password for user1 is set to expire on 2019-01-01 14:00:00 UTC.
The password for user2 is set to expire on 2019-01-01 14:00:00 UTC.
The password for user3 is set to expire on 2019-01-01 14:00:00 UTC.
Unknown group: group3
1 group was not known
";
		$this->assertEquals($expectedOutput, $output);
	}

	public function providesExpirePassword() {
		return [
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
		$expireArg,
		$expireRuleDays,
		$expectedHistoryTimestamp,
		$expectedReportedTimestamp
	) {
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
		$this->assertStringContainsString("The password for existing-uid is set to expire on $expectedReportedTimestamp.", $output);

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

		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
				['password_policy', 'spv_user_password_expiration_value', 90, 10]
			]));

		$this->commandTester->execute([
			'--uid' => ['existing-uid'],
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();

		self::assertStringContainsString("The user's backend doesn't support password changes. The password cannot be expired for user: existing-uid", $output);
	}

	public function testNoRulesSetup() {
		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, false],
			]));

		$this->commandTester->execute([
			'--uid' => ['existing-uid'],
			'expiredate' => '2018-06-28 10:13 UTC'
		]);
		$output = $this->commandTester->getDisplay();

		self::assertStringContainsString("Cannot use this command because no expiration rule was configured", $output);
	}

	public function testInvalidArgument() {
		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
			]));

		$this->commandTester->execute([
			'existing-uid'
		]);
		$output = $this->commandTester->getDisplay();

		$this->assertStringContainsString('Invalid argument given.', $output);
	}

	public function testNonExistingGroups() {
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
			'--group' => ['group1', 'group2']
		]);
		$output = $this->commandTester->getDisplay();
		$expectedOutput = "Unknown group: group1
Unknown group: group2
2 groups were not known
";
		$this->assertEquals($expectedOutput, $output);
	}

	public function testNonExistingUsers() {
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
			'--uid' => ['user1', 'user2']
		]);
		$output = $this->commandTester->getDisplay();
		$expectedOutput = "Unknown user: user1
Unknown user: user2
2 users were not known
";
		$this->assertEquals($expectedOutput, $output);
	}

	public function testExistingUsersAndGroups() {
		$this->timeFactory
			->method('getTime')
			->willReturn(1530180780);

		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
				['password_policy', 'spv_user_password_expiration_value', 90, 10]
			]));

		$user1 = $this->createMock(IUser::class);
		$user1->method('getUID')
			->willReturn('user1');
		$user1->method('canChangePassword')
			->willReturn(true);
		$user2 = $this->createMock(IUser::class);
		$user2->method('getUID')
			->willReturn('user2');
		$user2->method('canChangePassword')
			->willReturn(true);
		$user3 = $this->createMock(IUser::class);
		$user3->method('getUID')
			->willReturn('user3');
		$user3->method('canChangePassword')
			->willReturn(true);
		$user4 = $this->createMock(IUser::class);
		$user4->method('getUID')
			->willReturn('user4');
		$user4->method('canChangePassword')
			->willReturn(true);
		$group1Users = [$user1, $user2];
		$group2Users = [$user3, $user4];

		$this->groupManager
			->method('groupExists')
			->will($this->returnValueMap([
				['group1', true],
				['group2', true]
			]));
		$this->groupManager
			->method('findUsersInGroup')
			->willReturnOnConsecutiveCalls($group1Users, $group2Users);
		$this->userManager
			->method('get')
			->willReturnOnConsecutiveCalls($user1, $user2, $user3, $user4);

		$this->commandTester->execute([
			'--uid' => ['user1', 'user2', 'user3', 'user4'],
			'--group' => ['group1', 'group2']

		]);
		$output = $this->commandTester->getDisplay();
		$expectedOutput = "The password for user1 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user2 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user3 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user4 is set to expire on 2018-06-27 10:13:00 UTC.
";
		$this->assertEquals($expectedOutput, $output);
	}

	public function testExistingUsersAndGroupsSomeDontExist() {
		$this->timeFactory
			->method('getTime')
			->willReturn(1530180780);

		$this->config
			->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, 'on'],
				['password_policy', 'spv_user_password_expiration_value', 90, 10]
			]));

		$user1 = $this->createMock(IUser::class);
		$user1->method('getUID')
			->willReturn('user1');
		$user1->method('canChangePassword')
			->willReturn(true);
		$user2 = $this->createMock(IUser::class);
		$user2->method('getUID')
			->willReturn('user2');
		$user2->method('canChangePassword')
			->willReturn(true);
		$user3 = $this->createMock(IUser::class);
		$user3->method('getUID')
			->willReturn('user3');
		$user3->method('canChangePassword')
			->willReturn(true);
		$user4 = $this->createMock(IUser::class);
		$user4->method('getUID')
			->willReturn('user4');
		$user4->method('canChangePassword')
			->willReturn(true);
		$user5 = $this->createMock(IUser::class);
		$user5->method('getUID')
			->willReturn('user5');
		$user5->method('canChangePassword')
			->willReturn(true);
		$user6 = $this->createMock(IUser::class);
		$user6->method('getUID')
			->willReturn('user6');
		$user6->method('canChangePassword')
			->willReturn(true);
		$user7 = $this->createMock(IUser::class);
		$user7->method('getUID')
			->willReturn('user7');
		$user7->method('canChangePassword')
			->willReturn(true);
		$user8 = $this->createMock(IUser::class);
		$user8->method('getUID')
			->willReturn('user8');
		$user8->method('canChangePassword')
			->willReturn(true);

		$group1Users = [$user1, $user2];
		$group2Users = [$user3, $user4];
		$group3Users = [$user5, $user6];
		$group4Users = [$user7, $user8];

		$this->groupManager
			->method('groupExists')
			->will($this->returnValueMap([
				['group1', true],
				['group2', true],
				['group3', false],
				['group4', false]
			]));
		$this->groupManager
			->method('findUsersInGroup')
			->willReturnOnConsecutiveCalls($group1Users, $group2Users, $group3Users, $group4Users);
		$this->userManager
			->method('get')
			->willReturnOnConsecutiveCalls($user1, $user2, $user3, $user4);

		$this->commandTester->execute([
			'--uid' => ['user1', 'user2', 'user3', 'user4', 'user5', 'user6', 'user7', 'user8'],
			'--group' => ['group1', 'group2', 'group3', 'group4']

		]);
		$output = $this->commandTester->getDisplay();
		$expectedOutput = "The password for user1 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user2 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user3 is set to expire on 2018-06-27 10:13:00 UTC.
The password for user4 is set to expire on 2018-06-27 10:13:00 UTC.
Unknown group: group3
Unknown group: group4
Unknown user: user5
Unknown user: user6
Unknown user: user7
Unknown user: user8
2 groups were not known
4 users were not known
";
		$this->assertEquals($expectedOutput, $output);
	}
}
