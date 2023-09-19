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
 * @template-extends QBMapper<CustomField>
 */
class CustomFieldMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        //der 2. string ist der db name
        parent::__construct($db, 'custom_fields', CustomField::class);
    }

    /**
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     * @throws DoesNotExistException
     */
    /*
    public function find(int $inventarnummer): Asset
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('asset')
            ->where($qb->expr()->eq('inventarnummer', $qb->createNamedParameter($inventarnummer)));
        return $this->findEntity($qb);
    }
    */


    //TODO: findEntities() funktioniert nicht, weil die Klasse CustomField nicht existiert
    public function findAllCustomFieldValues()
    {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('id', 'name', 'type')
            ->from('custom_fields');
        $result = $this->db->executeQuery("SELECT cfv.assetId, cfv.value, cf.name, cf.type from oc_custom_field_values as cfv LEFT JOIN oc_custom_fields cf on cfv.customFieldId = cf.id");
        try {
            $customFields = [];
            while ($row = $result->fetch()) {
                $customFields[] = $row;
            }
        } finally {
            $result->closeCursor();
        }
        return $customFields;
    }
    public function findAllCustomFields()
    {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('custom_fields');
        $result = $this->db->executeQuery($qb->getSQL());
        try {
            $customFieldValues = [];
            while ($row = $result->fetch()) {
                $customFieldValues[] = $row;
            }
        } finally {
            $result->closeCursor();
        }
        return $customFieldValues;
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
    public function getId(string $name): int
    {
        $query = "SELECT id FROM oc_custom_fields WHERE name = :name";
        $statement = $this->db->prepare($query);
        $statement->bindValue(':name', $name);
        $result = $statement->execute();

        if ($result instanceof \OCP\DB\IResult) {
            $row = $result->fetchColumn();

            if ($row !== false) {
                return (int) $row;
            }
        }

        // Fehlerbehandlung, wenn kein Ergebnis gefunden wurde
        return -1;
    }
}
