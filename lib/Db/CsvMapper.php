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
 * @template-extends QBMapper<Asset>
 */
class CsvMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		//der 2. string ist der db name
		parent::__construct($db, 'inventar', Asset::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $inventarnummer): Asset {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('inventar')
			->where($qb->expr()->eq('inventarnummer', $qb->createNamedParameter($inventarnummer)));
		return $this->findEntity($qb);
	}
	

	
	public function findAll() {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('inventar');
		return $this->findEntities($qb);
		
	}

	
	public function getColumns() {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		
		//$qb = $qb->select('COLUMN_NAME')->from('information_schema.columns', 'information_schema.columns')->where($qb->expr()->eq('table_name', $qb->createNamedParameter('oc_inventar')));
		//$qb = $qb->select('COLUMN_NAME')->from('information_schema.columns', 'c')->where($qb->expr()->eq('c.table_name', $qb->createNamedParameter('oc_inventar')));
		$result = $this->db->executeQuery("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name = 'oc_inventar' ORDER BY ordinal_position");
		try {
			$entities = [];
			while ($row = $result->fetch()) {
				$entities[] = $row;
			}
			
		} finally {
			$result->closeCursor();
			//return $result;
		}
		return $entities;
		//return $this->findEntities($qb);
		//$result = mysql_query('show columns from oc_inventar');
		//echo "FreeJannesKnie";
		//return $result;
	}
}
