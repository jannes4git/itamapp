<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 */
class PersonRaum extends Entity implements JsonSerializable
{
    protected ?int $personId = null;
    protected ?int $raumId = null;

    public function __construct()
    {
        $this->addType('personId', 'integer');
        $this->addType('raumId', 'integer');
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'personId' => $this->personId,
            'raumId' => $this->raumId,
        ];
    }

    public function propertyToColumn($property)
    {
        if ($property === 'personId') {
            return 'personId';
        } elseif ($property === 'raumId') {
            return 'raumId';
        } elseif ($property === 'raumName') {
            return 'raumName';
        } else {
            return parent::propertyToColumn($property);
        }
    }
}
