<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Service\AssetService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\AppFramework\Http\JSONResponse;

class AssetController extends Controller
{
    private $assetService;


    public function __construct(string $appName, IRequest $request, AssetService $assetService)
    {
        parent::__construct($appName, $request);
        $this->assetService = $assetService;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse
    {
        //$columns = $this->mapper->getColumns();
        try {
            $data = $this->assetService->findAll();
        } catch (Exception $e) {
            return new DataResponse($e->getMessage(), Http::STATUS_NOT_FOUND);
        }
        //return new DataResponse(array($columns, $data));
        return new DataResponse($data);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id): DataResponse
    {
        try {
            return new DataResponse($this->assetService->find($id));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param string $inventarnummer
     * @param string $rechnungsdatum
     * @param string|null $beschreibung
     */
    public function create(string $rechnungsdatum, string $seriennummer = null, ?int $locationId, ?int $personId, array $customFieldValues = null)
    {
        //$data = 'test';
        //$data = json_decode(file_get_contents('php://input'), true);
        $inventarnummer = $this->assetService->generateInventarnummer($rechnungsdatum);

        try {
            $invNum = $this->assetService->create($inventarnummer, $rechnungsdatum, $seriennummer, $locationId, $personId, $customFieldValues);
        } catch (Exception $e) {
            return new DataResponse($e->getMessage(), Http::STATUS_CONFLICT);
        }
        //$asset = new Asset();
        //$asset->setBeschreibung($beschreibung);
        //$asset->setRechnungsdatum($rechnungsdatum);
        //$asset->setInventarnummer($inventarnummer);

        //$customFieldSize = sizeof($data['customFields']);

        return new DataResponse(array("inventarnummer" => $invNum), Http::STATUS_CREATED);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $date
     * @param string $beschreibung
     */
    public function update(int $id, string $inventarnummer, string $rechnungsdatum, string $seriennummer = null, ?int $locationId = null, ?int $personId = null, array $customFieldValues = null)
    {
        $id = $this->assetService->update($id, $inventarnummer, $rechnungsdatum, $seriennummer, $locationId, $personId, $customFieldValues);
        return new DataResponse($id);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        $this->assetService->delete($id);
        return new DataResponse([], Http::STATUS_NO_CONTENT);
    }
}
