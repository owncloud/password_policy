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

use OCA\PasswordPolicy\Rules\Special;
use OCP\IL10N;

class SpecialTest extends \PHPUnit_Framework_TestCase {

	/** @var Special */
	private $r;

	public function setUp() {
		parent::setUp();

		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10n */
		$l10n = $this->getMockBuilder('\OCP\IL10N')
			->disableOriginalConstructor()->getMock();
		$l10n
			->expects($this->any())
			->method('t')
			->will($this->returnCallback(function($text, $parameters = array()) {
				return vsprintf($text, $parameters);
			}));

		$this->r = new Special($l10n);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Password contains too few special characters. Minimum 4 special characters are required.
	 */
	public function testTooShort() {
		$this->r->verify('', 4, []);
	}

	/**
	 * @dataProvider providesTestData
	 */
	public function testOkay($password, $val, $allowedSpecialChars) {
		$this->r->verify($password, $val, $allowedSpecialChars);
	}

	/**
	 * @dataProvider providesExceptionalData
	 */
	public function testInvalidSpecial($expectedMessage, $password, $val, $allowedSpecialChars) {
		try {
			$this->r->verify($password, $val, $allowedSpecialChars);
			$this->fail('');
		} catch (Exception $ex) {
			$this->assertEquals($expectedMessage, $ex->getMessage());
		}
	}

	function providesExceptionalData() {
		return [
			['Password contains invalid special characters. Only #+ are allowed.', '#+?@#+?@', 4, '#+'],
			['Password contains too few special characters. Minimum 9 special characters are required.', '#+?@#+?@', 9, []],
			['Password contains too few special characters. Minimum 10 special characters are required.', '#+?@#+?@', 10, '#+?@'],
			['Password contains too few special characters. Minimum 2 special characters are required.', '#', 2, '#!'],
			['Password contains too few special characters. Minimum 1 special characters are required.', 'qaa', 1, '#!']
		];
	}

	function providesTestData() {
		return [
			['#+?@#+?@', 6, []],
			['#+?@#+?@', 6, '#+?@'],
			['#', 1, '#!']
		];
	}
}
