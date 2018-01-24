<?php
/**
 *
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

namespace OCA\PasswordPolicy\Controller;

use OCP\AppFramework\Controller;
use OCP\IConfig;
use OCP\IRequest;
use OCP\Settings\ISettings;
use OCP\Template;

class SettingsController extends Controller implements ISettings {

	/** @var IRequest  */
	protected $request;
	/** @var IConfig  */
	protected $config;
	protected $appValues;

	public function __construct($appName,
								IRequest $request,
								IConfig $config) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->request = $request;

		$this->appValues = [
			'spv_min_chars_checked' => false,
			'spv_min_chars_value' => 8,
			'spv_uppercase_checked' => false,
			'spv_uppercase_value' => 1,
			'spv_numbers_checked' => false,
			'spv_numbers_value' => 1,
			'spv_special_chars_checked' => false,
			'spv_special_chars_value' => 1,
			'spv_def_special_chars_checked' => false,
			'spv_def_special_chars_value' => '#!',
			'spv_password_history_checked' => false,
			'spv_password_history_value' => 3,
			'spv_expiration_password_checked' => false,
			'spv_expiration_password_value' => 7,
			'spv_expiration_nopassword_checked' => false,
			'spv_expiration_nopassword_value' => 7,
		];
	}

	/**
	 *
	 */
	public function updatePolicy() {
		foreach ($this->appValues as $key => $default) {
			if (!is_null($this->request->getParam($key))) {
				if (substr($key, -6) === '_value' && $key !== 'spv_def_special_chars_value') {
					$value = min(max(0, (int)$this->request->getParam($key)), 255);
					$this->config->setAppValue('password_policy', $key, $value);
				} else {
					$this->config->setAppValue('password_policy', $key, strip_tags($this->request->getParam($key)));
				}
			} else {
				$this->config->setAppValue('password_policy', $key, $default);
			}
		}
	}

	public function getSectionID() {
		return 'security';
	}

	public function getPriority() {
		return 10;
	}

	public function getPanel() {
		$template = new Template('password_policy', 'admin');
		foreach($this->appValues as $key => $default) {
			$template->assign($key, $this->config->getAppValue('password_policy', $key, $default));
		}
		return $template;
	}


}
