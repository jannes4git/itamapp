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

	private IL10N $l10n;
	private IURLGenerator $urlGenerator;
	private AssetService $assetService;

	public function __construct(
		IL10N         $l10n,
		IURLGenerator $urlGenerator,
		AssetService  $assetService
	) {
		$this->l10n = $l10n;
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
	 * Suchfunktion funktioniert bisher nur mit Inventarnummern.
	 */
	public function search(IUser $user, ISearchQuery $query): SearchResult
	{

		$results = [];
		$limit = 3;
		$term = $query->getTerm();

		$result = $this->assetService->searchAsset($term);
		foreach ($result as $asset) {
			if (count($results) >= $limit) {
				break;
			}
			$route = $this->urlGenerator->linkToRouteAbsolute('itamapp.page.index');
			$route = $route . "#/asset/" . $asset->getId();

			$entry = new SearchResultEntry(
				$route,
				$asset->getInventarnummer(),
				$asset->getInventarnummer(),
				$route
			);
			array_push($results, $entry);
		}
		return SearchResult::complete($this->getName(), $results);
	}
}
