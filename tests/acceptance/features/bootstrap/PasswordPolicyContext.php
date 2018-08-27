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
use TestHelpers\SetupHelper;

require_once 'bootstrap.php';

/**
 * Context for password policy specific steps
 */
class PasswordPolicyContext implements Context {

	/**
	 * @var FeatureContext
	 */
	private $featureContext;

	/**
	 * @param string $enabledOrDisabled
	 *
	 * @return string "on" or ""
	 */
	private function appSettingIsExpectedToBe($enabledOrDisabled) {
		if ($enabledOrDisabled === "enabled") {
			return 'on';
		} else {
			return '';
		}
	}

	/**
	 * @Then /^the minimum characters password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theMinimumCharactersPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_min_chars_checked'
			)
		);
	}

	/**
	 * @Then /^the lowercase letters password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theLowercaseLettersPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_lowercase_checked'
			)
		);
	}

	/**
	 * @Then /^the uppercase letters password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theUppercaseLettersPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_uppercase_checked'
			)
		);
	}

	/**
	 * @Then /^the numbers password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theNumbersPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_numbers_checked'
			)
		);
	}

	/**
	 * @Then /^the special characters password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theSpecialCharactersPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_special_chars_checked'
			)
		);
	}

	/**
	 * @Then /^the restrict to these special characters password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theRestrictSpecialCharactersPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_def_special_chars_checked'
			)
		);
	}

	/**
	 * @Then /^the last passwords user password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theLastPasswordsPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_password_history_checked'
			)
		);
	}

	/**
	 * @Then /^the days until user password expires user password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilUserPasswordExpiresPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_user_password_expiration_checked'
			)
		);
	}

	/**
	 * @Then /^the notification days before password expires user password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theNotificationDaysBeforeUserPasswordExpiresPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_user_password_expiration_notification_checked'
			)
		);
	}

	/**
	 * @Then /^the force password change on first login user password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theForcePasswordChangeOnFirstLoginPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_user_password_force_change_on_first_login_checked'
			)
		);
	}

	/**
	 * @Then /^the days until link expires if password is set public link password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilLinkExpiresWithPasswordPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_expiration_password_checked'
			)
		);
	}

	/**
	 * @Then /^the days until link expires if password is not set public link password policy should be (enabled|disabled)$/
	 *
	 * @param string $enabledOrDisabled
	 *
	 * @return void
	 * @throws Exception
	 */
	public function theDaysUntilLinkExpiresWithoutPasswordPasswordPolicyShouldBe(
		$enabledOrDisabled
	) {
		PHPUnit_Framework_Assert::assertEquals(
			$this->appSettingIsExpectedToBe($enabledOrDisabled),
			$this->getPasswordPolicySetting(
				'spv_expiration_nopassword_checked'
			)
		);
	}

	/**
	 *
	 * @param string $setting
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getPasswordPolicySetting($setting) {
		$occResult = SetupHelper::runOcc(
			[
				'config:app:get',
				'password_policy',
				$setting
			]
		);
		if ($occResult['code'] !== "0") {
			// The setting is not set. This is expectd if settings have never
			// been saved yet. Treat this as the empty string.
			return '';
		}
		return \trim($occResult['stdOut']);
	}

	/**
	 *
	 * @param string $setting
	 * @param string $value
	 *
	 * @throws \Exception
	 * @return void
	 */
	public function setPasswordPolicySetting($setting, $value) {
		$occResult = SetupHelper::runOcc(
			[
				'config:app:set',
				'password_policy',
				$setting,
				'--value',
				$value
			]
		);
		if ($occResult['code'] !== "0") {
			throw new \Exception(
				__METHOD__ .
				"\ncould not set '$setting' for password_policy app\n" .
				"error message: " . $occResult['code']
			);
		}
	}

	/**
	 *
	 * @param string $setting
	 *
	 * @throws \Exception
	 * @return void
	 */
	public function deletePasswordPolicySetting($setting) {
		$occResult = SetupHelper::runOcc(
			[
				'config:app:delete',
				'password_policy',
				$setting
			]
		);
		if ($occResult['code'] !== "0") {
			throw new \Exception(
				__METHOD__ .
				"\ncould not delete '$setting' for password_policy app\n" .
				"error message: " . $occResult['code']
			);
		}
	}

	/**
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setUpScenario(BeforeScenarioScope $scope) {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->featureContext = $environment->getContext('FeatureContext');
		SetupHelper::init(
			$this->featureContext->getAdminUsername(),
			$this->featureContext->getAdminPassword(),
			$this->featureContext->getBaseUrl(),
			$this->featureContext->getOcPath()
		);

		// TODO: Make this more efficient. It takes time to clear all these
		//       settings one by one before every scenario.
		// TODO: Think about if we care enough to remember the current settings
		//       and put them back at the end of each scenario. That is not
		//       necessary during CI runs. But it is nice to developers who
		//       run test scenarios locally.
		$this->deletePasswordPolicySetting('spv_def_special_chars_checked');
		$this->deletePasswordPolicySetting('spv_def_special_chars_value');
		$this->deletePasswordPolicySetting('spv_expiration_nopassword_checked');
		$this->deletePasswordPolicySetting('spv_expiration_nopassword_value');
		$this->deletePasswordPolicySetting('spv_expiration_password_checked');
		$this->deletePasswordPolicySetting('spv_expiration_password_value');
		$this->deletePasswordPolicySetting('spv_lowercase_checked');
		$this->deletePasswordPolicySetting('spv_lowercase_value');
		$this->deletePasswordPolicySetting('spv_min_chars_checked');
		$this->deletePasswordPolicySetting('spv_min_chars_value');
		$this->deletePasswordPolicySetting('spv_numbers_checked');
		$this->deletePasswordPolicySetting('spv_numbers_value');
		$this->deletePasswordPolicySetting('spv_password_history_checked');
		$this->deletePasswordPolicySetting('spv_password_history_value');
		$this->deletePasswordPolicySetting('spv_special_chars_checked');
		$this->deletePasswordPolicySetting('spv_special_chars_value');
		$this->deletePasswordPolicySetting('spv_uppercase_checked');
		$this->deletePasswordPolicySetting('spv_uppercase_value');
		$this->deletePasswordPolicySetting('spv_user_password_expiration_checked');
		$this->deletePasswordPolicySetting('spv_user_password_expiration_value');
		$this->deletePasswordPolicySetting('spv_user_password_expiration_notification_checked');
		$this->deletePasswordPolicySetting('spv_user_password_expiration_notification_value');
		$this->deletePasswordPolicySetting('spv_user_password_force_change_on_first_login_checked');
	}
}
