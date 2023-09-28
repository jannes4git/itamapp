<?php

declare(strict_types=1);

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
