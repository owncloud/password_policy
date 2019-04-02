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
use PHPUnit\Framework\Assert;

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
	 * @When /^the administrator sets the minimum characters required to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsMinimumCharactersUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'minimumCharacters', $value
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
	 * @When /^the administrator sets the lowercase letters required to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsLowercaseLettersUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'lowercaseLetters', $value
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
	 * @When /^the administrator sets the uppercase letters required to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsUppercaseLettersUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'uppercaseLetters', $value
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
	 * @When /^the administrator sets the numbers required to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsNumbersUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'numbers', $value
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
	 * @When /^the administrator sets the special characters required to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsSpecialCharactersUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'specialCharacters', $value
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
	 * @When /^the administrator sets the restricted list of special characters to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsRestrictSpecialCharactersUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'restrictToTheseSpecialCharacters', $value
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
	 * @When /^the administrator sets the number of last passwords that should not be used to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsLastPasswordsUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'lastPasswords', $value
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
	 * @When /^the administrator sets the number of days until user password expires to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsDaysUntilUserPasswordExpiresUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'daysUntilUserPasswordExpires', $value
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
	 * @When /^the administrator sets the notification days before password expires to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsNotificationDaysBeforeUserPasswordExpiresUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'notificationDaysBeforeUserPasswordExpires', $value
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
	 * @When /^the administrator sets the number of days until link expires if password is set to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsDaysUntilLinkExpiresWithPasswordUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'daysUntilLinkExpiresWithPassword', $value
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
	 * @When /^the administrator sets the number of days until link expires if password is not set to "([^"]*)" using the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theAdminSetsDaysUntilLinkExpiresWithoutPasswordUsingTheWebui(
		$value
	) {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'daysUntilLinkExpiresWithoutPassword', $value
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'minimumCharacters'
			)
		);
	}

	/**
	 * @Then /^the required minimum characters should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theMinimumCharactersShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'lowercaseLetters'
			)
		);
	}

	/**
	 * @Then /^the required number of lowercase letters should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theLowercaseLettersShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'uppercaseLetters'
			)
		);
	}

	/**
	 * @Then /^the required number of uppercase letters should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theUppercaseLettersShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'numbers'
			)
		);
	}

	/**
	 * @Then /^the required number of numbers should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theNumbersShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'specialCharacters'
			)
		);
	}

	/**
	 * @Then /^the required number of special characters should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theSpecialCharactersShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'restrictToTheseSpecialCharacters'
			)
		);
	}

	/**
	 * @Then /^restrict to these special characters should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function restrictSpecialCharactersShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'lastPasswords'
			)
		);
	}

	/**
	 * @Then /^last passwords that should not be used should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function lastPasswordsShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'daysUntilUserPasswordExpires'
			)
		);
	}

	/**
	 * @Then /^the number of days until user password expires should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilUserPasswordExpiresShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'notificationDaysBeforeUserPasswordExpires'
			)
		);
	}

	/**
	 * @Then /^the notification days before password expires should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theNotificationDaysBeforeUserPasswordExpiresShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'daysUntilLinkExpiresWithPassword'
			)
		);
	}

	/**
	 * @Then /^the number of days until link expires if password is set should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilLinkExpiresWithPasswordShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
		Assert::assertEquals(
			$this->isExpectedToBeChecked($checkedOrUnchecked),
			$this->passwordPolicySettingsPage->isPolicyCheckboxChecked(
				'daysUntilLinkExpiresWithoutPassword'
			)
		);
	}

	/**
	 * @Then /^the number of days until link expires if password is not set should be set to "([^"]*)" on the webUI$/
	 *
	 * @param string $value
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilLinkExpiresWithoutPasswordShouldBeSetToOnTheWebui(
		$value
	) {
		Assert::assertEquals(
			$value,
			$this->passwordPolicySettingsPage->getPolicyValue(
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
