<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 */
class CustomField extends Entity implements JsonSerializable
{
    protected string $name = '';
    protected string $type = '';


    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
