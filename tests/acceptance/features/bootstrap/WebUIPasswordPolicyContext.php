<?php
/**
 * ownCloud
 *
 * @author Phil Davis <info@jankaritech.com>
 * @copyright Copyright (c) 2018 Phil Davis info@jankaritech.com
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
use Behat\MinkExtension\Context\RawMinkContext;
use Page\OwncloudPage;
use Page\PasswordPolicySettingsPage;
use Page\LoginPage;

require_once 'bootstrap.php';

/**
 * Context for password policy specific webUI steps
 */
class WebUIPasswordPolicyContext extends RawMinkContext implements Context {

	/**
	 * @var FeatureContext
	 */
	private $featureContext;
	
	/**
	 *
	 * @var WebUIGeneralContext
	 */
	private $webUIGeneralContext;

	/**
	 *
	 * @var OwncloudPage
	 */
	private $owncloudPage;

	/**
	 *
	 * @var PasswordPolicySettingsPage
	 */
	private $passwordPolicySettingsPage;

	/**
	 *
	 * @var LoginPage
	 */
	private $loginPage;
	/**
	 * WebUIPasswordPolicyContext constructor.
	 *
	 * @param OwncloudPage $owncloudPage
	 * @param PasswordPolicySettingsPage $passwordPolicySettingsPage
	 * @param LoginPage $loginPage
	 */
	public function __construct(
		OwncloudPage $owncloudPage,
		PasswordPolicySettingsPage $passwordPolicySettingsPage,
		LoginPage $loginPage
	) {
		$this->owncloudPage = $owncloudPage;
		$this->passwordPolicySettingsPage = $passwordPolicySettingsPage;
		$this->loginPage = $loginPage;
	}

	/**
	 * @When the administrator browses to the admin security settings page
	 * @Given the administrator has browsed to the admin security settings page
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminBrowsesToTheAdminSecuritySettingsPage() {
		$this->webUIGeneralContext->adminLogsInUsingTheWebUI();
		$this->theAdminReloadsTheAdminSecuritySettingsPage();
	}

	/**
	 * @When the administrator reloads the admin security settings page
	 * @Given the administrator has reloaded the admin security settings page
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminReloadsTheAdminSecuritySettingsPage() {
		$this->passwordPolicySettingsPage->open();
		$this->passwordPolicySettingsPage->waitTillPageIsLoaded(
			$this->getSession()
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the minimum characters password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesMinimumCharactersPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'minimumCharacters', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the lowercase letters password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesLowercaseLettersPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'lowercaseLetters', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the uppercase letters password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesUppercaseLettersPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'uppercaseLetters', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the numbers password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesNumbersPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'numbers', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the special characters password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesSpecialCharactersPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'specialCharacters', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the restrict to these special characters password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesRestrictSpecialCharactersPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'restrictToTheseSpecialCharacters', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the last passwords user password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesLastPasswordsPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'lastPasswords', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the days until user password expires user password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesDaysUntilUserPasswordExpiresPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'daysUntilUserPasswordExpires', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the notification days before password expires user password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesNotificationDaysBeforeUserPasswordExpiresPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'notificationDaysBeforeUserPasswordExpires', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the force password change on first login user password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesForcePasswordChangeOnFirstLoginPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'forcePasswordChangeOnFirstLogin', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the days until link expires if password is set public link password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesDaysUntilLinkExpiresWithPasswordPublicLinkPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'daysUntilLinkExpiresWithPassword', $action
		);
	}

	/**
	 * @When /^the administrator (enables|disables) the days until link expires if password is not set public link password policy using the webUI$/
	 *
	 * @param string $action
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminTogglesDaysUntilLinkExpiresWithoutPasswordPublicLinkPasswordPolicyUsingTheWebui(
		$action
	) {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			'daysUntilLinkExpiresWithoutPassword', $action
		);
	}

	/**
	 * @When /^the administrator saves the password policy settings using the webUI$/
	 *
	 * @return void
	 */
	public function theAdminSavesThePasswordPolicySettingsUsingTheWebui() {
		$this->passwordPolicySettingsPage->saveSettings(
			$this->getSession()
		);
	}

	/**
	 * @param string $checkedOrUnchecked
	 *
	 * @return bool
	 */
	private function isExpectedToBeChecked($checkedOrUnchecked) {
		return ($checkedOrUnchecked === "checked");
	}

	/**
	 * @Then /^the minimum characters password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theMinimumCharactersCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'minimumCharacters'
			)
		);
	}

	/**
	 * @Then /^the lowercase letters password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theLowercaseLettersCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'lowercaseLetters'
			)
		);
	}

	/**
	 * @Then /^the uppercase letters password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theUppercaseLettersCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'uppercaseLetters'
			)
		);
	}

	/**
	 * @Then /^the numbers password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theNumbersCheckboxShouldBeOnTheWebui($checkedOrUnchecked) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'numbers'
			)
		);
	}

	/**
	 * @Then /^the special characters password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theSpecialCharactersCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'specialCharacters'
			)
		);
	}

	/**
	 * @Then /^the restrict to these special characters password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theRestrictSpecialCharactersCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'restrictToTheseSpecialCharacters'
			)
		);
	}

	/**
	 * @Then /^the last passwords user password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theLastPasswordsCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'lastPasswords'
			)
		);
	}

	/**
	 * @Then /^the days until user password expires user password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilUserPasswordExpiresCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'daysUntilUserPasswordExpires'
			)
		);
	}

	/**
	 * @Then /^the notification days before password expires user password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theNotificationDaysBeforeUserPasswordExpiresCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'notificationDaysBeforeUserPasswordExpires'
			)
		);
	}

	/**
	 * @Then /^the force password change on first login user password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theForcePasswordChangeOnFirstLoginCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'forcePasswordChangeOnFirstLogin'
			)
		);
	}

	/**
	 * @Then /^the days until link expires if password is set public link password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilLinkExpiresWithPasswordCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'daysUntilLinkExpiresWithPassword'
			)
		);
	}

	/**
	 * @Then /^the days until link expires if password is not set public link password policy checkbox should be (checked|unchecked) on the webUI$/
	 *
	 * @param string $checkedOrUnchecked
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilLinkExpiresWithoutPasswordCheckboxShouldBeOnTheWebui(
		$checkedOrUnchecked
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'daysUntilLinkExpiresWithoutPassword'
			)
		);
	}

	/**
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 *
	 * @return void
	 */
	public function setUpScenario(BeforeScenarioScope $scope) {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->featureContext = $environment->getContext('FeatureContext');
		$this->webUIGeneralContext = $environment->getContext('WebUIGeneralContext');
	}
}
