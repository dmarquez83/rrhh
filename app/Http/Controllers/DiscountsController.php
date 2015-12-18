<?php namespace App\Http\Controllers;

use App\Helpers\DocumentReferenceVerificator;
use App\Models\Discount;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;


class DiscountsController extends Controller {

  public function index()
  {
    $discounts = Discount::all();
    return $discounts;
  }

  public function store()
  {
    $discount = Input::all();
    $discountCreated = Discount::create($discount);
    if($discountCreated){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function update($id)
  {
    $discount = Input::all();
    $savedDiscount = Discount::find($id);
    if($savedDiscount->update($discount)){
      return ResultMsgMaker::updateSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify("discounts_id", $id, ['Employee']);
    if($canRemove === true){
      if ($discounts = Discount::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('el', 'departamento', '', $modelName);
    }
  }
}
