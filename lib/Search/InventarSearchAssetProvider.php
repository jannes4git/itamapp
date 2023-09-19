<?php

declare(strict_types=1);

namespace OCA\ItamApp\Search;

use OCA\ItamApp\AppInfo\Application;
use OCA\ItamApp\Service\AssetService;
use OCP\App\IAppManager;
use OCP\IL10N;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Search\IProvider;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use OCP\Search\SearchResultEntry;

class InventarSearchAssetProvider implements IProvider
{

	private IAppManager $appManager;
	private IL10N $l10n;
	private IConfig $config;
	private IURLGenerator $urlGenerator;
	private AssetService $assetService;

	public function __construct(
		IAppManager   $appManager,
		IL10N         $l10n,
		IConfig       $config,
		IURLGenerator $urlGenerator,
		AssetService  $assetService
	) {
		$this->appManager = $appManager;
		$this->l10n = $l10n;
		$this->config = $config;
		$this->urlGenerator = $urlGenerator;
		$this->assetService = $assetService;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string
	{
		return 'asset-search';
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return $this->l10n->t('Assets');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(string $route, array $routeParameters): int
	{

		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function search(IUser $user, ISearchQuery $query): SearchResult
	{

		$results = [];
		$limit = $query->getLimit();
		$term = $query->getTerm();
		$offset = $query->getCursor();
		$offset = $offset ? intval($offset) : 0;

		$result = $this->assetService->searchAsset($query->getTerm());
		//$route = $this->urlGenerator->linkToRouteAbsolute('itamapp.page.index');

		if ($query->getTerm() === 'hello world') {
			$route = $this->urlGenerator->linkToRouteAbsolute('itamapp.page.index');
			// $route = "https://www.google.de";
			$route = $route . "#/asset/213";

			$entry = new SearchResultEntry(
				$route, // URL zum Eintrag
				'Hello World', // Haupttext
				'Test-Eintrag', // Untertitel
				$route // Icon-Klasse (optional)
			);
			$entries[] = $entry;
			return SearchResult::complete($this->getName(), $entries);
		}

		return SearchResult::complete($this->getName(), $results);
	}
}
