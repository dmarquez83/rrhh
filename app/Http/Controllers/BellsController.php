<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Bells;
use App\Helpers\ResultMsgMaker;
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
        var_dump($bells);
        foreach($bells as $bell){
            Bells::create($bell);
        }
        return ResultMsgMaker::saveSuccess();
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
}
