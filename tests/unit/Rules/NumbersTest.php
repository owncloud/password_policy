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

use OCA\PasswordPolicy\Rules\Numbers;
use OCP\IL10N;

class NumbersTest extends \Test\TestCase {
	/** @var Numbers */
	private $r;

	public function setUp(): void {
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

		$this->r = new Numbers($l10n);
	}

	/**
	 */
	public function testNoNumbers() {
		$this->expectException(\OCA\PasswordPolicy\Rules\PolicyException::class);
		$this->expectExceptionMessage('The password contains too few numbers. At least one number is required.');

		$this->r->verify('ab', 1);
	}

	/**
	 */
	public function testTooShort() {
		$this->expectException(\OCA\PasswordPolicy\Rules\PolicyException::class);
		$this->expectExceptionMessage('The password contains too few numbers. At least 4 numbers are required.');

		$this->r->verify('ab123', 4);
	}

	/**
	 * @throws \OCA\PasswordPolicy\Rules\PolicyException
	 */
	public function testOkay() {
		$this->assertNull($this->r->verify('1234567890', 6));
	}
}
