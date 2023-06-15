<?php declare(strict_types=1);
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
use Behat\Mink\Exception\ElementNotFoundException;
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
	 *
	 * @var  $webUILoginContext;
	 */
	private $webUILoginContext;
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
	public function theAdminBrowsesToTheAdminSecuritySettingsPage(): void {
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
	public function theAdminReloadsTheAdminSecuritySettingsPage(): void {
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'minimumCharacters',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'minimumCharacters',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'lowercaseLetters',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'lowercaseLetters',
			$value
		);
	}

	/**
	 * @param string $username
	 * @param string $password
	 *
	 * @return void
	 * @throws Exception
	 */
	public function loginWithExpiredPassword(
		string $username,
		string $password
	): void {
		$session = $this->getSession();
		$this->loginPage->waitTillPageIsLoaded($session);
		$this->loginPage->loginAs(
			$username,
			$password
		);
		$this->featureContext->asUser($username);
	}

	/**
	 * @Given user :username has logged in with the expired password using the webUI
	 *
	 * @param string $username
	 *
	 * @return void
	 * @throws Exception
	 */
	public function userHasLoggedInWithExpiredPasswordUsingTheWebui(string $username): void {
		$usernameActual = $this->featureContext->getActualUsername($username);
		$this->webUILoginContext->theUserBrowsesToTheLoginPage();
		$this->loginWithExpiredPassword(
			$usernameActual,
			$this->featureContext->getPasswordForUser($usernameActual)
		);
		$this->webUIGeneralContext->theUserShouldBeRedirectedToAWebUIPageWithTheTitle("%productname%");
	}

	/**
	 * @When user :username logs in with the expired password using the webUI
	 *
	 * @param string $username
	 *
	 * @return void
	 * @throws Exception
	 */
	public function userLogsInWithExpiredPasswordUsingTheWebui(string $username): void {
		$usernameActual = $this->featureContext->getActualUsername($username);
		$this->webUILoginContext->theUserBrowsesToTheLoginPage();
		$this->loginWithExpiredPassword(
			$usernameActual,
			$this->featureContext->getPasswordForUser($usernameActual)
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'uppercaseLetters',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'uppercaseLetters',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'numbers',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'numbers',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'specialCharacters',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'specialCharacters',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'restrictToTheseSpecialCharacters',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'restrictToTheseSpecialCharacters',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'lastPasswords',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'lastPasswords',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'daysUntilUserPasswordExpires',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'daysUntilUserPasswordExpires',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'notificationDaysBeforeUserPasswordExpires',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'notificationDaysBeforeUserPasswordExpires',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'forcePasswordChangeOnFirstLogin',
			$action
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'daysUntilLinkExpiresWithPassword',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'daysUntilLinkExpiresWithPassword',
			$value
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
		string $action
	): void {
		$this->passwordPolicySettingsPage->togglePolicyCheckbox(
			$this->getSession(),
			'daysUntilLinkExpiresWithoutPassword',
			$action
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
		string $value
	): void {
		$this->passwordPolicySettingsPage->enterPolicyValue(
			'daysUntilLinkExpiresWithoutPassword',
			$value
		);
	}

	/**
	 * @When /^the administrator saves the password policy settings using the webUI$/
	 *
	 * @return void
	 * @throws ElementNotFoundException
	 */
	public function theAdminSavesThePasswordPolicySettingsUsingTheWebui(): void {
		$this->passwordPolicySettingsPage->saveSettings(
			$this->getSession()
		);
	}

	/**
	 * @param string $checkedOrUnchecked
	 *
	 * @return bool
	 */
	private function isExpectedToBeChecked(string $checkedOrUnchecked): bool {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
	public function theNumbersCheckboxShouldBeOnTheWebui(string $checkedOrUnchecked): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
		string $checkedOrUnchecked
	): void {
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
		string $value
	): void {
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
	public function setUpScenario(BeforeScenarioScope $scope): void {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->featureContext = $environment->getContext('FeatureContext');
		$this->webUIGeneralContext = $environment->getContext('WebUIGeneralContext');
		$this->webUILoginContext = $environment->getContext('WebUILoginContext');
	}
}
