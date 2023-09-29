<?php

declare(strict_types=1);


namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;


/**
 * @template-extends QBMapper<Asset>
 */
class RaumMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'raum', Raum::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): Raum
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('raum')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}

	/**
	 * @throws DoesNotExistException
	 */
	public function findAll(): array
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('raum');
		return $this->findEntities($qb);
	}
}
