<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Service\PersonRaumService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class PersonController extends Controller
{
    private $personRaumService;


    public function __construct(string $appName, IRequest $request, PersonRaumService $personRaumService)
    {
        parent::__construct($appName, $request);
        $this->personRaumService = $personRaumService;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse
    {
        $data = $this->personRaumService->findAllPersonen();
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
            return new DataResponse($this->personRaumService->findPerson($id));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param int|null $locationId
     */
    public function create(string $name, ?int $locationId = null)
    {
        $id = $this->personRaumService->createPerson($name, $locationId);
        return new DataResponse($id);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string|null $name
     * @param int|null $locationId
     */
    public function update(int $id, ?string $name = null, ?int $locationId = null)
    {
        $id = $this->personRaumService->updatePerson($id, $name, $locationId);
        return new DataResponse($id);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        $response = $this->personRaumService->deletePerson($id);
        return new DataResponse($response);
    }
}
