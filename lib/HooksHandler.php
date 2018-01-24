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

namespace OCA\PasswordPolicy;

use OCA\PasswordPolicy\Db\OldPassword;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCP\IUser;

class HooksHandler {

	public static function generatePassword() {
		$configValues = self::loadConfiguration();

		$engine = new Engine($configValues,
			\OC::$server->getL10NFactory()->get('password_policy'),
			\OC::$server->getSecureRandom(),
			\OC::$server->getDatabaseConnection(),
			\OC::$server->getHasher()
		);

		return $engine->generatePassword();
	}

	public static function verifyPassword($password, $uid = NULL) {

		$configValues = self::loadConfiguration();

		$engine = new Engine($configValues,
			\OC::$server->getL10NFactory()->get('password_policy'),
			\OC::$server->getSecureRandom(),
			\OC::$server->getDatabaseConnection(),
			\OC::$server->getHasher()
			);

		$engine->verifyPassword($password, $uid);
	}

	public static function updateLinkExpiry($params) {
		$configValues = self::loadConfiguration();

		$date = null;
		$days = null;

		if ($params['passwordSet'] === true) {
			if ($configValues['spv_expiration_password_checked'] === 'on') {
				$days = $configValues['spv_expiration_password_value'];
				$date = new \DateTime();
				$date->setTime(0,0,0);
				$date->add(new \DateInterval('P'.$days.'D'));
			}
		} else {
			if ($configValues['spv_expiration_nopassword_checked'] === 'on') {
				$days = $configValues['spv_expiration_nopassword_value'];
				$date = new \DateTime();
				$date->setTime(0,0,0);
				$date->add(new \DateInterval('P'.$days.'D'));
			}
		}

		if ($date !== null) {
			$l = \OC::$server->getL10NFactory()->get('password_policy');
			if ($params['expirationDate'] === null) {
				$params['accepted'] = false;
				$params['message'] = (string) $l->t('Expiration date is required');
			}

			// $date is the max expiration date
			if ($date < $params['expirationDate']) {
				$params['accepted'] = false;
				$params['message'] = (string) $l->n('Expiration date cannot exceed %n day', 'Expiration date cannot exceed %n days', $days);
			}
		}
	}

	/**
	 * @return array
	 */
	private static function loadConfiguration() {
		$appValues = [
			'spv_min_chars_checked' => false,
			'spv_min_chars_value' => 8,
			'spv_uppercase_checked' => false,
			'spv_uppercase_value' => 1,
			'spv_numbers_checked' => false,
			'spv_numbers_value' => 1,
			'spv_special_chars_checked' => false,
			'spv_special_chars_value' => 1,
			'spv_def_special_chars_checked' => false,
			'spv_def_special_chars_value' => '#!',
			'spv_password_history_checked' => false,
			'spv_password_history_value' => 3,
			'spv_expiration_password_checked' => false,
			'spv_expiration_password_value' => 7,
			'spv_expiration_nopassword_checked' => false,
			'spv_expiration_nopassword_value' => 7,
		];

		$configValues = [];
		foreach ($appValues as $key => $default) {
			$configValues[$key] = \OC::$server->getConfig()->getAppValue('password_policy', $key, $default);
		}
		return $configValues;
	}

	/**
	 * @param IUser  $user
	 * @param string $password
	 */
	public static function saveOldPassword($user, $password) {
		$dbMapper = new OldPasswordMapper(\OC::$server->getDatabaseConnection());
		$oldPassword = new OldPassword();
		$oldPassword->setUid($user->getUID());
		$oldPassword->setPassword(\OC::$server->getHasher()->hash($password));
		$oldPassword->setChangeTime(\OC::$server->getTimeFactory()->getTime());
		$dbMapper->insert($oldPassword);
	}
}
