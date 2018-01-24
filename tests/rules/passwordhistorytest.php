<?php
/**
 *
 * @author Semih Serhat Karakaya <karakayasemi@itu.edu.tr>
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
use OCA\PasswordPolicy\Rules\PasswordHistory;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCA\PasswordPolicy\Db\OldPassword;
use OCP\IL10N;

class PasswordHistoryTest extends \PHPUnit_Framework_TestCase {

	/** @var PasswordHistory */
	private $r;

	public function setUp() {
		parent::setUp();

		/** @var IL10N | \PHPUnit_Framework_MockObject_MockObject $l10n */
		$l10n = $this->getMockBuilder(IL10N::class)
			->disableOriginalConstructor()->getMock();
		$l10n
			->expects($this->any())
			->method('t')
			->will($this->returnCallback(function($text, $parameters = array()) {
				return vsprintf($text, $parameters);
			}));


		/** @var \OCA\PasswordPolicy\Db\OldPasswordMapper | \PHPUnit_Framework_MockObject_MockObject $mapper */
		$mapper = $this->getMockBuilder(OldPasswordMapper::class)
			->disableOriginalConstructor()->getMock();
		/** @var \OCA\PasswordPolicy\Db\OldPassword | \PHPUnit_Framework_MockObject_MockObject $entity1 */
		$entity1 = $this->getMockBuilder(OldPassword::class)
			->disableOriginalConstructor()->getMock();
		/** @var \OCA\PasswordPolicy\Db\OldPassword | \PHPUnit_Framework_MockObject_MockObject $entity2 */
		$entity2 = $this->getMockBuilder(OldPassword::class)
			->disableOriginalConstructor()->getMock();

		$entity1
			->expects($this->any())
			->method('getPassword')
			->will($this->returnValue(\OC::$server->getHasher()->hash("testpass1")));
		$entity2
			->expects($this->any())
			->method('getPassword')
			->will($this->returnValue(\OC::$server->getHasher()->hash("testpass2")));
		$mapper
			->expects($this->any())
			->method('getOldPasswords')
			->with("testuser","3")
			->will($this->returnValue(
				[$entity1, $entity2]
			));

		$this->r = new PasswordHistory($l10n, $mapper, \OC::$server->getHasher());
	}

	/**
	 * @dataProvider failDataProvider
	 * @param string $password
	 * @expectedException Exception
	 * @expectedExceptionMessage Password should be different than your last 2 passwords
	 */
	public function testWithOldPassword($password) {
		$this->r->verify($password, 2, 'testuser');
	}

	public function failDataProvider() {
		return [
			['testpass1'],
			['testpass2'],
		];
	}

	public function testSuccess() {
		$this->r->verify('testpass3', 2, 'testuser');
	}
}
