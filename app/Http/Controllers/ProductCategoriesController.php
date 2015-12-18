<?php namespace App\Http\Controllers;

use App\Models\ProductsCategory;
use App\Helpers\ResultMsgMaker;
use App\Helpers\DocumentReferenceVerificator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| USE WAREHOUSE for ProductsCategory Model
|--------------------------------------------------------------------------
*/

class ProductCategoriesController extends Controller {


	public function index()
	{
		$productCategories = ProductsCategory::warehouse()->get();
		$productCategoriesWithParent = $productCategories->map(function($category){
			$newCategory = $category;
			if($category->parentCategory_id){
				$newCategory->parent = ProductsCategory::find($category->parentCategory_id)->toArray();
			}
			return $newCategory;
		});
		return $productCategoriesWithParent;
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

    $totalRecords = ProductsCategory::warehouse()->count();
    $recordsFiltered = $totalRecords;
    $productCategories = ProductsCategory::warehouse()->skip($start)
      ->take($length)
      ->orderBy($columOrderName, $columOrderDir)
      ->get();

    $productCategories = $productCategories->map(function($productCategory){
      $newProductCategory = $productCategory;
      if($productCategory->parentType_id){
        $newProductCategory->parent = ProductsCategory::find($productCategory->parentType_id)->toArray();
      }
      return $newProductCategory;
    });

    if($searchValue!=''){
      $productCategories = $productCategories->filter(function($productCategory) use($searchValue){
        if (stripos($productCategory, $searchValue)) {return true;};
        return false;
      })->values();
      $recordsFiltered = $productCategories->count();
    }

    $returnData = array(
      'draw' => $draw,
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $recordsFiltered,
      'data' => $productCategories);
    return $returnData;
  }

  public function massiveLoad()
  {
    $productCategories = Input::all();

    if(is_array($productCategories)) {

      foreach ($productCategories as $productCategory) {

        if (!ProductsCategory::create($productCategory)) {
          return ResultMsgMaker::error();
        }
      }
    }
    return ResultMsgMaker::saveSuccess();
  }

	public function store()
  {
    $productsCategory = Input::all();
    if(ProductsCategory::create($productsCategory)){
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

	public function update($id)
	{
		$productsCategory = Input::all();
    $savedProductsCategory = ProductsCategory::find($id);
    if($savedProductsCategory->update($productsCategory)){
      return ResultMsgMaker::updateSuccess();
    } else {
      return ResultMsgMaker::error();
    }

	}

	public function destroy($id)
  {
    $canRemove = DocumentReferenceVerificator::verify(['productCategory_id', 'parentCategory_id'], $id, ['Product', 'ProductsCategory']);

    if($canRemove === true){
      if (ProductsCategory::find($id)->delete()) {
        return ResultMsgMaker::deleteSuccess();
      } else {
        return ResultMsgMaker::error();
      }
    } else {
      $modelName = $canRemove['modelName'];
      $modelName = Lang::get('modelNames.'.$modelName);

      return ResultMsgMaker::errorCannotDelete('la', 'categoría', '', $modelName);
    }
  }

}
