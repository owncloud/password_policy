<?php
/**
 * ownCloud
 *
 * @author Kiran Parajuli <kiran@jankaritech.com>
 * @copyright Copyright (c) 2018 Kiran Parajuli kiran@jankaritech.com
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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;
use Page\UpdatePasswordPage;
use PHPUnit\Framework\Assert;

require_once 'bootstrap.php';

/**
 * Class WebUIPasswordUpdateContext
 */
class WebUIPasswordUpdateContext extends RawMinkContext implements Context {
	/**
	 * @var FeatureContext
	 */
	private $featureContext;

	/**
	 *
	 * @var UpdatePasswordPage
	 */
	private $updatePasswordPage;

	/**
	 * WebUIPasswordUpdateContext constructor.
	 *
	 * @param UpdatePasswordPage $updatePasswordPage
	 */
	public function __construct(
		UpdatePasswordPage $updatePasswordPage
	) {
		$this->updatePasswordPage = $updatePasswordPage;
	}

	/**
	 * @When user :username enters the current password, chooses a new password :newPassword and confirms it using the webUI
	 *
	 * @param string $username
	 * @param string $newPassword
	 *
	 * @return void
	 * @throws ElementNotFoundException
	 */
	public function theUserEntersTheCurrentPasswordChoosesANewPasswordAndConfirmsItUsingTheWebui(
		string $username,
		string $newPassword
	): void {
		$currentPassword = $this->featureContext->getPasswordForUser($username);
		$this->updatePasswordPage->chooseNewPassword(
			$currentPassword,
			$newPassword,
			$newPassword,
			$this->getSession()
		);
	}

	/**
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 *
	 * @return void
	 */
	public function setUpScenario(BeforeScenarioScope $scope): void {
		$environment = $scope->getEnvironment();

		$this->featureContext = $environment->getContext('FeatureContext');
	}

	/**
	 * @When the user requests for password update with the following credentials using the webUI
	 *
	 * @param TableNode $table
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theUserRequestsForPasswordUpdateWithFollowingCredentialsUsingTheWebui(
		TableNode $table
	): void {
		$this->featureContext->verifyTableNodeRows($table, ["old_password", "new_password", "confirm_new_password"]);
		$updateData = $table->getRowsHash();
		$this->updatePasswordPage->chooseNewPassword(
			$updateData["old_password"],
			$updateData["new_password"],
			$updateData["confirm_new_password"],
			$this->getSession()
		);
	}

	/**
	 * @Then an error message with the following text should be displayed on the webUI:
	 *
	 * @param PyStringNode $string
	 *
	 * @return void
	 * @throws ElementNotFoundException
	 */
	public function anErrorMessageWithFollowingTextShouldBeDisplayedOnTheWebui(
		PyStringNode $string
	): void {
		$expectedString = $string->getRaw();
		$errorMessage = $this->updatePasswordPage->getErrorMessage();
		Assert::assertEquals(
			$expectedString,
			$errorMessage,
			__METHOD__
			. "The message expected to be displayed on the webUI was "
			. "'$expectedString', but got '$errorMessage' instead"
		);
	}

	/**
	 * @Then /^(\d+) new password fields should be highlighted with red color$/
	 *
	 * @param int $numberOfFields
	 *
	 * @return void
	 */
	public function newPasswordFieldsShouldBeHighlightedWithRedColor(
		int $numberOfFields
	): void {
		$actualCount = $this->updatePasswordPage->getNewPasswordEditFieldErrorCount();
		Assert::assertEquals($numberOfFields, $actualCount);
	}

	/**
	 * @Then /^the password update submit button should be disabled$/
	 *
	 * @return void
	 * @throws ElementNotFoundException
	 */
	public function passwordUpdateSubmitButtonShouldBeDisabled(): void {
		$disabled = $this->updatePasswordPage->getSubmitButtonDisabledAttribute();
		Assert::assertEquals("disabled", $disabled);
	}
}
