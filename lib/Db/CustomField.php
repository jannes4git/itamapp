<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Jannes Lensch <test@test.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

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
