<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Statement;
use App\Helpers\DocumentReferenceVerificator;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class StatementController extends Controller {


  public function index()
  {
    $statement = Statement::orderBy('code','asc')->get();

    return $statement;
  }

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

    $totalRecords = Statement::count();
    $recordsFiltered = $totalRecords;
    $finalStatement = [];

    if($searchValue!=''){
      $allLedgersAccounts = Statement::orderBy($columOrderName, $columOrderDir)->get();
      $finalStatement = $allLedgersAccounts->filter(function($ledgerAccount) use($searchValue){
        if (stripos($ledgerAccount, $searchValue)!== false) {return true;};
        return false;
      })->values();;
      $recordsFiltered = $finalStatement->count();
      $finalStatement = $finalStatement->slice($start, $length);

    } else {
      $finalStatement = Statement::skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $finalStatement);
    return $returnData;
  }


  public function store()
  {
    $ledger = Input::get('ledger');
    $ledgerCreated = Statement::create($ledger);
    if ($ledgerCreated) {
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $accounts = Statement::where($parameter['parameter'], '=', $parameter['value'])->get();
    return $accounts;
  }

  public function forSelectize()
  {
    $parameters = Input::all();
    $firstParameter = $parameters[0]['parameter'];
    $firstValue = $parameters[0]['value'];
    unset($parameters[0]);
    $otherParameters = $parameters;
    $accounts = Statement::where($firstParameter, '=', $firstValue)
      ->orWhere(function($query)  use ($parameters)
      {
        foreach ($parameters as $key => $parameter) {
          $query->where($parameter['parameter'], 'like', '%'.$parameter['value'].'%');
        }
      })
      ->limit(10)
      ->get();
    return $accounts;
  }


  public function show($code)
  {
    $account = Statement::where('code', '=', $code)->take(1)->get();
    return $account;
  }

  public function destroy($id)
  {
    $ledger = Statement::find($id);
    $canRemove = DocumentReferenceVerificator::verify(['parent'], $ledger->name, ['Statement']);

    if($canRemove === true) {
      $canRemove = DocumentReferenceVerificator::verify(['accountsLedgerAccount_id', 'documentsLedgerAccount_id','accountsLedgerAccount_id',
      'documentsLedgerAccount_id','expenseLedgerAccount_id','rentLedgerAccount_id','ledgerAccount_id', 'documentsLedgerAccount_id'
      ], $id, ['Customer','Customer','Supplier','Supplier', 'Product','Product', 'BankAccount', 'CreditCard']);
      if($canRemove === true){
        if (Statement::find($id)->delete()) {
          return ResultMsgMaker::deleteSuccess();
        } else {
          return ResultMsgMaker::error();
        }
      }else {
        $modelName = $canRemove['modelName'];
        $modelName = Lang::get('modelNames.'.$modelName);
        return ResultMsgMaker::errorCannotDelete('la', 'cuentas', '', $modelName);
      }
    }else{
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);
      return ResultMsgMaker::errorCannotDelete('la', 'cuenta', '', $modelName);
    }
  }


}
