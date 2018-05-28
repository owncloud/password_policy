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

use OCA\PasswordPolicy\Rules\Uppercase;
use OCP\IL10N;
use Test\TestCase;

class UppercaseTest extends TestCase {

	/** @var Uppercase */
	private $r;

	public function setUp() {
		parent::setUp();

		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10n */
		$l10n = $this->createMock(IL10N::class);
		$l10n
			->method('t')
			->will($this->returnCallback(function($text, $parameters = array()) {
				return vsprintf($text, $parameters);
			}));

		$this->r = new Uppercase($l10n);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few uppercase characters. A minimum of 4 uppercase characters are required.
	 */
	public function testTooShort() {
		$this->r->verify('ab', 4);
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testOkay() {
		$this->r->verify('ABCFWA12345', 6);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few uppercase characters. A minimum of 5 uppercase characters are required.
	 */
	public function testSpecialUpperCaseTooShort() {
		$this->r->verify('Ññññññññññ', 5);
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testSpecialUpperCaseOkay() {
		$this->r->verify('ÑñÑñÑñÑñÑñ', 5);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few uppercase characters. A minimum of 5 uppercase characters are required.
	 */
	public function testNumericOnlyTooShort() {
		$this->r->verify('11111111', 5);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few uppercase characters. A minimum of 5 uppercase characters are required.
	 */
	public function testSpecialOnlyTooShort() {
		$this->r->verify('#+?@#+?@', 5);
	}

}
