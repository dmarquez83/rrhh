<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Bank;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for Bank Model
|--------------------------------------------------------------------------
*/

class BankController extends Controller {


	public function index()
	{
		$banks = Bank::all();
		return $banks;
	}

	public function store()
	{
    $bank = Input::all();
    $bankCreated = Bank::create($bank);
    if($bankCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
	  }
	}

	public function update($id)
	{
		$bank = Input::all();
    $savedBank = Bank::find($id);
	  if($savedBank->update($bank)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function destroy($id)
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
	}
}
