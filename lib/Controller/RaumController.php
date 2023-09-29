<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Db\Asset;
use OCA\ItamApp\Db\Raum;
use OCP\AppFramework\Http;
use OCA\ItamApp\Db\RaumMapper;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class RaumController extends Controller
{
    private RaumMapper $mapper;


    public function __construct(string $appName, IRequest $request, RaumMapper $mapper)
    {
        parent::__construct($appName, $request);
        $this->mapper = $mapper;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse
    {
        $raeume = $this->mapper->findAll();
        return new DataResponse($raeume);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id): DataResponse
    {
        try {
            $raum = $this->mapper->find($id);
            return new DataResponse($raum);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     */
    public function create(string $name): DataResponse
    {
        $raum = new Raum();
        $raum->setRaumName($name);
        $raum = $this->mapper->insert($raum);
        return new DataResponse($raum);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $name
     */
    public function destroy(int $id): DataResponse
    {
        try {
            $raum = $this->mapper->find($id);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($raum);
        return new DataResponse($raum);
    }
}
