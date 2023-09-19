<?php
 namespace OCA\ItamApp\Controller;

use Exception;
use OCA\ItamApp\Db\Asset;
use OCP\AppFramework\Http;
use OCA\ItamApp\Db\ColumnMapper;
use OCA\ItamApp\Db\CustomFieldMapper;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

 class MetaController extends Controller {
    

    private ColumnMapper $mapper;
    private CustomFieldMapper $cfMapper;
     public function __construct(string $appName, IRequest $request, ColumnMapper $mapper, CustomFieldMapper $cfMapper){
         parent::__construct($appName, $request);
         $this->mapper=$mapper;
         $this->cfMapper=$cfMapper;
     }

     /**
      * @NoAdminRequired
      */
     public function index() : DataResponse {
        //Nur die übermitteln die auch gemappt werden sollen?
        $columns = $this->mapper->getColumns();
        $customColumns = $this->cfMapper->findAllCustomFields();
        return new DataResponse(array($columns, $customColumns));
     }

     /**
      * @NoAdminRequired
      *
      * @param int $id
      */
     public function show(int $id) : DataResponse{
        try {
            return new DataResponse(null);
        } catch(Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
     }

     /**
      * @NoAdminRequired
      */
     public function create() {
        //$_REQUEST
        //$_FILES['data']['type'];
        $csvData = $this->request->getParams();
        //$columns = $this->mapper->getColumns();
        $data = null;
        //return new DataResponse(array($columns, $data));
        return new DataResponse($_FILES);
     }

     /**
      * @NoAdminRequired
      *
      * @param int $id
      * @param string $date
      * @param string $beschreibung
      */
     public function update(int $id, string $date, string $beschreibung) {
         // empty for now
     }

     /**
      * @NoAdminRequired
      *
      * @param int $id
      */
     public function destroy(int $id) {
         // empty for now
     }

 }
