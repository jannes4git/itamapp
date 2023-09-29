<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Db\CustomField;
use OCP\AppFramework\Http;
use OCA\ItamApp\Db\CustomFieldMapper;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class CustomFieldController extends Controller
{
    private CustomFieldMapper $mapper;


    public function __construct(string $appName, IRequest $request, CustomFieldMapper $mapper)
    {
        parent::__construct($appName, $request);
        $this->mapper = $mapper;
    }

    /**
     * @NoAdminRequired
     * Gibt sowohl alle CustomFields als auch alle CustomFieldValues zurück.
     */
    public function index(): DataResponse
    {
        $cf = $this->mapper->findAllCustomFieldValues();
        $fields = $this->mapper->findAllCustomFields();
        return new DataResponse(array($fields, $cf));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id): DataResponse
    {
        try {
            return new DataResponse($this->mapper->find($id));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param string $type
     */
    public function create(string $name, string $type)
    {
        $customField = new CustomField();
        $customField->setName($name);
        $customField->setType($type);
        $id = $this->mapper->insert($customField);
        return $id;
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
        try {
            $cf = $this->mapper->find($id);
            $this->mapper->delete($cf);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        return $id;
    }
}
