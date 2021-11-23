<?php declare(strict_types=1);

/**
 * ownCloud
 *
 * @author Kiran Parajuli <kiran@jankaritech.com>
 * @copyright Copyright (c) 2020 Kiran Parajuli kiran@jankaritech.com
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

use Behat\Mink\Session;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * PageObject for the password policy settings Page
 *
 */
class UpdatePasswordPage extends OwncloudPage {
	/**
	 * @var string path
	 */
	protected $path = '/index.php/apps/password_policy/update_password?redirect_url=/coreAgain/index.php/apps/files/';

	private $currentPasswordInputId = "current_password";
	private $newPasswordInputId = "new_password";
	private $confirmNewPasswordInputId = "confirm_password";
	private $chooseNewPasswordFormXpath = "//form[@id='password_policy']";
	private $submitUpdateId = "submit";
	private $errorMessageId = "error";
	private $passwordMissMatchSelector = ".password-mismatch";

	/**
	 * choose new password for user after password expiration
	 *
	 * @param string $currentPassword
	 * @param string $newPassword
	 * @param string $confirmPassword
	 * @param Session $session
	 *
	 * @return void
	 * @throws ElementNotFoundException
	 */
	public function chooseNewPassword(
		string $currentPassword,
		string $newPassword,
		string $confirmPassword,
		Session $session
	): void {
		$form = $this->waitTillElementIsNotNull($this->chooseNewPasswordFormXpath);
		$this->assertElementNotNull(
			$form,
			__METHOD__ .
			" xpath $this->chooseNewPasswordFormXpath " .
			'could not find choose a new password form.'
		);
		$this->fillField($this->currentPasswordInputId, $currentPassword);
		$this->fillField($this->newPasswordInputId, $newPassword);
		$this->fillField($this->confirmNewPasswordInputId, $confirmPassword);
		$this->findById($this->submitUpdateId)->click();
		$this->waitForAjaxCallsToStartAndFinish($session);
	}

	/**
	 * saves the settings
	 *
	 * @param Session $session
	 *
	 * @return UpdatePasswordPage
	 * @throws ElementNotFoundException
	 */
	public function saveSettings(Session $session): UpdatePasswordPage {
		$saveButton = $this->findById($this->submitUpdateId);
		if ($saveButton === null) {
			throw new ElementNotFoundException(
				__METHOD__ .
				"could not find password save button."
			);
		}
		$saveButton->click();
		$this->waitForAjaxCallsToStartAndFinish($session);
		return $this;
	}

	/**
	 * Returns update password error message
	 *
	 * @return string
	 * @throws ElementNotFoundException
	 */
	public function getErrorMessage(): string {
		$errorMessage = $this->findById($this->errorMessageId);
		if ($errorMessage === null) {
			throw new ElementNotFoundException(
				__METHOD__ .
				" could not find error message."
			);
		}
		return $errorMessage->getText();
	}

	/**
	 * Returns no of error labelled fields
	 *
	 * @return int
	 */
	public function getNewPasswordEditFieldErrorCount(): int {
		$errorFields = $this->findAll("css", $this->passwordMissMatchSelector);
		return \count($errorFields);
	}

	/**
	 * Returns the value of the disabled attribute of the submit button
	 *
	 * @return string
	 * @throws ElementNotFoundException
	 */
	public function getSubmitButtonDisabledAttribute():string {
		$submitButton = $this->findById($this->submitUpdateId);
		if ($submitButton === null) {
			throw new ElementNotFoundException(
				__METHOD__ .
				"could not find the save-button."
			);
		}
		return $submitButton->getAttribute("disabled");
	}
}
