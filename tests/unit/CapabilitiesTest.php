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

use OCA\PasswordPolicy\Capabilities;
use OCA\PasswordPolicy\ConfigProvider;
use Test\TestCase;

class CapabilitiesTest extends TestCase {
	/** @var ConfigProvider|\PHPUnit\Framework\MockObject\MockObject */
	protected $configProvider;

	protected function setUp(): void {
		parent::setUp();
		$this->configProvider = $this->createMock(ConfigProvider::class);
	}

	/**
	 * @dataProvider dataProviderTestData
	 * @param array $configData
	 * @param array $expected
	 */
	public function testGetCapabilities(array $configData, array $expected) {
		$this->configProvider->
			method('get')
			->will(
				$this->returnValueMap(
					$configData
				)
			);

		$capabilities = new Capabilities($this->configProvider);

		$this->assertEquals(
			$expected,
			$capabilities->getCapabilities()
		);
	}

	/**
	 * @return array
	 */
	public function dataProviderTestData(): array {
		$skeleton = 		[
			'password_policy' => [
				'password_requirements' => [
					'configuration' => [
						'lower_case' => [
							'generate_from' => [
								'characters' => 'abcdefghijklmnopqrstuvwxyz'

							]
						],
						'upper_case' => [
							'generate_from' => [
								'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
							]
						],
						'numbers' => [
							'generate_from' => [
								'characters' => '1234567890'
							]
						],
						'special_characters' => [
							'generate_from' => [
								'any' => true
							]
						],
					],

				]
			]
		];

		$case1 = $skeleton;
		$case1['password_policy']['password_requirements']['configuration']['lower_case']['minimum'] = 1;

		$case2 = $skeleton;
		$case2['password_policy']['password_requirements']['configuration']['upper_case']['minimum'] = 1;

		$case3 = $skeleton;
		$case3['password_policy']['password_requirements']['minimum_characters'] = 8;

		$case4 = $skeleton;
		$case4['password_policy']['password_requirements']['configuration']['special_characters']['minimum'] = 1;

		$case5 = $skeleton;
		$case5['password_policy']['password_requirements']['configuration']['special_characters']['generate_from'] = ['characters' => '#!'];

		return[
			[[],$skeleton],
			[[['spv_lowercase', 1]], $case1],
			[[['spv_uppercase', 1]], $case2],
			[[['spv_min_chars', 8]], $case3],
			[[['spv_special_chars', 1]], $case4],
			[[['spv_def_special_chars', '#!']], $case5],
		];
	}
}
