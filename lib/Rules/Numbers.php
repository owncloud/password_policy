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

namespace OCA\PasswordPolicy\Rules;

class Numbers extends Base {

	public function verify($password, $val) {
		if ($this->countDigits($password) < $val) {
			throw new \Exception(
				$this->l10n->t("Password contains too few numbers. Minimum %d numbers are required.", [$val]));
		}
	}

	private function countDigits( $str ) {
		return preg_match_all( "/[0-9]/", $str );
	}
}
