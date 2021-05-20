<?php
/**
 * @author Jan Ackermann <jackermann@owncloud.com>
 *
 * @copyright Copyright (c) 2021, ownCloud GmbH
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

namespace OCA\PasswordPolicy\Tests;

use OCA\PasswordPolicy\ConfigProvider;
use OCP\IConfig;
use Test\TestCase;

class ConfiProviderTest extends TestCase {
	/** @var IConfig|\PHPUnit\Framework\MockObject\MockObject */
	protected $config;

	/** @var ConfigProvider */
	protected $configProvider;

	protected function setUp(): void {
		parent::setUp();
		$this->config = $this->createMock(IConfig::class);
		$this->configProvider = new ConfigProvider($this->config);
	}

	public function testGetWithActiveSetting() {
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_min_chars_checked', null, 'on'],
				['password_policy', 'spv_min_chars_value', null , '8'],
			]));

		$this->assertEquals('8', $this->configProvider->get('spv_min_chars'));
	}

	public function testGetWithInactiveSetting() {
		$this->config->method('getAppValue')
			->will($this->returnValueMap([
				['password_policy', 'spv_min_chars_checked', false, ''],
				['password_policy', 'spv_min_chars_value', false, '8'],
			]));

		$this->assertEquals(null, $this->configProvider->get('spv_min_chars'));
	}
}
