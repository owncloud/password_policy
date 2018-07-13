<?php
/**
 * @author Jörn Friedrich Dreyer <jfd@butonic.de>
 * @author Semih Serhat Karakaya <karakayasemi@itu.edu.tr>
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

namespace OCA\PasswordPolicy\Tests\Db;

use OCA\PasswordPolicy\Db\OldPassword;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCP\IDBConnection;
use OCP\DB\QueryBuilder\IQueryBuilder;
use Test\TestCase;

/**
 * @group DB
 */
class OldPasswordMapperTest extends TestCase {
	/** @var IDBConnection */
	private $db;
	/** @var OldPasswordMapper */
	private $mapper;
	/** @var string */
	private $testUID = 'test1';
	private $testUIDs = ['test1', 'test2', 'test3'];

	private function resetDB() {
		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->mapper->getTableName())
			->where($qb->expr()->in('uid', $qb->createNamedParameter($this->testUIDs, IQueryBuilder::PARAM_STR_ARRAY)));
		$qb->execute();
	}
	protected function setUp() {
		parent::setUp();
		$this->db = \OC::$server->getDatabaseConnection();
		$this->mapper = new OldPasswordMapper($this->db);
		$this->resetDB();
	}

	protected function tearDown() {
		parent::tearDown();
		$this->resetDB();
	}

	public function addInitialTestEntries($baseTime) {
		//add an initial entries
		foreach ($this->testUIDs as $index => $uid) {
			$oldPassword = new OldPassword();
			$oldPassword->setUid($uid);
			$oldPassword->setPassword("{$uid}testpass1");
			$oldPassword->setChangeTime($baseTime);
			$this->mapper->insert($oldPassword);

			$oldPassword = new OldPassword();
			$oldPassword->setUid($uid);
			$oldPassword->setPassword("{$uid}testpass2");
			$oldPassword->setChangeTime($baseTime + (10 * ($index + 1)));
			$this->mapper->insert($oldPassword);

			$oldPassword = new OldPassword();
			$oldPassword->setUid($uid);
			$oldPassword->setPassword("{$uid}testpass3");
			$oldPassword->setChangeTime($baseTime + (20 * ($index + 1)));
			$this->mapper->insert($oldPassword);
		}
	}

	public function providesBool() {
		return [[false], [true]];
	}

	/**
	 * @dataProvider providesBool
	 */
	public function testGetOldPasswords($excludeForceExpired) {
		$baseTime = \time();
		$uid = $this->testUIDs[0];
		$this->assertCount(0, $this->mapper->getOldPasswords($uid, 3, $excludeForceExpired));
		$this->addInitialTestEntries($baseTime);

		$oldPassword = new OldPassword();
		$oldPassword->setUid($uid);
		$oldPassword->setPassword(OldPassword::EXPIRED);
		$oldPassword->setChangeTime($baseTime + 100);
		$this->mapper->insert($oldPassword);

		$oldPasswords = $this->mapper->getOldPasswords($uid, 2, $excludeForceExpired);

		if ($excludeForceExpired) {
			$this->assertCount(2, $oldPasswords);
			$this->assertSame("{$uid}testpass3", $oldPasswords[0]->getPassword());
			$this->assertSame("{$uid}testpass2", $oldPasswords[1]->getPassword());

			$this->assertCount(3, $this->mapper->getOldPasswords($uid, 3, $excludeForceExpired));
		} else {
			$this->assertCount(2, $oldPasswords);
			$this->assertSame(OldPassword::EXPIRED, $oldPasswords[0]->getPassword());
			$this->assertSame("{$uid}testpass3", $oldPasswords[1]->getPassword());

			$this->assertCount(3, $this->mapper->getOldPasswords($uid, 3, $excludeForceExpired));
		}
	}

	public function testLatestPassword() {
		$baseTime = \time();
		$uid = $this->testUIDs[0];
		$this->assertNull($this->mapper->getLatestPassword($uid));
		$this->addInitialTestEntries($baseTime);

		$latestPassword = $this->mapper->getLatestPassword($uid);

		$this->assertSame("{$uid}testpass3", $latestPassword->getPassword());
	}

	public function testLatestPasswordIncludesExpired() {
		$baseTime = \time();
		$uid = $this->testUIDs[0];
		$this->assertNull($this->mapper->getLatestPassword($uid));
		$this->addInitialTestEntries($baseTime);

		// this one is excluded
		$oldPassword = new OldPassword();
		$oldPassword->setUid($uid);
		$oldPassword->setPassword(OldPassword::EXPIRED);
		$oldPassword->setChangeTime($baseTime + 100);
		$this->mapper->insert($oldPassword);

		$latestPassword = $this->mapper->getLatestPassword($uid);

		$this->assertSame(OldPassword::EXPIRED, $latestPassword->getPassword());
	}

	public function testGetPasswordsAboutToExpireAllOk() {
		$baseTime = \time();
		$this->addInitialTestEntries($baseTime);

		$passwordList = $this->mapper->getPasswordsAboutToExpire($baseTime + 14);
		$passwordList = \iterator_to_array($passwordList);  // convert to array

		// last password change after the timestamp, so we shouldn't get any result
		$this->assertCount(0, $passwordList);
	}

	public function testGetPasswordsAboutToExpireSomePassMarked() {
		$baseTime = \time();
		$this->addInitialTestEntries($baseTime);

		$passwordList = $this->mapper->getPasswordsAboutToExpire($baseTime + 44);
		$passwordList = \iterator_to_array($passwordList);  // convert to array
		// last password change before the timestamp
		$this->assertCount(2, $passwordList);

		$uid = $this->testUIDs[0];
		$latestPassword = $passwordList[0];
		$this->assertSame("{$uid}testpass3", $latestPassword->getPassword());
		$this->assertSame($uid, $latestPassword->getUid());
		$this->assertLessThan($baseTime + 44, $latestPassword->getChangeTime());

		$uid = $this->testUIDs[1];
		$latestPassword = $passwordList[1];
		$this->assertSame("{$uid}testpass3", $latestPassword->getPassword());
		$this->assertSame($uid, $latestPassword->getUid());
		$this->assertLessThan($baseTime + 44, $latestPassword->getChangeTime());
	}

	public function testGetPasswordsAboutToExpireAllMarked() {
		$baseTime = \time();
		$this->addInitialTestEntries($baseTime);

		$passwordList = $this->mapper->getPasswordsAboutToExpire($baseTime + 130);
		$passwordList = \iterator_to_array($passwordList);  // convert to array
		// last password change before the timestamp
		$this->assertCount(3, $passwordList);

		$uid = $this->testUIDs[0];
		$latestPassword = $passwordList[0];
		$this->assertSame("{$uid}testpass3", $latestPassword->getPassword());
		$this->assertSame($uid, $latestPassword->getUid());
		$this->assertLessThan($baseTime + 130, $latestPassword->getChangeTime());

		$uid = $this->testUIDs[1];
		$latestPassword = $passwordList[1];
		$this->assertSame("{$uid}testpass3", $latestPassword->getPassword());
		$this->assertSame($uid, $latestPassword->getUid());
		$this->assertLessThan($baseTime +130, $latestPassword->getChangeTime());

		$uid = $this->testUIDs[2];
		$latestPassword = $passwordList[2];
		$this->assertSame("{$uid}testpass3", $latestPassword->getPassword());
		$this->assertSame($uid, $latestPassword->getUid());
		$this->assertLessThan($baseTime + 130, $latestPassword->getChangeTime());
	}
}
