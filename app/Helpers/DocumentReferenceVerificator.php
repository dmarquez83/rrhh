<?php namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DocumentReferenceVerificator{

  public static function verify($fieldName, $fieldValue, $models){

    foreach ($models as $modelName) {
        $injectModelName = 'App\\Models\\'.$modelName;
        if (is_array($fieldName)){
          foreach ($fieldName as $key => $field) {
            $result = $injectModelName::where($field, '=', $fieldValue)->get();
            if ($result->count() > 0) {
              return ['modelName' => $modelName];
            }
          }
        } else {
          $result = $injectModelName::where($fieldName, '=', $fieldValue)->get();
          if ($result->count() > 0) {
            return ['modelName' => $modelName];
          }
        }

    }

    return true;
  }

}
