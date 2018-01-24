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

use OCA\PasswordPolicy\Engine;
use OCP\IL10N;

class EngineTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider providesTestData
	 * @param array $config
	 * @param $password
	 */
	public function testOkay(array $config, $password) {
		$default = [
			'spv_min_chars_checked' => false,
			'spv_min_chars_value' => 8,
			'spv_lowercase_checked' => false,
			'spv_lowercase_value' => 1,
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
		];

		$config = array_replace($default, $config);

		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10n */
		$l10n = $this->getMockBuilder(IL10N::class)
				->disableOriginalConstructor()->getMock();
		$l10n
			->expects($this->any())
					->method('t')
					->will($this->returnCallback(function($text, $parameters = array()) {
							return vsprintf($text, $parameters);
			}));
		$random = \OC::$server->getSecureRandom();
		/** @var \OCP\IDBConnection | \PHPUnit_Framework_MockObject_MockObject $db */
		$db = $this->getMockBuilder(\OCP\IDBConnection::class)
			->disableOriginalConstructor()->getMock();
		/** @var \OCP\Security\IHasher | \PHPUnit_Framework_MockObject_MockObject $hasher */
		$hasher = $this->getMockBuilder(\OCP\Security\IHasher::class)
			->disableOriginalConstructor()->getMock();
		$r = new Engine($config, $l10n, $random, $db, $hasher);
		$r->verifyPassword($password);
	}

	public function providesTestData() {
		return [
			[[], ''],
			[['spv_min_chars_checked' => true], '1234567890'],
			[['spv_lowercase_checked' => true], 'a234567890'],
			[['spv_uppercase_checked' => true], 'A234567890'],
			[['spv_numbers_checked' => true], '1234567890'],
			[['spv_special_chars_checked' => true], '#234567890'],
		];
	}

	/**
	 * @dataProvider providesTestData
	 * @param array $config
	 */
	public function testPasswordGeneration(array $config) {
		$default = [
			'spv_min_chars_checked' => false,
			'spv_min_chars_value' => 8,
			'spv_lowercase_checked' => false,
			'spv_lowercase_value' => 1,
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
		];

		$config = array_replace($default, $config);

		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10n */
		$l10n = $this->getMockBuilder(IL10N::class)
			->disableOriginalConstructor()->getMock();
		$l10n
			->expects($this->any())
			->method('t')
			->will($this->returnCallback(function($text, $parameters = array()) {
				return vsprintf($text, $parameters);
			}));
		$random = \OC::$server->getSecureRandom();
		/** @var \OCP\IDBConnection | \PHPUnit_Framework_MockObject_MockObject $db */
		$db = $this->getMockBuilder(\OCP\IDBConnection::class)
			->disableOriginalConstructor()->getMock();
		/** @var \OCP\Security\IHasher | \PHPUnit_Framework_MockObject_MockObject $hasher */
		$hasher = $this->getMockBuilder(\OCP\Security\IHasher::class)
			->disableOriginalConstructor()->getMock();
		$r = new Engine($config, $l10n, $random, $db, $hasher);
		$password = $r->generatePassword();
		$r->verifyPassword($password);
	}
}
