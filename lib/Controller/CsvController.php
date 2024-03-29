<?php

namespace OCA\ItamApp\Controller;

use Exception;
use OCP\AppFramework\Http;
use OCA\ItamApp\Service\AssetService;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;


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
        return new DataResponse($csvData);
    }

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
     * NOTE: Eine Transaktionsfunktion die bei Fehlern einen rollback macht wäre sinnvoll
     */
    public function create()
    {
        // Lese den POST-Body
        $postData = file_get_contents('php://input');

        // Konvertiere den POST-Body in ein PHP-Array
        $data = json_decode($postData, true);

        // Check ob Inventarnummern bereits vergeben sind
        $inventarnummernExisting = array();
        foreach ($data as $index => &$asset) {
            if ($asset['inventarnummer'] !== null && $asset['inventarnummer'] !== "") {
                $exists = $this->assetService->inventarnummerExistsCheck($asset['inventarnummer']);
                if ($exists) {
                    array_push($inventarnummernExisting, $asset['inventarnummer']);
                }
            }
            unset($asset);
        }
        if (count($inventarnummernExisting) > 0) {
            $response = [
                "message" => "Inventarnummern bereits vergeben:",
                "existingInventarnummern" => $inventarnummernExisting,
            ];
            return new DataResponse($response, Http::STATUS_CONFLICT);
        }

        //Check ob Inventarnummern und Rechnungsdaten fehlen 
        //  -> Inventarnummer kann weder importiert noch generiert werden
        $inventarnummerAndRechnungsdatumMissing = array();
        foreach ($data as $index => &$asset) {
            if (($asset['inventarnummer'] == null || $asset['inventarnummer'] == "") && ($asset['rechnungsdatum'] == null || $asset['rechnungsdatum'] == "")) {
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

        foreach ($data as $index => &$asset) {
            if ($asset['inventarnummer'] == null && $asset['inventarnummer'] == "") {
                try {
                    $asset['inventarnummer'] = $this->assetService->generateInventarnummer($asset['rechnungsdatum']);
                } catch (Exception $e) {
                    array_push($fehlerBeiAsset, $count);
                    continue;
                }
            }
            try {
                $id = $this->assetService->create($asset['inventarnummer'], $asset['rechnungsdatum'], $asset['seriennummer'], $asset['locationId'], $asset['personId'], $asset['customFieldValues']);
            } catch (Exception $e) {
                //TODO: rollback
            }
            $count++;
            unset($asset);
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
     */
    public function update(int $id, string $date, string $beschreibung)
    {
        // empty for now
    }

    /**
     * @NoAdminRequired
     */
    public function destroy(int $id)
    {
        // empty for now
    }
}
