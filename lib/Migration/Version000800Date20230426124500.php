<?php

declare(strict_types=1);

namespace OCA\ItamApp\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000800Date20230426124500 extends SimpleMigrationStep
{

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
	{
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		//Raum Table
		if (!$schema->hasTable('raum')) {
			$raumTable = $schema->createTable('raum');
			$raumTable->addColumn('id', 'bigint', [
				'unsigned' => true,
				'autoincrement' => true,
				'notnull' => true,
			]);
			$raumTable->addColumn('raumName', 'string', [
				'length' => 255,
				'notnull' => true,
			]);

			$raumTable->setPrimaryKey(['id']);
		}
		//Person Table 
		if (!$schema->hasTable('person')) {
			$table = $schema->createTable('person');
			$table->addColumn('id', 'bigint', [
				'unsigned' => true,
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('name', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn('locationId', 'bigint', [
				'notnull' => false,
				'unsigned' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addForeignKeyConstraint($schema->getTable('raum'), ['locationId'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'], 'location_person_fk');
		}

		//Custom Field Table
		if (!$schema->hasTable('custom_fields')) {
			$cfTable = $schema->createTable('custom_fields');
			$cfTable->addColumn('id', 'bigint', [
				'unsigned' => true,
				'autoincrement' => true,
				'notnull' => true,
			]);
			$cfTable->addColumn('name', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$cfTable->addColumn('type', 'string', [
				'length' => 255,
				'notnull' => true,
			]);

			$cfTable->setPrimaryKey(['id']);
		}
		//Asset Table
		if (!$schema->hasTable('asset')) {
			$assetTable = $schema->createTable('asset');
			$assetTable->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'unsigned' => true,
				'notnull' => true,
			]);
			$assetTable->addColumn('inventarnummer', 'string', [
				'notnull' => true,
				'length' => 255
			]);
			$assetTable->addColumn('seriennummer', 'string', [
				'notnull' => true,
			]);
			$assetTable->addColumn('rechnungsdatum', 'date', [
				'notnull' => false,
			]);
			$assetTable->addColumn('locationId', 'bigint', [
				'notnull' => false,
				'unsigned' => true,
			]);
			$assetTable->addColumn('personId', 'bigint', [
				'notnull' => false,
				'unsigned' => true,
			]);


			$assetTable->setPrimaryKey(['id']);
			$assetTable->addUniqueIndex(['inventarnummer'], 'asset_inventarnummer_unique');
			$assetTable->addForeignKeyConstraint($schema->getTable('raum'), ['locationId'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'], 'location_fk');
			$assetTable->addForeignKeyConstraint($schema->getTable('person'), ['personId'], ['id'], [
				'onDelete' => 'SET NULL',
			]);
		}
		$table = $schema->getTable('asset');
		$column = $table->getColumn('seriennummer');
		$column->setNotnull(false);
		$type = \Doctrine\DBAL\Types\Type::getType('string');
		$column->setType($type);



		//Raum-Person Table
		/**
		 if (!$schema->hasTable('person_raum')) {
			$table = $schema->createTable('person_raum');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('personId', 'bigint', [
				'notnull' => true,
				'unsigned' => true,

			]);
			$table->addColumn('raumId', 'bigint', [
				'notnull' => true,
				'unsigned' => true,

			]);

			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['personId', 'raumId'], 'person_raum_person_raum_index');
			$table->addForeignKeyConstraint($schema->getTable('person'), ['personId'], ['id'], ['onDelete' => 'CASCADE'], 'person_raum_person_id_fk');
			$table->addForeignKeyConstraint($schema->getTable('raum'), ['raumId'], ['id'], ['onDelete' => 'CASCADE'], 'person_raum_raum_id_fk');
		}
		 */


		//Custom Field Values
		if (!$schema->hasTable('custom_field_values')) {
			$table = $schema->createTable('custom_field_values');
			$table->addColumn('id', 'bigint', [
				'unsigned' => true,
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('assetId', 'bigint', [
				'unsigned' => true,
				'notnull' => true,
			]);
			$table->addColumn('customFieldId', 'bigint', [
				'unsigned' => true,
				'notnull' => true,
			]);
			$table->addColumn('value', 'string', [
				'length' => 255,
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['assetId', 'customFieldId'], 'custom_field_v_index');
			$table->addForeignKeyConstraint($schema->getTable('asset'), ['assetId'], ['id'], ['onDelete' => 'CASCADE'], 'asset_fk');
			$table->addForeignKeyConstraint($schema->getTable('custom_fields'), ['customFieldId'], ['id'], ['onDelete' => 'CASCADE'], 'custom_field_fk');
		}

		return $schema;
	}
}
