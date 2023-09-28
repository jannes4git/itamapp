<?php

declare(strict_types=1);

//TODO: delete?
namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<Asset>
 */
class CsvMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		//der 2. string ist der db name
		parent::__construct($db, 'inventar', Asset::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $inventarnummer): Asset
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('inventar')
			->where($qb->expr()->eq('inventarnummer', $qb->createNamedParameter($inventarnummer)));
		return $this->findEntity($qb);
	}



	public function findAll()
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('inventar');
		return $this->findEntities($qb);
	}
}
