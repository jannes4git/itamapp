<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 */
class Asset extends Entity implements JsonSerializable
{
	protected string $inventarnummer = '';
	protected ?string $seriennummer = '';
	protected ?string $rechnungsdatum = '';
	protected ?string $locationId = '';
	protected ?string $personId = '';

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'inventarnummer' => $this->inventarnummer,
			'seriennummer' => $this->seriennummer,
			'rechnungsdatum' => $this->rechnungsdatum,
			'locationId' => $this->locationId,
			'personId' => $this->personId,
		];
	}
	public function propertyToColumn($property)
	{
		if ($property === 'locationId') {
			return 'locationId';
		} else if ($property === 'personId') {
			return 'personId';
		} else {
			return parent::propertyToColumn($property);
		}
	}
}
