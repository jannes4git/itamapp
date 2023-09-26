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

        // Check ob Inventarnummern bereits vergeben sind
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
        $inventarnummerAndRechnungsdatumMissing = array();
        foreach ($data as $index => &$item) {
            if (($item['inventarnummer'] == null || $item['inventarnummer'] == "") && ($item['rechnungsdatum'] == null || $item['rechnungsdatum'] == "")) {
                array_push($inventarnummerAndRechnungsdatumMissing, $index + 2);
            }
        }
        if (count($inventarnummerAndRechnungsdatumMissing) > 0) {
            $response = [
                "message" => "Inventarnummer und Rechnungsdatum fehlen bei Assets.",
                "zeilen" => $inventarnummerAndRechnungsdatumMissing,
            ];
            return new DataResponse($response, Http::STATUS_CONFLICT);
        }


        $count = 0;
        $fehlerBeiAsset = array();

        foreach ($data as $index => &$item) {
            //TODO: vielleicht erstmal Defaultrechnungsdatum setzen?
            //if ($item['inventarnummer'] == null && $item['rechnungsdatum'] == null) {
            //    $response = [
            //        "message" => "Rechnungsdatum fehlt bei Asset -> Inventarnummer kann nicht erstellt werden"
            //    ];
            //    return new DataResponse($response, Http::STATUS_CONFLICT);
            //}
            if ($item['inventarnummer'] == null && $item['inventarnummer'] == "") {
                try {
                    $item['inventarnummer'] = $this->assetService->generateInventarnummer($item['rechnungsdatum']);
                } catch (Exception $e) {
                    array_push($fehlerBeiAsset, $count);
                    continue;
                }
            }
            try {
                $id = $this->assetService->create($item['inventarnummer'], $item['rechnungsdatum'], $item['seriennummer'], $item['locationId'], $item['personId'], $item['customFieldValues']);
            } catch (Exception $e) {
                //array_push($fehlerBeiAsset, $count);
                //$response = [
                //    "message" => "Fehler beim Erstellen des Assets mit der Inventarnummer: " . $item['inventarnummer'],
                //    "error" => $e->getMessage(),
                //];
                //return new DataResponse($response, Http::STATUS_INTERNAL_SERVER_ERROR);
            }
            $count++;
            unset($item);
        }
        if (count($fehlerBeiAsset) > 0) {
            $response = [
                "message" => "Fehler beim Erstellen der Assets mit den Inventarnummern: ",
                "fehlerBeiAsset" => $fehlerBeiAsset,
            ];
            return new DataResponse($response, Http::STATUS_INTERNAL_SERVER_ERROR);
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
