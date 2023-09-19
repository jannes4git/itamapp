<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Jannes Lensch <test@test.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

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
