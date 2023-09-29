<?php

declare(strict_types=1);

namespace OCA\ItamApp\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\ItamApp\Service\AssetNotFound;

trait Errors
{
	protected function handleNotFound(Closure $callback): DataResponse
	{
		try {
			return new DataResponse($callback());
		} catch (AssetNotFound $e) {
			$message = ['message' => $e->getMessage()];
			return new DataResponse($message, Http::STATUS_NOT_FOUND);
		}
	}
}
