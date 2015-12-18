<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class AuditController extends Controller {


    public function index() {

        $notShownCollections = array('Module', 'Role', 'Users', 'migrations');//anadir aqui las colecciones que no se quieran mostrar
        $db = DB::getMongoDB();
        $collections = $db->listCollections();
        $response = array();

        foreach ($collections as $collection) {
            if(!in_array($collection->getName(), $notShownCollections)){
                array_push($response, array(
                    'name' => $collection->getName(),
                    'totalDocuments' => $collection->count()
                ));
            }
        }

        return $response;
    }


    public function search() {

        $data = Input::all();

        if(!empty($data['collections'])){

            $startDate = new MongoDate(strtotime($data['startDate']));
            $endDate = new MongoDate(strtotime($data['endDate'].' 23:59:59'));
            $actions = !empty($data['actions']) ? $data['actions'] : NULL;

            $records = array();
            $filteredRecords = NULL;
            $recordsTotal = 0;
            $recordsFiltered = 0;
            $inflector = new Inflector();

            foreach ($data['collections'] as $collectionName) {
                $collectionName = $inflector->singularize($collectionName);

                $collection = $collectionName::where(function ($query) use ($startDate, $endDate, $actions){
                    if(is_null($actions)){
                        $query->whereBetween('created_at', array($startDate, $endDate));
                    }else{
                        foreach ($actions as $action) {
                            $query->whereBetween($action, array($startDate, $endDate));
                        }
                    }
                })
                ->select('_id', 'user_id', 'warehouse', 'created_at', 'updated_at', 'deleted_at')
                ->skip($data['start'])
                ->take($data['length'])
                ->orderBy($data['columns'][$data['order'][0]['column']]['data'], $data['order'][0]['dir'])
                ->get();

                $recordsTotal += $collection->count();

                $searchString = $data['search']['value'];

                if(!empty($searchString)){
                    $filteredRecords = $collection->filter(function($result) use($searchString){
                      return stripos($result, $searchString);
                    })->values();
                    $recordsFiltered += $filteredRecords->count();
                }

                $array = !is_null($filteredRecords) ? $filteredRecords : $collection;

                foreach ($array as &$element){
                    $element->collection = $collectionName;
                    if(!empty($element->user_id)){
                        $element->username = User::find($element->user_id)->username;
                    }
                    if(!empty($element->warehouse)){
                        $element->warehouse = Warehouse::find($element->warehouse)->name;
                    }
                    array_push($records, $element);
                }
            }

            return array(
                'draw' => $data['draw'],
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered > 0 ? $recordsFiltered : $recordsTotal,
                'data' => $records
            );
        }
    }


}
