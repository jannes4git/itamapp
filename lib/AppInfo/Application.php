<?php

declare(strict_types=1);

namespace OCA\ItamApp\AppInfo;

use OCA\ItamApp\Search\InventarSearchAssetProvider;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;

class Application extends App implements IBootstrap
{
	public const APP_ID = 'itamapp';

	public function __construct()
	{
		parent::__construct(self::APP_ID);
	}
	public function register(IRegistrationContext $context): void
	{
		$context->registerSearchProvider(InventarSearchAssetProvider::class);
	}
	public function boot(IBootContext $context): void
	{
	}
}
