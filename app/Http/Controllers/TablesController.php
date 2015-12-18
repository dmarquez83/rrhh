<?php namespace App\Http\Controllers;

use App\Helpers\ResultMsgMaker;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Tables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TablesController extends Controller {

	public function index()
	{
    $tables = Tables::first();
    return $tables->tables;
	}

	public function store()
	{
    $tables  = Input::all();
    $newTables = ["tables" => $tables];
    Tables::truncate();
    if(Tables::create($newTables)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function update($id)
	{
    $office = Input::all();
    $savedOffice = Tables::find($id);
    if($savedOffice->update($office)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}

	public function destroy($id)
	{

	}

}
