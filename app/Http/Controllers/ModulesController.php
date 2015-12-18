<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Module;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class ModulesController extends Controller {

    public function index() {
        return Module::orderBy('name', 'asc')->get();
    }

    public function store() {
        
        $module = Input::all();

        $result = array(
            'type' => 'success',
            'msg' => 'El módulo se ha guardado con éxito');

        if (!Module::create($module)) {
            $result['type'] = 'error';
            $result['msg'] = 'Ocurrió un problema al guardar el módulo';
        }
        
        return $result;
    }

    public function update($id) { 
        
        $data = Input::all();
        $module = Module::find($id);
        $module->update($data);

        $result = array(
            'type' => 'success',
            'msg' => 'Se ha actualizado el modulo');

        if (!$module->save()) {
            $result['type'] = 'error';
            $result['msg'] = 'Ocurrió un problema al actualizar el módulo';
        }

        return $result;
    }


    public function destroy($id) {
        
        $result = array(
            'type' => 'success',
            'msg' => 'Se ha eliminado el modulo con exito');

        if (!Module::destroy($id)) {
            $result['type'] = 'error';
            $result['msg'] = 'Ocurrió un problema al eliminar el módulo';
        }
        
        return $result;
    }

}
