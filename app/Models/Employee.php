<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Employee extends Moloquent{

  protected $collection = 'Employees';
  protected $guarded = ['department','office','maritalStatus', 'bank'];

  public function  __construct(array $attributes = [])
  {
    $userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    parent::__construct($attributes);
  }

  public function department()
  {
    return $this->belongsTo('App\Models\Department', 'department_id', '_id');
  }

  public function office()
  {
    return $this->belongsTo('App\Models\Office', 'office_id', '_id');
  }

  public function maritalStatus()
  {
    return $this->belongsTo('App\Models\MaritalStatus', 'maritalStatus_id', '_id');
  }

  public function bank()
  {
    return $this->belongsTo('App\Models\Bank', 'bank_id', '_id');
  }

}
