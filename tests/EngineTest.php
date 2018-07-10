<?php
/**
 * @author Jörn Friedrich Dreyer <jfd@butonic.de>
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

namespace OCA\PasswordPolicy\Tests;

use OCA\PasswordPolicy\Engine;
use OCA\PasswordPolicy\Rules\PolicyException;
use OCP\IDBConnection;
use OCP\IL10N;
use OCP\Security\IHasher;
use OCP\Security\ISecureRandom;
use Test\TestCase;

class EngineTest extends TestCase {

	private $defaults = [
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

	/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject */
	protected $l10n;
	/** @var ISecureRandom | \PHPUnit_Framework_MockObject_MockObject */
	protected $random;
	/** @var IDBConnection | \PHPUnit_Framework_MockObject_MockObject */
	protected $db;
	/** @var IHasher | \PHPUnit_Framework_MockObject_MockObject */
	protected $hasher;

	protected function setUp() {
		$this->l10n = $this->createMock(IL10N::class);
		$this->l10n
			->method('t')
			->will($this->returnCallback(function($text, $parameters = []) {
				return \vsprintf($text, $parameters);
			}));

		$this->random = \OC::$server->getSecureRandom();
		$this->db = $this->createMock(IDBConnection::class);
		$this->hasher = $this->createMock(IHasher::class);

		return parent::setUp();
	}

	/**
	 * Creates and configures an Engine for testing. not done in setUp because
	 * the data provider only overwrites part of the data ... the rest are defaults
	 * TODO use yield to move default generation completely to data provider
	 * @param array $config
	 * @return Engine
	 */
	protected function createEngine(array $config = []) {
		return new Engine(
			\array_replace($this->defaults, $config),
			$this->l10n,
			$this->random,
			$this->db,
			$this->hasher
		);
	}

	/**
	 * @dataProvider providesTestData
	 * @param array $config
	 * @param $password
	 */
	public function testPolicyPasses(array $config, $password) {
		$engine = $this->createEngine($config);
		$engine->verifyPassword($password);
	}

	/**
	 * @dataProvider providesFailTestData
	 * @param array $config
	 * @param $password
	 * @expectedException OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testPolicyFails(array $config, $password) {
		$engine = $this->createEngine($config);
		$engine->verifyPassword($password);
	}

	/**
	 * @dataProvider providesTypes
	 * @param string $type
	 * @expectedException OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testPolicyFailsWithTypes($type) {
		$engine = $this->createEngine(['spv_min_chars_checked' => true]);
		$engine->verifyPassword('ab', null, $type);
	}

	/**
	 * @dataProvider providesTypes
	 * @expectedException OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testPolicyFailsEmptyPassword() {
		$engine = $this->createEngine(['spv_min_chars_checked' => true]);
		$engine->verifyPassword('', null, 'user');
	}

	/**
	 * @dataProvider providesFailTestData
	 * @param array $config
	 * @param $password
	 * @expectedException OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testPolicyFailsWithUserType(array $config, $password) {
		$engine = $this->createEngine($config);
		$engine->verifyPassword($password, null, 'user');
	}

	/**
	 * @dataProvider providesTestData
	 * @param array $config
	 */
	public function testPasswordGeneration(array $config) {
		$engine = $this->createEngine($config);
		$password = $engine->generatePassword();
		$engine->verifyPassword($password);
	}

	public function providesTestData() {
		return [
			[[], ''],
			[[], '1234567890'],
			[['spv_min_chars_checked' => true], '1234567890'],
			[['spv_lowercase_checked' => true], 'a234567890'],
			[['spv_uppercase_checked' => true], 'A234567890'],
			[['spv_numbers_checked' => true], '1234567890'],
			[['spv_special_chars_checked' => true], '#234567890'],
		];
	}

	public function providesFailTestData() {
		return [
			[['spv_min_chars_checked' => true], '12'],
			[['spv_lowercase_checked' => true], '1234567890'],
			[['spv_uppercase_checked' => true], '1234567890'],
			[['spv_numbers_checked' => true], 'ABCDEFGH'],
			[['spv_special_chars_checked' => true], 'ABCDEFGH'],
		];
	}

	public function providesTypes() {
		return [['user'], ['public'], ['guest'], ['unknown'], [null]];
	}

	public function testGetConfigValues() {
		$engine = $this->createEngine();
		self::assertEquals($this->defaults, $engine->getConfigValues());
	}

	public function testGetConfigValue() {
		$engine = $this->createEngine();
		foreach ($this->defaults as $key => $value) {
			self::assertEquals($value, $engine->getConfigValue($key));
		}
	}
}
