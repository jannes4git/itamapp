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
class AssetDTO extends Entity implements JsonSerializable
{
	protected string $inventarnummer = '';
	protected ?string $seriennummer = '';
	protected string $rechnungsdatum = '';
	protected ?string $raumName = '';

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'inventarnummer' => $this->inventarnummer,
			'seriennummer' => $this->seriennummer,
			'rechnungsdatum' => $this->rechnungsdatum,
			'raumName' => $this->raumName,
		];
	}
	public function propertyToColumn($property)
	{
		if ($property === 'locationId') {
			return 'locationId';
		} else {
			return parent::propertyToColumn($property);
		}
	}
}
