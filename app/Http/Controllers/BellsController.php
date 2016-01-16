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
        Bells::where('countBell', '>', 0)->delete();
        foreach($bells as $bell){
            Bells::create($bell);        }
        return ResultMsgMaker::saveSuccess();
    }


    public function getBells() {

        //$Bells = DB::table('ScheduleConfiguration')->select('_id', 'countBell', 'hourBell','typeBell')->get();

        $Bells = Bells::get();
        return $Bells;
    }

    public function destroy($id)
    {

        if ($bell = Bells::find($id)->delete()){
            return ResultMsgMaker::deleteSuccess();
        } else {
            return ResultMsgMaker::error();
        }
    }

}
