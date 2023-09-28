<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use JsonSerializable;


use OCP\AppFramework\Db\Entity;

/**
 * @method int getId()
 */
class Person extends Entity implements JsonSerializable
{
	protected string $name = '';
	protected ?string $locationId = '';
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'locationId' => $this->locationId,
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
