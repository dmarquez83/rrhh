<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Bells;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

class BellsController extends Controller {

    public function index()
    {
        $bells = Bells::all();
        return $bells;
    }


    public function store()
    {
        $bells = Input::all();

        //var_dump($bells, 'SOY DEL CONTROLLER HTTP');
        foreach($bells as $bell){
            Bells::create($bell);
        }
        return ResultMsgMaker::saveSuccess();
    }


    public function getBells() {

        //$Bells = DB::table('ScheduleConfiguration')->select('_id', 'countBell', 'hourBell','typeBell')->get();

        $Bells = Bells::get();
        return $Bells;
    }

    public function destroy($id)
    {
        $canRemove = DocumentReferenceVerificator::verify("bells_id", $id, ['Bells']);

        //var_dump($canRemove, 'puedo borrar ', $id);

        if($canRemove === true){
            if ($bell = Bells::find($id)->delete()){
                   return ResultMsgMaker::deleteSuccess();
                } else {
                    return ResultMsgMaker::error();
                }
            }
        else {
            $modelName = $canRemove['modelName'];
            $modelName = Lang::get('modelNames.'.$modelName);

            return ResultMsgMaker::errorCannotDelete('el', 'timbre', '', $modelName);
        }
    }

}
