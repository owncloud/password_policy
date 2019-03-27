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

use OCA\PasswordPolicy\Rules\Lowercase;
use OCP\IL10N;
use Test\TestCase;

class LowercaseTest extends TestCase {

	/** @var Lowercase */
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

		$this->r = new Lowercase($l10n);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few lowercase letters. At least one lowercase letter is required.
	 */
	public function testNoLowercase() {
		$this->r->verify('AB', 1);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few lowercase letters. At least 4 lowercase letters are required.
	 */
	public function testTooShort() {
		$this->r->verify('ab', 4);
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testOkay() {
		$this->assertNull($this->r->verify('abcfwaA12345', 6));
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few lowercase letters. At least 5 lowercase letters are required.
	 */
	public function testSpecialLowerCaseTooShort() {
		$this->r->verify('ÑÑÑÑÑÑÑÑÑñ', 5);
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testSpecialLowerCaseOkay() {
		$this->assertNull($this->r->verify('ÑñÑñÑñÑñÑñ', 5));
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few lowercase letters. At least 5 lowercase letters are required.
	 */
	public function testNumericOnlyTooShort() {
		$this->r->verify('11111111', 5);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password contains too few lowercase letters. At least 5 lowercase letters are required.
	 */
	public function testSpecialOnlyTooShort() {
		$this->r->verify('#+?@#+?@', 5);
	}
}
