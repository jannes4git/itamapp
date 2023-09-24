<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Jannes Lensch <test@test.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use function Safe\mysql_query;

/**
 * @template-extends QBMapper<Person>
 */
class PersonMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		//der 2. string ist der db name
		parent::__construct($db, 'person', Person::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): Person
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('person')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findAll(): array
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('person');
		return $this->findEntities($qb);
	}
}