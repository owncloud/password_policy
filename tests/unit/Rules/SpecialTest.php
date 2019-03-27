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

namespace OCA\PasswordPolicy\Tests\Rules;

use OCA\PasswordPolicy\Rules\PolicyException;
use OCA\PasswordPolicy\Rules\Special;
use OCP\IL10N;

class SpecialTest extends \PHPUnit\Framework\TestCase {

	/** @var Special */
	private $r;

	public function setUp() {
		parent::setUp();

		/** @var IL10N | \PHPUnit\Framework\MockObject\MockObject $l10n */
		$l10n = $this->createMock(IL10N::class);
		$l10n
			->method('t')
			->will($this->returnCallback(function ($text, $parameters = []) {
				return \vsprintf($text, $parameters);
			}));
		$l10n
			->method('n')
			->will($this->returnCallback(function ($text_singular, $text_plural, $count, $parameters = []) {
				if ($count === 1) {
					return (string) \vsprintf(\str_replace('%n', $count, $text_singular), $parameters);
				} else {
					return (string) \vsprintf(\str_replace('%n', $count, $text_plural), $parameters);
				}
			}));

		$this->r = new Special($l10n);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few special characters. At least 4 special characters are required.
	 */
	public function testTooShort() {
		$this->r->verify('', 4, []);
	}

	/**
	 * @dataProvider providesTestData
	 * @param $password
	 * @param $val
	 * @param $allowedSpecialChars
	 * @throws PolicyException
	 */
	public function testOkay($password, $val, $allowedSpecialChars) {
		$this->assertNull($this->r->verify($password, $val, $allowedSpecialChars));
	}

	/**
	 * @dataProvider providesExceptionalData
	 * @param $expectedMessage
	 * @param $password
	 * @param $val
	 * @param $allowedSpecialChars
	 */
	public function testInvalidSpecial($expectedMessage, $password, $val, $allowedSpecialChars) {
		try {
			$this->r->verify($password, $val, $allowedSpecialChars);
			$this->fail('');
		} catch (PolicyException $ex) {
			$this->assertEquals($expectedMessage, $ex->getMessage());
		}
	}

	public function providesExceptionalData() {
		return [
			['The password contains invalid special characters. Only #+ are allowed.', '#+?@#+?@', 4, '#+'],
			['The password contains too few special characters. At least one special character is required.', 'Abc123', 1, []],
			['The password contains too few special characters. At least 9 special characters are required.', '#+?@#+?@', 9, []],
			['The password contains too few special characters. At least 10 special characters (#+?@) are required.', '#+?@#+?@', 10, '#+?@'],
			['The password contains too few special characters. At least 2 special characters (#!) are required.', '#', 2, '#!'],
			['The password contains too few special characters. At least one special character (#!) is required.', 'qaa', 1, '#!']
		];
	}

	public function providesTestData() {
		return [
			['#+?@#+?@', 6, []],
			['#+?@#+?@', 6, '#+?@'],
			['#', 1, '#!']
		];
	}
}
