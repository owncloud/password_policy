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

use OCA\PasswordPolicy\Rules\Length;
use OCA\PasswordPolicy\Rules\PolicyException;
use OCP\IL10N;
use Test\TestCase;

class LengthTest extends TestCase {

	/** @var Length */
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

		$this->r = new Length($l10n);
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password is too short. At least 25 characters are required.
	 */
	public function testTooShort() {
		$this->r->verify('1234567890', 25);
	}

	/**
	 * @throws PolicyException
	 */
	public function testOkay() {
		$this->assertNull($this->r->verify('1234567890', 6));
	}

	/**
	 * @expectedException \OCA\PasswordPolicy\Rules\PolicyException
	 * @expectedExceptionMessage The password is too short. At least 5 characters are required.
	 */
	public function testSpecialCharsTooShort() {
		$this->r->verify('ççç', 5);
	}

	/**
	 * @throws PolicyException
	 */
	public function testSpecialCharsOkay() {
		$this->assertNull($this->r->verify('çççççç', 5));
	}
}
