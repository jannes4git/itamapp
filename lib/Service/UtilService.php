<?php

declare(strict_types=1);

namespace OCA\ItamApp\Service;

use Exception;

use OCA\ItamApp\Db\PersonMapper;

use OCA\ItamApp\Db\Person;
use OCA\ItamApp\Db\RaumMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use Punic\Data;

class UtilService
{
    private $personMapper;
    public function __construct(PersonMapper  $personMapper)
    {
        $this->personMapper = $personMapper;
    }
    public function findAllPersonen(): array
    {
        return $this->personMapper->findAll();
    }

    public function findPerson(int $id): Person
    {
        return $this->personMapper->find($id);
    }
    public function createPerson(string $name): int
    {
        $person = new Person();
        $person->setName($name);
        $person = $this->personMapper->insert($person);
        return $person->getId();
    }
    public function deletePerson(int $id): DataResponse
    {
        try {
            $person = $this->findPerson($id);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->personMapper->delete($person);
        return new DataResponse($person);
    }
}
