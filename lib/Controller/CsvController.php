<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Db\Asset;
use OCP\AppFramework\Http;
use OCA\ItamApp\Service\AssetService;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCA\User_LDAP;
use OCA\User_LDAP\User_LDAP as User_LDAPUser_LDAP;

class CsvController extends Controller
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
        $csvData = $this->request->getParams();
        //$columns = $this->mapper->getColumns();
        $data = null;
        //return new DataResponse(array($columns, $data));
        return new DataResponse($csvData);
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
        // Lese den POST-Body
        $postData = file_get_contents('php://input');

        // Konvertiere den POST-Body in ein PHP-Array
        $data = json_decode($postData, true);

        //TODO: per Transaction rollbackable machen?
        $inventarnummernExisting = array();
        foreach ($data as $index => &$item) {
            if ($item['inventarnummer'] !== null && $item['inventarnummer'] !== "") {
                $exists = $this->assetService->inventarnummerExistsCheck($item['inventarnummer']);
                if ($exists) {
                    array_push($inventarnummernExisting, $item['inventarnummer']);
                }
            }
            unset($item);
        }
        if (count($inventarnummernExisting) > 0) {
            $response = [
                "message" => "Inventarnummern bereits vergeben:",
                "existingInventarnummern" => $inventarnummernExisting,
            ];
            return new DataResponse($response, Http::STATUS_CONFLICT);
        }

        //TODO: hier Problem da es zu Inventarnummerduplikaten kommen kann!!!!
        //foreach ($data as $item) {
        //    $id = $this->assetService->create($item['inventarnummer'], $item['rechnungsdatum'], $item['seriennummer'], $item['locationId'], $item['personId'], $item['customFieldValues']);
        //}
        $count = 0;
        foreach ($data as $index => &$item) {
            if ($item['inventarnummer'] == null && $item['inventarnummer'] == "") {
                $item['inventarnummer'] = $this->assetService->generateInventarnummer($item['rechnungsdatum']);
            }
            $id = $this->assetService->create($item['inventarnummer'], $item['rechnungsdatum'], $item['seriennummer'], $item['locationId'], $item['personId'], $item['customFieldValues']);
            $count++;
            unset($item);
        }

        return new DataResponse($count);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $date
     * @param string $beschreibung
     */
    public function update(int $id, string $date, string $beschreibung)
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
