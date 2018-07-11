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

use OCA\PasswordPolicy\Notifier;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OC\L10N\L10N;
use Test\TestCase;

class NotifierTest extends TestCase {
	/** @var IFactory */
	private $factory;

	/** @var ITimeFactory */
	private $timeFactory;

	/** @var Notifier */
	private $notifier;

	protected function setUp() {
		parent::setUp();
		$this->factory = $this->getMockBuilder(IFactory::class)
			->disableOriginalConstructor()
			->getMock();
		$this->timeFactory = $this->getMockBuilder(ITimeFactory::class)
			->disableOriginalConstructor()
			->getMock();

		$l10n = $this->getMockBuilder(L10N::class)
			->disableOriginalConstructor()
			->getMock();
		$l10n->method('t')
			->will($this->returnCallback(function ($text, $parameters = []) {
				return \vsprintf($text, $parameters);
			}));

		$this->factory->method('get')->willReturn($l10n);

		$this->notifier = new Notifier($this->factory, $this->timeFactory);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testPrepareInvalidApp() {
		$notification = $this->createMock(INotification::class);
		$notification->method('getApp')->willReturn('another');
		$notification->method('getObjectType')->willReturn('local_share');
		$this->notifier->prepare($notification, 'en_US');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testPrepareInvalidUnknownObjectType() {
		$notification = $this->createMock(INotification::class);
		$notification->method('getApp')->willReturn('password_policy');
		$notification->method('getObjectType')->willReturn('local_share');
		$this->notifier->prepare($notification, 'en_US');
	}

	public function testPrepareAboutToExpire() {
		$initialTime = 1531232050;
		$expireIn = 10 * 24 * 60 * 60;  // 10 days;
		$notification = $this->createMock(INotification::class);
		$notification->method('getApp')->willReturn('password_policy');
		$notification->method('getObjectType')->willReturn('about_to_expire');
		$notification->method('getSubjectParameters')->willReturn([$initialTime, $expireIn]);
		// first parameter is the password change time and the second is the
		// expiration time starting from the password change time
		$notification->method('getMessageParameters')->willReturn([$initialTime, $expireIn]);

		$this->timeFactory->method('getTime')->willReturn($initialTime + (7 * 24 * 60 * 60));

		$notification->expects($this->once())
			->method('setParsedSubject')
			->with('Your password is about to expire!');

		$notification->expects($this->once())
			->method('setParsedMessage')
			->with('You have 3 days to change your password');

		$this->notifier->prepare($notification, 'en_US');
	}

	public function testPrepareAboutToExpirePassDate() {
		$initialTime = 1531232050;
		$expireIn = 10 * 24 * 60 * 60;  // 10 days;
		$notification = $this->createMock(INotification::class);
		$notification->method('getApp')->willReturn('password_policy');
		$notification->method('getObjectType')->willReturn('about_to_expire');
		$notification->method('getSubjectParameters')->willReturn([$initialTime, $expireIn]);
		// first parameter is the password change time and the second is the
		// expiration time starting from the password change time
		$notification->method('getMessageParameters')->willReturn([$initialTime, $expireIn]);

		$this->timeFactory->method('getTime')->willReturn($initialTime + (12 * 24 * 60 * 60));

		$notification->expects($this->once())
			->method('setParsedSubject')
			->with('Your password is about to expire!');

		$notification->expects($this->once())
			->method('setParsedMessage')
			->with('Your password expired 2 days ago');

		$this->notifier->prepare($notification, 'en_US');
	}

	public function testPrepareExpired() {
		$initialTime = 1531232050;
		$expireIn = 10 * 24 * 60 * 60;  // 10 days;
		$notification = $this->createMock(INotification::class);
		$notification->method('getApp')->willReturn('password_policy');
		$notification->method('getObjectType')->willReturn('expired');
		$notification->method('getSubjectParameters')->willReturn([$initialTime, $expireIn]);
		// first parameter is the password change time and the second is the
		// expiration time starting from the password change time
		$notification->method('getMessageParameters')->willReturn([$initialTime, $expireIn]);

		$this->timeFactory->method('getTime')->willReturn($initialTime + (12 * 24 * 60 * 60));

		$notification->expects($this->once())
			->method('setParsedSubject')
			->with('Your password has expired');

		$notification->expects($this->once())
			->method('setParsedMessage')
			->with('You have to change your password before you can access again');

		$this->notifier->prepare($notification, 'en_US');
	}
}
