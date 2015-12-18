<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\CheckbookRegister;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;


/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for ChecbkBookRegister Model
|--------------------------------------------------------------------------
*/

class CheckbookRegisterController extends Controller {

	public function index()
	{
		$checkbookRegisters = CheckbookRegister::with(['bankAccount' => function($query){
      $query->with('bank');
    }])->get();
		return $checkbookRegisters;
	}

	public function store()
	{
    $checkbookRegister = Input::all();
    $checkbookRegisterCreated = CheckbookRegister::create($checkbookRegister);
    if($checkbookRegisterCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function update($id)
	{
		$checkbookRegister = Input::all();
		$savedCheckbookRegister = CheckbookRegister::find($id);
    if($savedCheckbookRegister->update($checkbookRegister)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function destroy($id)
  {
    $canRemove = true;
    if ($canRemove === true) {
      if ($bankAccount = CheckbookRegister::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.' . $modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'chequera', '', $modelName);
    }
  }
}

