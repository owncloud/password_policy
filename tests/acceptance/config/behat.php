<?php
/**
 * ownCloud
 *
 * @author Phillip Davis <phil@jankaritech.com>
 * @copyright Copyright (c) 2026, ownCloud GmbH
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License,
 * as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Cjm\Behat\StepThroughExtension\ServiceContainer\StepThroughExtension;

$featureContextArgs = [
	'baseUrl' => 'http://localhost:8080',
	'adminUsername' => 'admin',
	'adminPassword' => 'admin',
	'regularUserPassword' => 123456,
	'ocPath' => 'apps/testing/api/v1/occ',
];

return (new Config())
	->withProfile(
		(new Profile(
			'default',
			[
			'autoload' => [
			'' => '%paths.base%/../features/bootstrap',
			],
			]
		))
			->withExtension(new Extension(StepThroughExtension::class))
			->withSuite(
				(new Suite('apiPasswordAddUser'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/apiPasswordAddUser')
			)
			->withSuite(
				(new Suite('apiPasswordAddUserSpecial'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/apiPasswordAddUserSpecial')
			)
			->withSuite(
				(new Suite('apiPasswordChange'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/apiPasswordChange')
			)
			->withSuite(
				(new Suite('apiPasswordChangeSpecial'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/apiPasswordChangeSpecial')
			)
			->withSuite(
				(new Suite('apiUpdateShare'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->addContext('PublicWebDavContext')
					->addContext('WebDavPropertiesContext')
					->withPaths('%paths.base%/../features/apiUpdateShare')
			)
			->withSuite(
				(new Suite('apiGuests'))
					->addContext('PasswordPolicyContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->addContext('EmailContext')
					->addContext('GuestsContext')
					->addContext('OccContext')
					->addContext('PublicWebDavContext')
					->withPaths('%paths.base%/../features/apiGuests')
			)
			->withSuite(
				(new Suite('cliPasswordAddUser'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext('OccUsersGroupsContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/cliPasswordAddUser')
			)
			->withSuite(
				(new Suite('cliPasswordChange'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext('OccUsersGroupsContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/cliPasswordChange')
			)
			->withSuite(
				(new Suite('cliPasswordExpire'))
					->addContext('PasswordPolicyContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/cliPasswordExpire')
			)
			->withSuite(
				(new Suite('webUIPasswordAddUser'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('EmailContext')
					->addContext('WebUIFilesContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('WebUIUsersContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordAddUser')
			)
			->withSuite(
				(new Suite('webUIPasswordAddUserSpecial'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('EmailContext')
					->addContext('WebUIFilesContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('WebUIUsersContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordAddUserSpecial')
			)
			->withSuite(
				(new Suite('webUIPasswordChange'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordChange')
			)
			->withSuite(
				(new Suite('webUIPasswordChangeSpecial'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordChangeSpecial')
			)
			->withSuite(
				(new Suite('webUIPasswordChangeUsersPage'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUIUsersContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordChangeUsersPage')
			)
			->withSuite(
				(new Suite('webUIPasswordChangeUsersPageSpecial'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUIUsersContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordChangeUsersPageSpecial')
			)
			->withSuite(
				(new Suite('webUIPasswordPolicySettings'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordPolicySettings')
			)
			->withSuite(
				(new Suite('webUIPasswordReset'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('EmailContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordReset')
			)
			->withSuite(
				(new Suite('webUIPublicShareLink'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('WebUISharingContext')
					->addContext('WebUIFilesContext')
					->addContext('WebUIPersonalGeneralSettingsContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPublicShareLink')
			)
			->withSuite(
				(new Suite('webUIGuests'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('WebUIFilesContext')
					->addContext('WebUIGuestsContext')
					->addContext('EmailContext')
					->addContext('GuestsContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIGuests')
			)
			->withSuite(
				(new Suite('webUIPasswordExpire'))
					->addContext('PasswordPolicyContext')
					->addContext('WebUIPasswordPolicyContext')
					->addContext('WebUIPasswordUpdateContext')
					->addContext('WebUIGeneralContext')
					->addContext('WebUILoginContext')
					->addContext('OccContext')
					->addContext(
						'FeatureContext',
						$featureContextArgs
					)
					->withPaths('%paths.base%/../features/webUIPasswordExpire')
			)
	);
