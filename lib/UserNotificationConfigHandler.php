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

namespace OCA\PasswordPolicy;

use OCP\IConfig;
use OCA\PasswordPolicy\Db\OldPassword;

class UserNotificationConfigHandler {
	const DEFAULT_EXPIRATION_FOR_NORMAL_NOTIFICATION = 30 * 24 * 60 * 60;  // 30 days

	/** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	public function getExpirationTime() {
		$isChecked = $this->config->getAppValue(
			'password_policy',
			'spv_user_password_expiration_checked',
			false
		);
		if (!\filter_var($isChecked, FILTER_VALIDATE_BOOLEAN)) {
			return null;
		}

		$expirationTime = $this->config->getAppValue(
			'password_policy',
			'spv_user_password_expiration_value',
			null
		);
		if ($expirationTime === null || !\is_numeric($expirationTime)) {
			return null;  // passwords don't expire or have weird value
		}
		return \intval($expirationTime);
	}

	public function getExpirationTimeForNormalNotification() {
		$isChecked = $this->config->getAppValue(
			'password_policy',
			'spv_user_password_expiration_notification_checked',
			false
		);
		if (!\filter_var($isChecked, FILTER_VALIDATE_BOOLEAN)) {
			return null;
		}

		$expirationTime = $this->config->getAppValue(
			'password_policy',
			'spv_user_password_expiration_notification_value',
			self::DEFAULT_EXPIRATION_FOR_NORMAL_NOTIFICATION);
		if ($expirationTime === null || !\is_numeric($expirationTime)) {
			return null;  // passwords don't expire or have weird value
		}
		return \intval($expirationTime);
	}

	public function markAboutToExpireNotificationSentFor(OldPassword $passInfo) {
		$this->config->setUserValue($passInfo->getUid(), 'password_policy', 'aboutToExpireSent', $passInfo->getId());
	}

	public function markExpiredNotificationSentFor(OldPassword $passInfo) {
		$this->config->setUserValue($passInfo->getUid(), 'password_policy', 'expiredSent', $passInfo->getId());
	}

	public function isSentAboutToExpireNotification(OldPassword $passInfo) {
		$storedId = $this->config->getUserValue($passInfo->getUid(), 'password_policy', 'aboutToExpireSent', null);
		if ($storedId === null) {
			return false;  // notification not sent
		} elseif (\intval($storedId) !== $passInfo->getId()) {
			// if the pasword id doesn't match the one stored, the notification hasn't been sent
			return false;
		}
		return true;
	}

	public function isSentExpiredNotification(OldPassword $passInfo) {
		$storedId = $this->config->getUserValue($passInfo->getUid(), 'password_policy', 'expiredSent', null);
		if ($storedId === null) {
			return false;  // notification not sent
		} elseif (\intval($storedId) !== $passInfo->getId()) {
			// if the pasword id doesn't match the one stored, the notification hasn't been sent
			return false;
		}
		return true;
	}

	public function resetExpirationMarks($uid) {
		$targetKeys = [
			'aboutToExpireSent',
			'expiredSent',
		];
		foreach ($targetKeys as $targetKey) {
			$this->config->deleteUserValue($uid, 'password_policy', $targetKey);
		}
	}
}