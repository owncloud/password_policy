<?php
/**
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

use Symfony\Component\EventDispatcher\GenericEvent;
use OCA\PasswordPolicy\HooksHandler;

OCP\Util::connectHook('\OC\Share', 'verifyExpirationDate', \OCA\PasswordPolicy\HooksHandler::class, 'updateLinkExpiry');

$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener(
	'OCP\User::validatePassword',
	function($event) {
		if ($event instanceof GenericEvent) {
			HooksHandler::verifyPassword(
				$event->getArguments()['password'],
				$event->getArguments()['uid']
			);
		}
	}
);
$eventDispatcher->addListener(
	'OCP\Share::validatePassword',
	function($event) {
		if ($event instanceof GenericEvent) {
			HooksHandler::verifyPassword($event->getArguments()['password']);
		}
	}
);
$eventDispatcher->addListener(
	'OCP\User::createPassword',
	function($event) {
		if ($event instanceof GenericEvent) {
			$event['password'] = HooksHandler::generatePassword();
			$event->stopPropagation();
		}
	}
);
\OC::$server->getEventDispatcher()->addListener(
	'user.aftersetpassword',
	function (GenericEvent $event) {
		HooksHandler::saveOldPassword(
			$event->getArgument('user'),
			$event->getArgument('password')
		);
	}
);
