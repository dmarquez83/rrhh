<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Configuration;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class ConfigurationController extends Controller {


	public function index()
	{
		$configurations = Configuration::all();
		return $configurations;
	}


	public function store()
	{
		Config::set('database.default', 'matriz');
    $action = Input::get('action');
    $configuration = Input::get('configuration');

    if ($action == 'save') {
      $resultado = array(
      	'type' => 'success',
      	'msg' => 'El cliente se ha guardado con Exito');

      if (!Configuration::create($configuration)) {
      	$resultado['type'] = 'danger';
      	$resultado['msg'] = 'Ocurrio un Problema al guardar la configuracion';
      	return $resultado;
      }
      return $resultado;
    }
    
    if($action == 'update'){
      return $this->update($configuration);
    }  
	}


	public function update($data)
	{
		$configuration = Configuration::first();
		$configuration->update($data);

    $resultado = array(
    	'type' => 'success',
    	'msg' => 'Se ha guardado la configuración');

    if (!$configuration->save()) {
    	$resultado['type'] = 'danger';
    	$resultado['msg'] = 'Ocurrio un problema al guardadar la configuración';
    	return $resultado;
    }
    
    return $resultado;
	}


}
