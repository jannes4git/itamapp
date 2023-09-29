<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCP\AppFramework\Http;
use OCA\ItamApp\Db\CustomFieldMapper;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class MetaController extends Controller
{


    private CustomFieldMapper $cfMapper;
    public function __construct(string $appName, IRequest $request, CustomFieldMapper $cfMapper)
    {
        parent::__construct($appName, $request);
        $this->cfMapper = $cfMapper;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse
    {
        $customColumns = $this->cfMapper->findAllCustomFields();
        return new DataResponse($customColumns);
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
    public function create()
    {
        $csvData = $this->request->getParams();
        return new DataResponse(null);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function update(int $id)
    {
        // empty for now
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
