<?php
/**
 *
 * @author Juan Pablo Villafáñez <jvillafanez@solidgear.es>
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

namespace OCA\PasswordPolicy\Tests\Jobs;

use OCP\IUser;
use OCP\Notification\IManager;
use OCP\Notification\INotification;
use OCP\Notification\IAction;
use OCP\IURLGenerator;
use OCP\ILogger;
use OCP\IUserManager;
use OCP\AppFramework\Utility\ITimeFactory;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCA\PasswordPolicy\Db\OldPassword;
use OCA\PasswordPolicy\UserNotificationConfigHandler;
use OCA\PasswordPolicy\Jobs\PasswordExpirationNotifierJob;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Test\TestCase;

class PasswordExpirationNotifierJobTest extends TestCase {
	/** @var OldPasswordMapper */
	private $mapper;

	/** @var $manager */
	private $manager;

	/** @var UserNotificationConfigHandler */
	private $unConfigHandler;

	/** @var IUserManager */
	private $userManager;

	/** @var ITimeFactory */
	private $timeFactory;

	/** @var IURLGenerator */
	private $urlGenerator;

	/** @var ILogger */
	private $logger;

	private $eventDispatcher;

	/** @var PasswordExpirationNotifierJob */
	private $job;

	protected function setUp(): void {
		parent::setUp();
		$this->mapper = $this->getMockBuilder(OldPasswordMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->manager = $this->getMockBuilder(IManager::class)
			->disableOriginalConstructor()
			->getMock();
		$this->unConfigHandler = $this->getMockBuilder(UserNotificationConfigHandler::class)
			->disableOriginalConstructor()
			->getMock();
		$this->userManager = $this->getMockBuilder(IUserManager::class)
			->disableOriginalConstructor()
			->getMock();
		$this->timeFactory = $this->getMockBuilder(ITimeFactory::class)
			->disableOriginalConstructor()
			->getMock();
		$this->urlGenerator = $this->getMockBuilder(IURLGenerator::class)
			->disableOriginalConstructor()
			->getMock();
		$this->logger = $this->getMockBuilder(ILogger::class)
			->disableOriginalConstructor()
			->getMock();
		$this->eventDispatcher = $this->createMock(EventDispatcher::class);

		$this->job = new PasswordExpirationNotifierJob(
			$this->mapper,
			$this->manager,
			$this->unConfigHandler,
			$this->userManager,
			$this->timeFactory,
			$this->urlGenerator,
			$this->logger,
			$this->eventDispatcher
		);

		$this->manager->method('createNotification')
			->will($this->returnCallback(function () {
				$holder = [];
				$mock = $this->createMock(INotification::class);
				$mock->method('setApp')->will($this->returnCallback(function ($app) use (&$holder, $mock) {
					$holder['app'] = $app;
					return $mock;
				}));
				$mock->method('setUser')->will($this->returnCallback(function ($user) use (&$holder, $mock) {
					$holder['user'] = $user;
					return $mock;
				}));
				$mock->method('setObject')->will($this->returnCallback(function ($obj, $id) use (&$holder, $mock) {
					$holder['object'] = [$obj, $id];
					return $mock;
				}));
				$mock->method('setDateTime')->will($this->returnCallback(function ($time) use (&$holder, $mock) {
					$holder['datetime'] = $time;
					return $mock;
				}));
				$mock->method('setSubject')->will($this->returnCallback(function ($subject) use (&$holder, $mock) {
					$holder['subject'] = $subject;
					return $mock;
				}));
				$mock->method('setMessage')->will($this->returnCallback(function ($message) use (&$holder, $mock) {
					$holder['message'] = $message;
					return $mock;
				}));
				$mock->method('setLink')->will($this->returnCallback(function ($link) use (&$holder, $mock) {
					$holder['link'] = $link;
					return $mock;
				}));
				$mock->method('getApp')->will($this->returnCallback(function () use (&$holder) {
					return $holder['app'];
				}));
				$mock->method('getUser')->will($this->returnCallback(function () use (&$holder) {
					return $holder['user'];
				}));
				$mock->method('getObjectType')->will($this->returnCallback(function () use (&$holder) {
					return $holder['object'][0];
				}));
				$mock->method('getObjectId')->will($this->returnCallback(function () use (&$holder) {
					return $holder['object'][1];
				}));
				$mock->method('createAction')->will($this->returnCallback(function () {
					$actionMock = $this->createMock(IAction::class);
					$actionMock->method('setLabel')->will($this->returnSelf());
					$actionMock->method('setLink')->will($this->returnSelf());
					return $actionMock;
				}));
				return $mock;
			}));
	}

	public function testRunNoExpiration() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(null);

		$this->mapper->expects($this->never())
			->method('getPasswordsAboutToExpire');
		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function testRunWrongRange() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(120);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$this->mapper->expects($this->never())
			->method('getPasswordsAboutToExpire');
		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function testRunEmptyInfo() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 20);

		$this->mapper->expects($this->once())
			->method('getPasswordsAboutToExpire')
			->willReturn([]);
		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	private function getOldPassword($id, $userid, $baseTime, $password = 'password') {
		$data = [
			'id' => $id,
			'uid' => $userid,
			'password' => $password,
			'changeTime' => $baseTime
		];
		return OldPassword::fromRow($data);
	}

	public function testRunAboutToExpireAlreadySent() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 150);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(true);

		$this->unConfigHandler->method('isSentAboutToExpireNotification')
			->willReturn(true);

		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function testRunExpiredAlreadySent() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 250);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->unConfigHandler->method('isSentExpiredNotification')
			->willReturn(true);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(true);

		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function testRunAboutToExpireMissingUser() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 150);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(false);

		$this->unConfigHandler->method('isSentAboutToExpireNotification')
			->willReturn(false);

		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function testRunExpiredMissingUser() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 250);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->unConfigHandler->method('isSentExpiredNotification')
			->willReturn(false);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(false);

		$this->manager->expects($this->never())
			->method('notify');

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function providesExpirePassword() {
		return [
			[\md5('password')],
			// special password
			[OldPassword::EXPIRED],
		];
	}

	/**
	 * @dataProvider providesExpirePassword
	 */
	public function testRunAboutToExpire($password) {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 150);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime, $password);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->unConfigHandler->method('isSentAboutToExpireNotification')
			->willReturn(false);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(true);

		$this->manager->expects($this->once())
			->method('notify');
		$this->unConfigHandler->expects($this->once())
			->method('markAboutToExpireNotificationSentFor')
			->with($returnedOldPassword);

		$iUser = $this->createMock(IUser::class);
		$this->userManager->expects($this->once())
			->method('get')
			->willReturn($iUser);
		$aboutToExpireEvent = new GenericEvent(null,
			['expireStatus' => 'about_to_expire',
				'user' => $iUser,
				'passwordExpireInSeconds' => 180]);
		$this->eventDispatcher->expects($this->once())
			->method('dispatch')
			->with('user.passwordAboutToExpire', $aboutToExpireEvent);

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	/**
	 * @dataProvider providesExpirePassword
	 */
	public function testRunExpired($password) {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(120);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 250);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime, $password);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->unConfigHandler->method('isSentExpiredNotification')
			->willReturn(false);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(true);

		$this->manager->expects($this->once())
			->method('notify');
		$this->unConfigHandler->expects($this->once())
			->method('markExpiredNotificationSentFor')
			->with($returnedOldPassword);

		$iUser = $this->createMock(IUser::class);
		$this->userManager->expects($this->once())
			->method('get')
			->willReturn($iUser);
		$aboutToExpireEvent = new GenericEvent(null,
			['expireStatus' => 'expired',
				'user' => $iUser,
				'passwordExpireInSeconds' => 180]);
		$this->eventDispatcher->expects($this->once())
			->method('dispatch')
			->with('user.passwordExpired', $aboutToExpireEvent);

		$this->invokePrivate($this->job, 'run', [[]]);
	}

	public function testRunAboutToExpireNotConfigured() {
		$this->unConfigHandler->method('getExpirationTime')
			->willReturn(180);
		$this->unConfigHandler->method('getExpirationTimeForNormalNotification')
			->willReturn(null);

		$baseTime = 1531232050;
		$this->timeFactory->method('getTime')
			->willReturn($baseTime + 150);

		$returnedOldPassword = $this->getOldPassword('22', 'usertest', $baseTime);
		$this->mapper->method('getPasswordsAboutToExpire')
			->willReturn([$returnedOldPassword]);

		$this->unConfigHandler->method('isSentAboutToExpireNotification')
			->willReturn(false);

		$this->userManager->method('userExists')
			->with('usertest')
			->willReturn(true);

		$this->manager->expects($this->never())
			->method('notify');
		$this->unConfigHandler->expects($this->never())
			->method('markAboutToExpireNotificationSentFor')
			->with($returnedOldPassword);

		$this->invokePrivate($this->job, 'run', [[]]);
	}
}
