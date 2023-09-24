<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Jannes Lensch <jannes.lensch@web.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\ItamApp\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'resources' => [
		'asset' => ['url' => '/assets'],
		'meta' => ['url' => '/meta'],
		'customField' => ['url' => '/customfields'],
		'raum' => ['url' => '/raum'],
		'person' => ['url' => '/person'],
		'csv' => ['url' => '/csv'],
		'personRaum' => ['url' => '/personraum'],
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		[
			'name' => 'note_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']
		]
	]
];