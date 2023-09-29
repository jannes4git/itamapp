<?php

declare(strict_types=1);


namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;


/**
 * @template-extends QBMapper<CustomField>
 */
class CustomFieldMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'custom_fields', CustomField::class);
    }

    /**
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     * @throws DoesNotExistException
     */

    public function find(int $id): CustomField
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('custom_fields')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));
        return $this->findEntity($qb);
    }



    //TODO: findEntities() funktioniert nicht, weil die Klasse CustomField nicht existiert
    public function findAllCustomFieldValues()
    {
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

    /**
     * Gibt die ID des CustomFields mit dem Namen $name zurück.
     * Falls kein CustomField mit dem Namen existiert, wird -1 zurückgegeben.
     * 
     * @param string $name
     */
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

        // Kein Ergebnis wurde gefunden
        return -1;
    }
}
