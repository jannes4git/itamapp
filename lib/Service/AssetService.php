<?php

declare(strict_types=1);

namespace OCA\ItamApp\Service;

use Exception;
use OCA\ItamApp\Db\Asset;
use OCA\ItamApp\Db\AssetMapper;
use OCA\ItamApp\Db\CustomFieldMapper;
use OCA\ItamApp\Db\CustomFieldValue;
use OCA\ItamApp\Db\CustomFieldValueMapper;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class AssetService
{
	private AssetMapper $assetMapper;
	private CustomFieldValueMapper $customFieldValueMapper;
	private CustomFieldMapper $customFieldMapper;

	public function __construct(AssetMapper $assetMapper, CustomFieldValueMapper $customFieldValueMapper, CustomFieldMapper $customFieldMapper)
	{
		$this->assetMapper = $assetMapper;
		$this->customFieldValueMapper = $customFieldValueMapper;
		$this->customFieldMapper = $customFieldMapper;
	}
	/**
	 * Erstellt ein Asset.
	 */
	public function create(string $inventarnummer, string $rechnungsdatum, string $seriennummer = null, ?int $locationId, ?int $personId, array $customFieldValues = null)
	{
		try {
			$asset = new Asset();
			$asset->setSeriennummer($seriennummer);
			$asset->setRechnungsdatum($rechnungsdatum);
			$asset->setInventarnummer($inventarnummer);
			$asset->setLocationId($locationId);
			$asset->setPersonId($personId);
			$asset = $this->assetMapper->insert($asset);

			foreach ($customFieldValues as $key => $value) {
				if ($value == null || $value == "") {
					continue;
				}
				$cfv = new CustomFieldValue();
				$cfv->setAssetId($asset->getId());
				$id = $this->getIdForCF($key);
				$cfv->setCustomFieldId($id);
				$cfv->setValue($value);
				$this->customFieldValueMapper->insert($cfv);
			}
		} catch (Exception $e) {
			throw $e;
		}

		return $asset->getInventarnummer();
	}
	/**
	 * Aktualisiert ein Asset.
	 */
	public function update(int $id, string $inventarnummer, string $rechnungsdatum, string $seriennummer = null, ?int $locationId, ?int $personId, array $customFieldValues = null)
	{
		try {
			$asset = $this->assetMapper->find($id);
		} catch (Exception $e) {
			$this->handleException($e);
		}


		$asset->setSeriennummer($seriennummer);
		$asset->setRechnungsdatum($rechnungsdatum);
		$asset->setInventarnummer($inventarnummer);
		$asset->setLocationId($locationId);
		$asset->setPersonId($personId);


		foreach ($customFieldValues as $key => $value) {
			if ($value === null) {
				continue;
			}
			if ($value == "") {
				try {
					$cfv = $this->customFieldValueMapper->find($key, $id);
					$this->customFieldValueMapper->delete($cfv);
				} catch (Exception $e) {
					//cfv exisitiert nicht
				}
				continue;
			}
			try {
				$cfv = $this->customFieldValueMapper->find($key, $id);
				$cfv->setValue($value);
				$this->customFieldValueMapper->update($cfv);
			} catch (Exception $e) {
				//cfv existiert nicht
			}
			if ($cfv === null) {
				$cfv = new CustomFieldValue();
				$cfv->setAssetId($asset->getId());
				$cfv->setCustomFieldId($key);
				$cfv->setValue($value);
				$this->customFieldValueMapper->insert($cfv);
			}
			$cfv = null;
		}


		$this->assetMapper->update($asset);
		return $id;
	}

	public function findAll()
	{
		return $this->assetMapper->findAll();
	}
	public function findAssetOfPerson(int $personId)
	{
		return $this->assetMapper->findAssetOfPerson($personId);
	}
	public function changeRaumId(int $assetId, ?int $raumId = null)
	{
		$this->assetMapper->changeRaum($assetId, $raumId);
	}
	public function find(int $id)
	{
		try {
			return $this->assetMapper->find($id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
	public function getIdForCF(string $cf)
	{
		try {
			return $this->customFieldMapper->getId($cf);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
	public function delete(int $id)
	{
		try {
			$asset = $this->assetMapper->find($id);
			$this->assetMapper->delete($asset);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Generiert die Inventarnummer anhand des Rechnungsdatums und einer fortlaufenden Nummer.
	 * @param string $date Rechnungsdatum
	 */
	public function generateInventarnummer(?string $date)
	{
		if ($date == null) {
			throw new Exception("Rechnungsdatum fehlt bei Asset");
		}

		//trenne ggf. Uhrzeit ab
		$date = explode(" ", $date)[0];
		$connection = \OC::$server->getDatabaseConnection();
		$queryBuilder = $connection->getQueryBuilder();
		$queryBuilder->select('inventarnummer')
			->from('asset')
			->where($queryBuilder->expr()->like('inventarnummer', $queryBuilder->createNamedParameter($date . '.%')));

		$result = $queryBuilder->execute();
		$rows = $result->fetchAll();

		$highest_number = 0;
		foreach ($rows as $row) {
			$parts = explode('.', $row['inventarnummer']);
			if (count($parts) == 2 && is_numeric($parts[1])) {
				$number = intval($parts[1]);
				$highest_number = max($highest_number, $number);
			}
		}
		$highest_number++;
		$highest_number_padded = str_pad(strval($highest_number), 2, '0', STR_PAD_LEFT);
		$new_inventarnummer = $date . '.' . $highest_number_padded;

		return $new_inventarnummer;
	}

	/**
	 * Überprüft, ob eine Inventarnummer bereits existiert.
	 * @param string $inventarnummer Inventarnummer
	 */
	public function inventarnummerExistsCheck(string $inventarnummer)
	{
		$connection = \OC::$server->getDatabaseConnection();
		$queryBuilder = $connection->getQueryBuilder();
		$queryBuilder->select('inventarnummer')
			->from('asset')
			->where($queryBuilder->expr()->eq('inventarnummer', $queryBuilder->createNamedParameter($inventarnummer)));
		try {
			$result = $queryBuilder->execute();
			$rows = $result->fetchAll();
			$result->closeCursor();

			// Überprüfen Sie, ob das Ergebnis mehr als 0 Zeilen hat
			if (count($rows) > 0) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * @return never
	 */
	private function handleException(Exception $e)
	{
		if (
			$e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException
		) {
			throw new AssetNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}
	public function searchAsset(string $query)
	{
		return $this->assetMapper->search($query);
	}
}
