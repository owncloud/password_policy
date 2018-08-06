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

namespace OCA\PasswordPolicy\Tests;

use OCA\PasswordPolicy\Db\OldPassword;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCA\PasswordPolicy\Engine;
use OCA\PasswordPolicy\HooksHandler;
use OCA\PasswordPolicy\UserNotificationConfigHandler;
use OCA\PasswordPolicy\Rules\PasswordExpired;
use OCA\PasswordPolicy\Rules\PolicyException;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IConfig;
use OCP\IL10N;
use OCP\ISession;
use OCP\IUser;
use OCP\Security\IHasher;
use OCP\Notification\IManager;
use OCP\Notification\INotification;
use Symfony\Component\EventDispatcher\GenericEvent;
use Test\TestCase;

class HooksHandlerTest extends TestCase {

	/** @var IConfig | \PHPUnit_Framework_MockObject_MockObject */
	protected $config;
	/** @var Engine | \PHPUnit_Framework_MockObject_MockObject */
	protected $engine;
	/** @var IHasher | \PHPUnit_Framework_MockObject_MockObject */
	protected $hasher;
	/** @var ITimeFactory | \PHPUnit_Framework_MockObject_MockObject */
	protected $timeFactory;
	/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject */
	protected $l10n;
	/** @var PasswordExpired | \PHPUnit_Framework_MockObject_MockObject */
	protected $passwordExpiredRule;
	/** @var OldPasswordMapper | \PHPUnit_Framework_MockObject_MockObject */
	protected $oldPasswordMapper;
	/** @var ISession | \PHPUnit_Framework_MockObject_MockObject */
	protected $session;
	/** @var HooksHandler | \PHPUnit_Framework_MockObject_MockObject */
	protected $handler;
	/** @var UserNotificationConfigHandler | \PHPUnit_Framework_MockObject_MockObject */
	protected $unConfigHandler;
	/** @var IManager | \PHPUnit_Framework_MockObject_MockObject */
	protected $manager;

	protected function setUp() {
		parent::setUp();

		$this->config = $this->createMock(IConfig::class);
		$this->engine = $this->createMock(Engine::class);
		$this->hasher = $this->createMock(IHasher::class);
		$this->timeFactory = $this->createMock(ITimeFactory::class);
		$this->l10n = $this->createMock(IL10N::class);
		$this->l10n
			->method('t')
			->will($this->returnCallback(function($text, $parameters = []) {
				return \vsprintf($text, $parameters);
			}));
		$this->passwordExpiredRule = $this->createMock(PasswordExpired::class);
		$this->oldPasswordMapper = $this->createMock(OldPasswordMapper::class);
		$this->session = $this->createMock(ISession::class);
		$this->manager = $this->createMock(IManager::class);
		$this->unConfigHandler = $this->createMock(UserNotificationConfigHandler::class);

		$this->handler = new HooksHandler(
			$this->config,
			$this->engine,
			$this->hasher,
			$this->timeFactory,
			$this->l10n,
			$this->passwordExpiredRule,
			$this->oldPasswordMapper,
			$this->session,
			$this->manager,
			$this->unConfigHandler
		);

		$this->manager->method('createNotification')
			->will($this->returnCallback(function() {
				$holder = [];
				$mock = $this->createMock(INotification::class);
				$mock->method('setApp')->will($this->returnCallback(function($app) use (&$holder, $mock) {
					$holder['app'] = $app;
					return $mock;
				}));
				$mock->method('setUser')->will($this->returnCallback(function($user) use (&$holder, $mock) {
					$holder['user'] = $user;
					return $mock;
				}));
				$mock->method('setObject')->will($this->returnCallback(function($obj, $id) use (&$holder, $mock) {
					$holder['object'] = [$obj, $id];
					return $mock;
				}));
				$mock->method('getApp')->will($this->returnCallback(function() use (&$holder) {
					return $holder['app'];
				}));
				$mock->method('getUser')->will($this->returnCallback(function() use (&$holder) {
					return $holder['user'];
				}));
				$mock->method('getObjectType')->will($this->returnCallback(function() use (&$holder) {
					return $holder['object'][0];
				}));
				$mock->method('getObjectId')->will($this->returnCallback(function() use (&$holder) {
					return $holder['object'][1];
				}));
				return $mock;
			}));
	}

	public function testGeneratePassword() {
		$this->engine->expects($this->once())
			->method('generatePassword')
			->willReturn('secret');
		$event = new GenericEvent();
		$this->handler->generatePassword($event);
		self::assertSame('secret', $event->getArgument('password'));
	}

	public function testVerifyPasswordWithPasswordOnly() {
		$this->engine->expects($this->once())
			->method('verifyPassword')
			->with('secret', null);

		$event = new GenericEvent(null, ['password' => 'secret']);
		$this->handler->verifyUserPassword($event);
	}

	public function testVerifyPasswordWithPasswordAndUid() {
		$this->engine->expects($this->once())
			->method('verifyPassword')
			->with('secret', 'testuid');

		$event = new GenericEvent(null, ['uid' => 'testuid', 'password' => 'secret']);
		$this->handler->verifyUserPassword($event);
	}

	public function testVerifyUserPassword() {
		$this->engine->expects($this->once())
			->method('verifyPassword')
			->with('secret', null, 'user');

		$event = new GenericEvent(null, ['password' => 'secret']);
		$this->handler->verifyUserPassword($event);
	}

	public function testVerifyPublicPassword() {
		$this->engine->expects($this->once())
			->method('verifyPassword')
			->with('secret', null, 'public');

		$event = new GenericEvent(null, ['password' => 'secret']);
		$this->handler->verifyPublicPassword($event);
	}

	public function updateLinkExpiryProvider() {
		$tomorrow = new \DateTime();
		$tomorrow->setTime(0,0,0);
		$tomorrow->add(new \DateInterval('P1D')); // tomorrow
		$in5days = new \DateTime();
		$in5days->setTime(0,0,0);
		$in5days->add(new \DateInterval('P5D')); // in 5 days

		$accepted = true; // needs to be reset on every test set
		return [
			[['checked' => false, 'passwordSet' => true, 'expirationDate' => null, 'accepted' => &$accepted], true],
			[['checked' => false, 'passwordSet' => false, 'expirationDate' => null, 'accepted' => &$accepted], true],
			[['checked' => 'on', 'passwordSet' => true, 'expirationDate' => null, 'accepted' => &$accepted], false],
			[['checked' => 'on', 'passwordSet' => true, 'expirationDate' => $tomorrow, 'accepted' => &$accepted], true],
			[['checked' => 'on', 'passwordSet' => true, 'expirationDate' => $in5days, 'accepted' => &$accepted], false],
		];
	}

	/**
	 * @param $params
	 * @param $expected
	 * @dataProvider updateLinkExpiryProvider
	 */
	public function testUpdateLinkExpiry($params, $expected) {
		$this->engine->expects($this->once())
			->method('getConfigValues')
			->willReturn([
				'spv_expiration_password_checked' => $params['checked'],
				'spv_expiration_password_value' => '3', // in 3 days
				'spv_expiration_nopassword_checked' => $params['checked'],
				'spv_expiration_nopassword_value' => '3' // in 3 days
			]);
		$params['accepted'] = true; // needs to be reset on every test set
		$this->handler->updateLinkExpiry($params);
		self::assertSame($expected, $params['accepted']);

	}

	public function testSaveOldPassword() {
		/** @var IUser | \PHPUnit_Framework_MockObject_MockObject $user */
		$user = $this->createMock(IUser::class);
		$user->method('getUID')->willReturn('testuid');

		$this->hasher->expects($this->once())
			->method('hash')
			->with('secret')
			->willReturn('somehash');

		$this->timeFactory->expects($this->once())
			->method('getTime')
			->willReturn(12345);

		$this->oldPasswordMapper->expects($this->once())
			->method('insert')
			->with($this->callback(function(OldPassword $oldPassword){
				return $oldPassword->getPassword() === 'somehash' &&
					$oldPassword->getUid() === 'testuid' &&
					$oldPassword->getChangeTime() === 12345;
			}));

		$this->unConfigHandler->expects($this->once())
			->method('resetExpirationMarks');

		$event = new GenericEvent(null, [
			'user' => $user,
			'password' => 'secret'
		]);
		$this->handler->saveOldPassword($event);
	}

	public function testSavePasswordForCreatedUser() {
		$this->hasher->expects($this->once())
			->method('hash')
			->with('secret')
			->willReturn('somehash');

		$this->timeFactory->expects($this->once())
			->method('getTime')
			->willReturn(12345);

		$this->oldPasswordMapper->expects($this->once())
			->method('insert')
			->with($this->callback(function(OldPassword $oldPassword){
				return $oldPassword->getPassword() === 'somehash' &&
					$oldPassword->getUid() === 'testuid' &&
					$oldPassword->getChangeTime() === 12345;
			}));

		$event = new GenericEvent(null, [
			'uid' => 'testuid',
			'password' => 'secret'
		]);
		$this->handler->savePasswordForCreatedUser($event);
	}

	public function testSaveOldPasswordClearingNotifications() {
		/** @var IUser | \PHPUnit_Framework_MockObject_MockObject $user */
		$user = $this->createMock(IUser::class);
		$user->method('getUID')->willReturn('testuid');

		$this->hasher->expects($this->once())
			->method('hash')
			->with('secret')
			->willReturn('somehash');

		$this->timeFactory->expects($this->once())
			->method('getTime')
			->willReturn(12345);

		$this->oldPasswordMapper->expects($this->once())
			->method('insert')
			->with($this->callback(function(OldPassword $oldPassword){
				return $oldPassword->getPassword() === 'somehash' &&
					$oldPassword->getUid() === 'testuid' &&
					$oldPassword->getChangeTime() === 12345;
			}));

		$this->unConfigHandler->method('getMarkAboutToExpireNotificationSentFor')
			->willReturn('222');
		$this->unConfigHandler->method('getMarkExpiredNotificationSentFor')
			->willReturn('333');

		$this->unConfigHandler->expects($this->once())
			->method('resetExpirationMarks');

		$this->manager->expects($this->exactly(2))
			->method('markProcessed')
			->withConsecutive(
				[
					$this->callback(function($notif) {
						return $notif->getApp() === 'password_policy' &&
							$notif->getUser() === 'testuid' &&
							$notif->getObjectType() === 'about_to_expire' &&
							$notif->getObjectId() === '222';
					})
				],
				[
					$this->callback(function($notif) {
						return $notif->getApp() === 'password_policy' &&
							$notif->getUser() === 'testuid' &&
							$notif->getObjectType() === 'expired' &&
							$notif->getObjectId() === '333';
					})
				]
			);

		$event = new GenericEvent(null, [
			'user' => $user,
			'password' => 'secret'
		]);
		$this->handler->saveOldPassword($event);
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testGetUser() {
		$event = new GenericEvent(null, [
			'user' => null, // missing user should throw exception
			'password' => 'secret'
		]);
		self::invokePrivate($this->handler, 'getUser', [$event]);
	}

	public function testCheckPasswordExpired() {
		/** @var IUser | \PHPUnit_Framework_MockObject_MockObject $user */
		$user = $this->createMock(IUser::class);
		$user->method('getUID')->willReturn('testuid');
		$this->engine->expects($this->once())
			->method('yes')
			->with('spv_user_password_expiration_checked')
			->willReturn(true);
		$this->engine->expects($this->once())
			->method('getConfigValue')
			->with('spv_user_password_expiration_value')
			->willReturn('3'); // days
		$this->passwordExpiredRule->expects($this->once())
			->method('verify')
			->with('secret', '3', 'testuid')
			->willThrowException(new PolicyException());

		$this->session->expects($this->once())
			->method('set')
			->with('password_policy.forcePasswordChange', true);

		$event = new GenericEvent(null, [
			'user' => $user,
			'password' => 'secret'
		]);
		$this->handler->checkPasswordExpired($event);
	}

	public function checkAdminRequestedPasswordChangeProvider() {
		return [
			[false, 1530180780],
			[true, 1530180781],
		];
	}

	/**
	 * @dataProvider checkAdminRequestedPasswordChangeProvider
	 * @param $expired
	 * @param $time
	 */
	public function testCheckAdminRequestedPasswordChange($expired, $time) {
		/** @var IUser | \PHPUnit_Framework_MockObject_MockObject $user */
		$user = $this->createMock(IUser::class);
		$user->method('getUID')->willReturn('testuid');
		$this->config->expects($this->once())
			->method('getUserValue')
			->with('testuid', 'password_policy', 'forcePasswordChange', '')
			->willReturn('2018-06-28T10:13:00Z'); // = 1530180780

		$this->timeFactory->expects($this->once())
			->method('getTime')
			->willReturn($time);

		if ($expired) {
			$this->session
				->expects($this->once())
				->method('set')
				->with('password_policy.forcePasswordChange', true);
		} else {
			$this->session
				->expects($this->never())
				->method('set');
		}

		$event = new GenericEvent(null, [
			'user' => $user,
			'password' => 'secret'
		]);
		$this->handler->checkAdminRequestedPasswordChange($event);
	}

	public function testCheckForcePasswordChangeOnFirstLogin() {
		/** @var IUser | \PHPUnit_Framework_MockObject_MockObject $user */
		$user = $this->createMock(IUser::class);
		$this->engine->expects($this->once())
			->method('yes')
			->with('spv_user_password_force_change_on_first_login_checked')
			->willReturn(true);

		$this->session->expects($this->once())
			->method('set')
			->with('password_policy.forcePasswordChange', true);

		$event = new GenericEvent($user);
		$this->handler->checkForcePasswordChangeOnFirstLogin($event);
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage 'foo' is not an instance of IUser.
	 */
	public function testCheckForcePasswordChangeOnFirstLoginException() {
		$event = new GenericEvent('foo');
		$this->handler->checkForcePasswordChangeOnFirstLogin($event);
	}

	public function testRemoveUserEntriesFromTable() {
		$event = new GenericEvent(null, ['uid' => 'foo']);
		$this->oldPasswordMapper->expects($this->once())
			->method('cleanUserHistory');

		$this->handler->removeUserEntriesFromTable($event);
	}
}
