<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Department extends Moloquent{

  protected $collection = 'Departments';
  protected $guarded = array();

  public function  __construct(array $attributes = [])
  {
    $userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    parent::__construct($attributes);
  }

  public function employee()
  {
    return $this->hasMany('App\Models\Employee', 'department_id', '_id');
  }

}
