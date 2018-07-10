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

namespace OCA\PasswordPolicy\Tests;

use OCP\IConfig;
use OCA\PasswordPolicy\UserNotificationConfigHandler;
use OCA\PasswordPolicy\Db\OldPassword;
use Test\TestCase;

class UserNotificationConfigHandlerTest extends TestCase {
	/** @var IConfig */
	private $config;

	/** @var UserNotificationConfigHandler */
	private $unConfigHandler;

	protected function setUp() {
		parent::setUp();
		$this->config = $this->getMockBuilder(IConfig::class)
			->disableOriginalConstructor()
			->getMock();

		$this->unConfigHandler = new UserNotificationConfigHandler($this->config);
	}

	public function falseyValueProvider() {
		return [
			[false],
			['off'],
			['no'],
			['0'],
			['false'],
		];
	}

	/**
	 * @dataProvider falseyValueProvider
	 */
	public function testExpirationTimeNotChecked($falseyValue) {
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, $falseyValue]
			]));
		$this->assertNull($this->unConfigHandler->getExpirationTime());
	}

	public function testExpirationTimeWeirdValue() {
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, true],
				['password_policy', 'spv_user_password_expiration_value', null, 'wwwww']
			]));
		$this->assertNull($this->unConfigHandler->getExpirationTime());
	}

	public function testExpirationTime() {
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_checked', false, true],
				['password_policy', 'spv_user_password_expiration_value', null, '12234']
			]));
		$this->assertEquals(12234, $this->unConfigHandler->getExpirationTime());
	}

	/**
	 * @dataProvider falseyValueProvider
	 */
	public function testExpirationTimeForNotificationNotChecked($falseyValue) {
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_notification_checked', false, $falseyValue]
			]));
		$this->assertNull($this->unConfigHandler->getExpirationTimeForNormalNotification());
	}

	public function testExpirationTimeForNotificationWeirdValue() {
		$expectedDefault = UserNotificationConfigHandler::DEFAULT_EXPIRATION_FOR_NORMAL_NOTIFICATION;
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_notification_checked', false, true],
				['password_policy', 'spv_user_password_expiration_notification_value', $expectedDefault, 'wwwww']
			]));
		$this->assertNull($this->unConfigHandler->getExpirationTimeForNormalNotification());
	}

	public function testExpirationTimeForNotification() {
		$expectedDefault = UserNotificationConfigHandler::DEFAULT_EXPIRATION_FOR_NORMAL_NOTIFICATION;
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_user_password_expiration_notification_checked', false, true],
				['password_policy', 'spv_user_password_expiration_notification_value', $expectedDefault, '12234']
			]));
		$this->assertEquals(12234, $this->unConfigHandler->getExpirationTimeForNormalNotification());
	}

	public function testMarkAboutToExpireNotificationSentFor() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);

		$this->config->expects($this->once())
			->method('setUserValue')
			->with('usertest1', 'password_policy', 'aboutToExpireSent', 34);

		$this->unConfigHandler->markAboutToExpireNotificationSentFor($oldPass);
	}

	public function testMarkExpiredNotificationSentFor() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);

		$this->config->expects($this->once())
			->method('setUserValue')
			->with('usertest1', 'password_policy', 'expiredSent', 34);

		$this->unConfigHandler->markExpiredNotificationSentFor($oldPass);
	}

	public function testIsSentAboutToExpireNotificationNotSent() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);
		$this->config->method('getUserValue')
			->will($this->returnValueMap([
				['usertest1', 'password_policy', 'aboutToExpireSent', null, null],
			]));
		$this->assertFalse($this->unConfigHandler->isSentAboutToExpireNotification($oldPass));
	}

	public function testIsSentAboutToExpireNotificationDifferentId() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);
		$this->config->method('getUserValue')
			->will($this->returnValueMap([
				['usertest1', 'password_policy', 'aboutToExpireSent', null, 20],
			]));
		$this->assertFalse($this->unConfigHandler->isSentAboutToExpireNotification($oldPass));
	}

	public function testIsSentAboutToExpireNotificationAlreadySent() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);
		$this->config->method('getUserValue')
			->will($this->returnValueMap([
				['usertest1', 'password_policy', 'aboutToExpireSent', null, 34],
			]));
		$this->assertTrue($this->unConfigHandler->isSentAboutToExpireNotification($oldPass));
	}

	public function testIsSentExpiredNotificationNotSent() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);
		$this->config->method('getUserValue')
			->will($this->returnValueMap([
				['usertest1', 'password_policy', 'expiredSent', null, null],
			]));
		$this->assertFalse($this->unConfigHandler->isSentExpiredNotification($oldPass));
	}

	public function testIsSentExpiredNotificationDifferentId() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);
		$this->config->method('getUserValue')
			->will($this->returnValueMap([
				['usertest1', 'password_policy', 'expiredSent', null, 20],
			]));
		$this->assertFalse($this->unConfigHandler->isSentExpiredNotification($oldPass));
	}

	public function testIsSentExpiredNotificationAlreadySent() {
		$passData = [
			'id' => 34,
			'uid' => 'usertest1',
			'password' => 'password',
			'changeTime' => 1002030,
		];
		$oldPass = OldPassword::fromRow($passData);
		$this->config->method('getUserValue')
			->will($this->returnValueMap([
				['usertest1', 'password_policy', 'expiredSent', null, 34],
			]));
		$this->assertTrue($this->unConfigHandler->isSentExpiredNotification($oldPass));
	}

	public function testResetExpirationMarks() {
		$targetKeys = [
			'aboutToExpireSent',
			'expiredSent',
		];
		$this->config->expects($this->exactly(\count($targetKeys)))
			->method('deleteUserValue')
			->withConsecutive(
				[$this->equalTo('usertest1'), $this->equalTo('password_policy'), $this->equalTo($targetKeys[0])],
				[$this->equalTo('usertest1'), $this->equalTo('password_policy'), $this->equalTo($targetKeys[1])]
			);
		$this->unConfigHandler->resetExpirationMarks('usertest1');
	}
}