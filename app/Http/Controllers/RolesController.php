<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Role;
use App\Models\User;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class RolesController extends Controller {

    public function index() {
        return Role::all();
    }

    public function create() {
        //
    }

    public function store() {
        $role = Input::all();
        $roleCreated = Role::create($role);
        if($roleCreated){
            return ResultMsgMaker::saveSuccess();
        }else{
            return ResultMsgMaker::error();
        }
    }

    public function update($id)
    {
        $role = Input::all();
        $savedRole = Role::find($id);
        if($savedRole->update($role)){
          return ResultMsgMaker::updateSuccess();
        }else{
          return ResultMsgMaker::error();
        }
    }

    public function destroy($id) {
        $users = User::where('configuration.role_id', '=', $id)->get();
        if($users->count() == 0) {
            if($role = Role::find($id)->delete()){
              return ResultMsgMaker::deleteSuccess();
            } else {
              return ResultMsgMaker::error();
            }
        }else{
            return ResultMsgMaker::errorCannotDelete('el','rol','','Usuarios');
        }
    }

}
