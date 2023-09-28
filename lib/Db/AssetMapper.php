<?php

declare(strict_types=1);

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
		$result = $this->db->executeQuery("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name = 'oc_inventar' ORDER BY ordinal_position");
		try {
			$entities = [];
			while ($row = $result->fetch()) {
				$entities[] = $row;
			}
		} finally {
			$result->closeCursor();
		}
		return $entities;
	}
}
