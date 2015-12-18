<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\BankAccount;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for BankAccount Model
|--------------------------------------------------------------------------
*/

class BankAccountController extends Controller {

	public function index()
	{
		$bankAccounts = BankAccount::with('bank', 'ledgerAccount')->get();
		return $bankAccounts;
	}

	public function store()
	{
      $bankAccount = Input::all();
      $bankAccountCreated = BankAccount::create($bankAccount);
      if($bankAccountCreated){
      	return ResultMsgMaker::saveSuccess();
      }
      return ResultMsgMaker::error();
	}

	public function update($id)
	{
		$bankAccount = Input::all();
		$savedBankAccount = BankAccount::find($id);  
    if($savedBankAccount->update($bankAccount)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function destroy($id)
	{
    $canRemove = DocumentReferenceVerificator::verify("bankAccount_id", $id, ['CheckbookRegister']);
    if($canRemove === true){
      if ($bankAccount = BankAccount::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'cuenta bancaria', '', $modelName);
    }
	}

}
