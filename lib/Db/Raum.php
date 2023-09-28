<?php

declare(strict_types=1);


namespace OCA\ItamApp\Db;

use JsonSerializable;


use OCP\AppFramework\Db\Entity;

/**
 * @method int getId()
 */
class Raum extends Entity implements JsonSerializable
{
	protected string $raumName = '';
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'raumName' => $this->raumName,
		];
	}
	public function propertyToColumn($property)
	{
		if ($property === 'raumName') {
			return 'raumName';
		} else {
			return parent::propertyToColumn($property);
		}
	}
}
