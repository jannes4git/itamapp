<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

//TODO: delete?
/**
 * @template-extends QBMapper<Asset>
 */
class ColumnMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'inventar');
	}


	public function getColumns()
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$result = $this->db->executeQuery("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name = 'oc_asset' ORDER BY ordinal_position");
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
	}
}
