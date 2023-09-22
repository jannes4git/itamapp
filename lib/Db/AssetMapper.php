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
use Psr\Log\NullLogger;

use function Safe\mysql_query;

/**
 * @template-extends QBMapper<Asset>
 */
class AssetMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		//der 2. string ist der db name
		parent::__construct($db, 'asset', Asset::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): Asset
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'a.seriennummer', 'a.locationId')
			->from('asset', 'a')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}



	public function findAll(): array
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'a.seriennummer', 'a.locationId', 'a.personId')
			->from('asset', 'a');
		return $this->findEntities($qb);
	}

	public function findAssetOfPerson(int $personId): array
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'a.seriennummer', 'a.locationId', 'a.personId')
			->from('asset', 'a')
			->where($qb->expr()->eq('a.personId', $qb->createNamedParameter($personId)));
		return $this->findEntities($qb);
	}
	public function changeRaum(int $assetId, ?int $raumId = null)
	{
		$qb = $this->db->getQueryBuilder();
		$qb->update('asset')
			->set('locationId', $qb->createNamedParameter($raumId))
			->where($qb->expr()->eq('id', $qb->createNamedParameter($assetId)));
		$qb->execute();
	}

	public function search(string $query)
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'a.seriennummer', 'a.locationId', 'a.personId')
			->from('asset', 'a')
			->where($qb->expr()->like('a.inventarnummer', $qb->createNamedParameter('%' . $query . '%')));
		return $this->findEntities($qb);
	}


	public function getColumns()
	{
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
