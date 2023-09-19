<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Jannes Lensch <test@test.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\ItamApp\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000003Date20181013124731 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if ($schema->hasTable('itamapp')) {
			$schema->dropTable('itamapp');
			$table = $schema->createTable('itamapp');
			$table->addColumn('inventarnummer', 'INTEGER', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('rechnungsdatum', 'DATETIME', [
				'notnull' => true,
				'length' => 200
			]);
			$table->addColumn('beschreibung', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			

			$table->setPrimaryKey(['inventarnummer']);
			//$table->addIndex(['user_id'], 'itamapp_user_id_index');
		}
		return $schema;
	}
}
