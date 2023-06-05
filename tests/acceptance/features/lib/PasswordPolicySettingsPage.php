<?php declare(strict_types=1);

/**
 * ownCloud
 *
 * @author Phil Davis <artur@jankaritech.com>
 * @copyright Copyright (c) 2018 Phil Davis artur@jankaritech.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Page;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use \Behat\Mink\Exception\ElementNotFoundException;

/**
 * PageObject for the password policy settings Page
 *
 */
class PasswordPolicySettingsPage extends OwncloudPage {
	/**
	 *
	 * @var string $path
	 */
	protected $path = '/index.php/settings/admin?sectionid=security';

	private $passwordPolicyFormId = 'password_policy';
	private $saveButtonValue = 'Save';

	private $policyCheckboxNames = [
		'minimumCharacters' => 'spv_min_chars_checked',
		'lowercaseLetters' => 'spv_lowercase_checked',
		'uppercaseLetters' => 'spv_uppercase_checked',
		'numbers' => 'spv_numbers_checked',
		'specialCharacters' => 'spv_special_chars_checked',
		'restrictToTheseSpecialCharacters' => 'spv_def_special_chars_checked',
		'lastPasswords' => 'spv_password_history_checked',
		'daysUntilUserPasswordExpires' => 'spv_user_password_expiration_checked',
		'notificationDaysBeforeUserPasswordExpires' => 'spv_user_password_expiration_notification_checked',
		'forcePasswordChangeOnFirstLogin' => 'spv_user_password_force_change_on_first_login_checked',
		'daysUntilLinkExpiresWithPassword' => 'spv_expiration_password_checked',
		'daysUntilLinkExpiresWithoutPassword' => 'spv_expiration_nopassword_checked',
	];
	private $policyValueNames = [
		'minimumCharacters' => 'spv_min_chars_value',
		'lowercaseLetters' => 'spv_lowercase_value',
		'uppercaseLetters' => 'spv_uppercase_value',
		'numbers' => 'spv_numbers_value',
		'specialCharacters' => 'spv_special_chars_value',
		'restrictToTheseSpecialCharacters' => 'spv_def_special_chars_value',
		'lastPasswords' => 'spv_password_history_value',
		'daysUntilUserPasswordExpires' => 'spv_user_password_expiration_value',
		'notificationDaysBeforeUserPasswordExpires' => 'spv_user_password_expiration_notification_value',
		'daysUntilLinkExpiresWithPassword' => 'spv_expiration_password_value',
		'daysUntilLinkExpiresWithoutPassword' => 'spv_expiration_nopassword_value',
	];

	/**
	 * toggle checkbox
	 *
	 * @param string $checkboxName that should be in the HTML
	 *
	 * @return NodeElement
	 * @throws ElementNotFoundException
	 */
	public function findSettingsCheckbox(string $checkboxName): NodeElement {
		$checkbox = $this->findField($checkboxName);
		if ($checkbox === null) {
			throw new ElementNotFoundException(
				__METHOD__ .
				" could not find checkbox named $checkboxName "
			);
		}
		return $checkbox;
	}

	/**
	 * toggle checkbox
	 *
	 * @param string $checkboxName that should be in the HTML
	 * @param string $action "enables|disables"
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function toggleCheckbox(
		string $checkboxName,
		string $action
	): void {
		$checkbox = $this->findSettingsCheckbox($checkboxName);
		if ($action === "disables") {
			if ($checkbox->isChecked()) {
				$checkbox->click();
			}
		} elseif ($action === "enables") {
			if ((!($checkbox->isChecked()))) {
				$checkbox->click();
			}
		} else {
			throw new \Exception(
				__METHOD__ . " invalid action: $action"
			);
		}
	}

	/**
	 * toggle policy checkbox
	 *
	 * @param string $policyCheckboxKey one of the known keys in the policyCheckboxNames array
	 * @param string $action "enables|disables"
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function togglePolicyCheckbox(
		string $policyCheckboxKey,
		string $action
	): void {
		if (\array_key_exists($policyCheckboxKey, $this->policyCheckboxNames)) {
			$this->toggleCheckbox(
				$this->policyCheckboxNames[$policyCheckboxKey],
				$action
			);
		} else {
			throw new \Exception(
				__METHOD__ . " unknown policyCheckboxKey: $policyCheckboxKey"
			);
		}
	}

	/**
	 * return the state of the policy checkbox
	 *
	 * @param string $policyCheckboxKey one of the known keys in the policyCheckboxNames array
	 *
	 * @return boolean
	 * @throws \Exception
	 */
	public function isPolicyCheckboxChecked(string $policyCheckboxKey): bool {
		if (\array_key_exists($policyCheckboxKey, $this->policyCheckboxNames)) {
			$checkbox = $this->findSettingsCheckbox(
				$this->policyCheckboxNames[$policyCheckboxKey]
			);
			return $checkbox->isChecked();
		} else {
			throw new \Exception(
				__METHOD__ . " unknown policyCheckboxKey: $policyCheckboxKey"
			);
		}
	}

	/**
	 * enter a value in the specified field
	 *
	 * @param string $policyValueKey one of the known keys in the policyValueNames array
	 * @param string $value
	 *
	 * @return void
	 * @throws ElementNotFoundException
	 */
	public function enterPolicyValue(
		string $policyValueKey,
		string $value
	): void {
		if (\array_key_exists($policyValueKey, $this->policyValueNames)) {
			$this->fillField($this->policyValueNames[$policyValueKey], $value);
		} else {
			throw new \Exception(
				__METHOD__ . " unknown policyValueKey: $policyValueKey"
			);
		}
	}

	/**
	 * get the value in the specified field
	 *
	 * @param string $policyValueKey one of the known keys in the policyValueNames array
	 *
	 * @return string value in the field
	 * @throws ElementNotFoundException
	 */
	public function getPolicyValue(string $policyValueKey): string {
		if (\array_key_exists($policyValueKey, $this->policyValueNames)) {
			$name = $this->policyValueNames[$policyValueKey];
			$field = $this->findField($name);
			if ($field === null) {
				throw new ElementNotFoundException(
					__METHOD__ .
					" could not find field with name $name "
				);
			}

			return $field->getValue();
		} else {
			throw new \Exception(
				__METHOD__ . " unknown policyValueKey: $policyValueKey"
			);
		}
	}

	/**
	 * saves the settings
	 *
	 * @param Session $session
	 *
	 * @return PasswordPolicySettingsPage
	 * @throws ElementNotFoundException
	 */
	public function saveSettings(Session $session): PasswordPolicySettingsPage {
		$saveButton = $this->findButton($this->saveButtonValue);
		if ($saveButton === null) {
			throw new ElementNotFoundException(
				__METHOD__ .
				"could not find the save-button with text $this->saveButtonValue"
			);
		}
		$saveButton->click();
		$this->waitForAjaxCallsToStartAndFinish($session);
		return $this;
	}

	/**
	 * there is no reliable loading indicator on the admin security settings page,
	 * so just wait for the password policy form to be there.
	 *
	 * @param Session $session
	 * @param int $timeout_msec
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function waitTillPageIsLoaded(
		Session $session,
		int $timeout_msec = STANDARD_UI_WAIT_TIMEOUT_MILLISEC
	): void {
		$currentTime = \microtime(true);
		$end = $currentTime + ($timeout_msec / 1000);
		while ($currentTime <= $end) {
			if ($this->findById($this->passwordPolicyFormId) !== null) {
				break;
			}
			\usleep(STANDARD_SLEEP_TIME_MICROSEC);
			$currentTime = \microtime(true);
		}

		if ($currentTime > $end) {
			throw new \Exception(
				__METHOD__ . " timeout waiting for password policy page to load"
			);
		}
		$this->waitForOutstandingAjaxCalls($session);
	}
}
