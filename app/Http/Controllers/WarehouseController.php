<?php namespace App\Http\Controllers;


use App\Helpers\DocumentConfigurationMaker;
use App\Helpers\DocumentReferenceVerificator;
use App\Helpers\PriceListMaker;
use App\Models\Warehouse;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class WarehouseController extends Controller {

	public function index()
	{
		$warehouses = Warehouse::all();
		return $warehouses;
	}

	public function store()
	{
      $warehouse = Input::all();
      $warehouseCreated = Warehouse::create($warehouse);
      if($warehouseCreated){
      	DocumentConfigurationMaker::generateDocumentConfiguration($warehouseCreated->_id);
      	return ResultMsgMaker::saveSuccess();
      }else{
      	return ResultMsgMaker::error();
      }
	}

	public function update($id)
	{
		$warehouse = Input::all();
		$savedWarehouse = Warehouse::find($id);
    if($savedWarehouse->update($warehouse)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
	}


  public function destroy($id)
  {
		$modelList = ['User', 'Customer', 'Supplier', 'Product', 'ProductsCategory', 'PricesList'];
    $canRemove = DocumentReferenceVerificator::verify("warehouse_id", $id, $modelList);
    if($canRemove === true){
      if ($warehouse = Warehouse::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'bodega', '', $modelName);
    }

  }

  public function getByParameterPost()
  {
    $parameter = Input::all();
    $accounts = Warehouse::where($parameter['parameter'], '=', $parameter['value'])->get();
    return $accounts;
  }

}
