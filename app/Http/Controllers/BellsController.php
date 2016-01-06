<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Bells;
use Illuminate\Http\Request;

class BellsController extends Controller {

    public function index()
    {
        $bells = Bells::all();
        return $bells;
    }

    public function store()
    {
        $bell = Input::all();
        $bellCreated = Bells::create($bell);
        if($bellCreated){
            return ResultMsgMaker::saveSuccess();
        }else{
            return ResultMsgMaker::error();
        }
    }

    public function update($id)
    {
        $bell = Input::all();
        $savedBell = Bells::find($id);
        if($savedBell->update($bell)){
            return ResultMsgMaker::updateSuccess();
        }else{
            return ResultMsgMaker::error();
        }
    }

    public function getBells() {

        //$Bells = DB::table('ScheduleConfiguration')->select('_id', 'countBell', 'hourBell','typeBell')->get();

        $Bells = Bells::get();

        return $Bells;
    }

  /*  public function destroy($id)
    {
        $canRemove = DocumentReferenceVerificator::verify("bank_id", $id, ['BankAccount']);
        if($canRemove === true){
            if ($bank = Bank::find($id)->delete()) {
                return ResultMsgMaker::deleteSuccess();
            } else {
                return ResultMsgMaker::error();
            }
        } else {
            $modelName = $canRemove['modelName'];
            $modelName = Lang::get('modelNames.'.$modelName);

            return ResultMsgMaker::errorCannotDelete('el', 'banco', '', $modelName);
        }
    }*/


    public function forTable()
   {
       $params = Input::all();
       $draw = $params['draw'];
       $start = $params['start'];
       $length = $params['length'];

       $columOrderIndex = $params['order'][0]['column'];
       $columOrderDir = $params['order'][0]['dir'];
       $columOrderName = $params['columns'][$columOrderIndex]['data'];

       $searchValue = $params['search']['value'];

       $totalRecords = ScheduleConfiguration::count();
       $recordsFiltered = ScheduleConfiguration::count();
       $bells = ScheduleConfiguration::skip($start)
           ->take($length)
           ->orderBy($columOrderName, $columOrderDir)
           ->get();

       if($searchValue!=''){
           $bells = $bells->filter(function($customer) use($searchValue){
               if (stripos($customer, $searchValue)) {return true;};
               return false;
           })->values();
           $recordsFiltered = $bells->count();
       }

       $returnData = [
           'draw' => $draw,
           'recordsTotal' => $totalRecords,
           'recordsFiltered' => $recordsFiltered,
           'data' => $bells];
       return $returnData;
   }


}
