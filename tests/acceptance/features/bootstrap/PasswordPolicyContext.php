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
	 *
	 * @param string $setting
	 * @param string $value
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
