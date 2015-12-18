<?php namespace App\Helpers;

class ResultMsgMaker {

  public static function saveSuccess(){
    $msg = array('type'=>'success', 'msg'=>'El registro se guardó correctamente');
    return $msg;
  }

  public static function saveSuccessWithExtraData($extraData){
    $msg = array('type'=>'success', 'msg'=>'El registro se guardó correctamente');
    $msgWithExtraData = array_merge($msg, $extraData);
    return $msgWithExtraData;
  }

  public static function updateSuccess(){
    $msg = array('type'=>'success', 'msg'=>'El registro se actualizó correctamente');
    return $msg;
  }

  public static function deleteSuccess(){
    $msg = array('type'=>'success', 'msg'=>'El registro se eliminó correctamente');
    return $msg;
  }

  public static function annulSuccess(){
    $msg = array('type'=>'success', 'msg'=>'El registro se anuló correctamente');
    return $msg;
  }

  public static function error(){
    $msg = array('type'=>'error', 'msg'=>'Lo lamentamos, algo salió mal');
    return $msg;
  }

  public static function errorDuplicate(){
    $msg = array('type'=>'warning', 'msg'=>'Este registro ya existe en el sistema');
    return $msg;
  }

  public static function warningDuplicateField(){
    $article = func_get_arg(0);
    $field = func_get_arg(1);
    $value = func_get_arg(2);
    $msg = array('type'=>'warning', 'msg' => "$article $field $value ya existe");
    return $msg;
  }

  public static function errorCannotDelete(){
    $article = func_get_arg(0);
    $field = func_get_arg(1);
    $value = func_get_arg(2);
    $module = func_get_arg(3);
    $msg = array('type'=>'error', 'msg'=>"No se puede eliminar $article $field $value porque está siendo utilizado en el Módulo $module");
    return $msg;
  }

  public static function errorCustom($msg){
    $msg = array('type'=>'error', 'msg'=>"$msg");
    return $msg;
  }

  public static function warningCustom($msg){
    $msg = array('type'=>'warning', 'msg'=>"$msg");
    return $msg;
  }

  public static function successCustom($msg){
    $msg = array('type'=>'success', 'msg'=>"$msg");
    return $msg;
  }
}
