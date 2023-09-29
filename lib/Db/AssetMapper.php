<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

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
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'a.seriennummer', 'a.locationId')
			->from('asset', 'a')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}



	public function findAll(): array
	{
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

	/**
	 * Suche Asset mit Inventarnummer.
	 */
	public function search(string $query)
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'a.seriennummer', 'a.locationId', 'a.personId')
			->from('asset', 'a')
			->where($qb->expr()->like('a.inventarnummer', $qb->createNamedParameter('%' . $query . '%')));
		return $this->findEntities($qb);
	}
}
