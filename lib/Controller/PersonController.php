<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Service\UtilService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class PersonController extends Controller
{
    private $utilService;


    public function __construct(string $appName, IRequest $request, UtilService $utilService)
    {
        parent::__construct($appName, $request);
        $this->utilService = $utilService;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse
    {
        $data = $this->utilService->findAllPersonen();
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
            return new DataResponse($this->utilService->findPerson($id));
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
    public function create(string $name)
    {
        $id = $this->utilService->createPerson($name);
        return new DataResponse($id);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $date
     * @param string $beschreibung
     */
    public function update(int $id, string $name)
    {
        $id = $this->utilService->updatePerson($id, $name);
        return new DataResponse($id);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        $response = $this->utilService->deletePerson($id);
        return new DataResponse($response);
    }
}
