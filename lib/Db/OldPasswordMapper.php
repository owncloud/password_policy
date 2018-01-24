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

namespace OCA\PasswordPolicy\Db;

use OCP\AppFramework\Db\Mapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class OldPasswordMapper extends Mapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'user_password_history');
	}

	/**
	 * @param string $uid
	 * @param int $length
	 * @return OldPassword[]
	 */
	public function getOldPasswords($uid, $length) {
		/* @var IQueryBuilder $qb */
		$qb = $this->db->getQueryBuilder();
		$qb->select('password')
			->from('user_password_history')
			->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)))
			->orderBy('change_time', 'desc');
		$result = $qb->execute();
		$rows = $result->fetchAll();
		$result->closeCursor();
		$rows = array_slice($rows,0, $length);
		return array_map(function ($row) {
			return OldPassword::fromRow($row);
		}, $rows);
	}
}