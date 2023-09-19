<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCA\ItamApp\Db\PersonRaum;
use OCA\ItamApp\Db\PersonRaumMapper;
use OCA\ItamApp\Service\AssetService;

class PersonRaumController extends Controller
{

    private $mapper;
    private $assetService;

    public function __construct(string $appName, IRequest $request, PersonRaumMapper $mapper, AssetService $assetService)
    {
        parent::__construct($appName, $request);
        $this->mapper = $mapper;
        $this->assetService = $assetService;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse
    {
        $data = $this->mapper->findAll();
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
            return new DataResponse(null);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function create(int $personId, int $raumId)
    {
        try {
            $personRaum = $this->mapper->findForPerson($personId);
        } catch (Exception $e) {
            $personRaum = null;
        }

        if ($personRaum != null) {
            for ($i = 0; $i < count($personRaum); $i++) {
                $this->mapper->delete($personRaum[$i]);
            }
        }
        $test = $this->assetService->findAssetOfPerson($personId);
        foreach ($test as $asset) {
            $this->assetService->changeRaumId($asset->getId(), $raumId);
        }
        $personRaum = new PersonRaum();
        $personRaum->setPersonId($personId);
        $personRaum->setRaumId($raumId);
        $personRaum = $this->mapper->insert($personRaum);
        return new DataResponse($personRaum);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $date
     * @param string $beschreibung
     */
    public function update(int $personId, int $raumId)
    {
        // empty for now
        $personRaum = new PersonRaum();
        $personRaum->setPersonId($personId);
        $personRaum->setRaumId($raumId);
        $personRaum = $this->mapper->insert($personRaum);
        return new DataResponse($personRaum);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        // empty for now
    }
}
