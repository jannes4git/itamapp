<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;


/**
 * @template-extends QBMapper<CustomFieldValue>
 */
class CustomFieldValueMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'custom_field_values', CustomFieldValue::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id, int $assetId): CustomFieldValue
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('cf.id', 'cf.assetId', 'cf.customFieldId', 'cf.value')
			->from('custom_field_values', 'cf')
			->where($qb->expr()->eq('assetId', $qb->createNamedParameter($assetId)))->andWhere($qb->expr()->eq('customFieldId', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}



	public function findAll(): array
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('a.id', 'a.inventarnummer', 'a.rechnungsdatum', 'r.raumName')
			->from('asset', 'a')->join('a', 'raum', 'r', 'a.locationId = r.id');
		return $this->findEntities($qb);
	}
}
