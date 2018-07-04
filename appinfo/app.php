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

use OCA\PasswordPolicy\HooksHandler;

$handler = new HooksHandler();

OCP\Util::connectHook(
	'\OC\Share', 'verifyExpirationDate',
	$handler, 'updateLinkExpiry'
);

$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener(
	'OCP\User::validatePassword',
	[$handler, 'verifyPassword']
);
$eventDispatcher->addListener(
	'OCP\Share::validatePassword',
	[$handler, 'verifyPassword']
);
$eventDispatcher->addListener(
	'OCP\User::createPassword',
	[$handler, 'generatePassword']
);
$eventDispatcher->addListener(
	'user.aftersetpassword',
	[$handler, 'saveOldPassword']
);
$eventDispatcher->addListener(
	'user.afterlogin',
	[$handler, 'checkPasswordExpired']
);
$eventDispatcher->addListener(
	'user.afterlogin',
	[$handler, 'checkAdminRequestedPasswordChange']
);
$eventDispatcher->addListener(
	\OCP\IUser::class . '::firstLogin',
	[$handler, 'checkForcePasswordChangeOnFirstLogin']
);
