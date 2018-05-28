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

	private function resetDB() {
		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->mapper->getTableName())
			->where($qb->expr()->eq('uid', $qb->createNamedParameter($this->testUID)));
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

	public function addInitialTestEntries() {
		//add an initial entries
		$oldPassword = new OldPassword();
		$oldPassword->setUid($this->testUID);
		$oldPassword->setPassword('testpass1');
		$oldPassword->setChangeTime(\OC::$server->getTimeFactory()->getTime());
		$this->mapper->insert($oldPassword);

		$oldPassword = new OldPassword();
		$oldPassword->setUid($this->testUID);
		$oldPassword->setPassword('testpass2');
		$oldPassword->setChangeTime(\OC::$server->getTimeFactory()->getTime()+1);
		$this->mapper->insert($oldPassword);

		$oldPassword = new OldPassword();
		$oldPassword->setUid($this->testUID);
		$oldPassword->setPassword('testpass3');
		$oldPassword->setChangeTime(\OC::$server->getTimeFactory()->getTime()+2);
		$this->mapper->insert($oldPassword);
	}

	public function testGetOldPasswords() {
		$this->assertCount(0, $this->mapper->getOldPasswords($this->testUID,3));
		$this->addInitialTestEntries();

		$oldPasswords = $this->mapper->getOldPasswords($this->testUID,2);

		$this->assertCount(2, $oldPasswords);
		$this->assertNotSame("testpass1", $oldPasswords[0]->getPassword());
		$this->assertNotSame("testpass1", $oldPasswords[1]->getPassword());

		$this->assertCount(3, $this->mapper->getOldPasswords($this->testUID,3));
	}

	public function testLatestPassword() {
		$this->assertNull($this->mapper->getLatestPassword($this->testUID));
		$this->addInitialTestEntries();

		$latestPassword = $this->mapper->getLatestPassword($this->testUID);

		$this->assertSame("testpass3", $latestPassword->getPassword());
	}
}