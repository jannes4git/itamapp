<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<PersonRaum>
 */
class PersonRaumMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        //der 2. string ist der db name
        parent::__construct($db, 'person_raum', PersonRaum::class);
    }

    public function find(int $id): PersonRaum
    {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        //$qb->select('p.id', 'p.name', 'r.id as raumId', 'r.raumName')
        //    ->from('person', 'p')
        //    ->leftJoin('p', 'person_raum', 'rp', $qb->expr()->eq('p.id', 'rp.personId'))
        //   ->leftJoin('rp', 'raum', 'r', $qb->expr()->eq('rp.raumId', 'r.id'))
        //   ->where($qb->expr()->eq('p.id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        $qb->select('id', 'personId', 'raumId')
            ->from('person_raum')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        return $this->findEntity($qb);
    }

    public function findForPerson(int $persoId)
    {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('id', 'personId', 'raumId')
            ->from('person_raum')
            ->where($qb->expr()->eq('personId', $qb->createNamedParameter($persoId, IQueryBuilder::PARAM_INT)));
        try {
            return $this->findEntities($qb);
        } catch (\Exception $e) {
            return null;
        }
    }


    public function findAll(): array
    {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();

        // $qb->select('rp.id', 'p.name', 'p.id as personId', 'r.id as raumId', 'r.raumName')
        //    ->from('person', 'p')
        //    ->leftJoin('p', 'person_raum', 'rp', $qb->expr()->eq('p.id', 'rp.personId'))
        //    ->leftJoin('rp', 'raum', 'r', $qb->expr()->eq('rp.raumId', 'r.id'));
        $qb->select('id', 'personId', 'raumId')
            ->from('person_raum');
        return $this->findEntities($qb);
    }
}
