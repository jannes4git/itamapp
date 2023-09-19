<?php

declare(strict_types=1);

namespace OCA\ItamApp\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 */
class CustomFieldValue extends Entity implements JsonSerializable
{
	protected string $assetId = '';
	protected string $customFieldId = '';
	protected string $value = '';

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'assetId' => $this->assetId,
            'customFieldId' => $this->customFieldId,
            'value' => $this->value,
		];
	}
	public function propertyToColumn($property) {
        if ($property === 'locationId') {
            return 'locationId';
        }else if ($property === 'assetId') {
			return 'assetId';
		}
		else if ($property === 'customFieldId') {
			return 'customFieldId';
		}
		 else {
            return parent::propertyToColumn($property);
        }
    }
	
}
