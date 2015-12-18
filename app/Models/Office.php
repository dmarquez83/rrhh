<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class Office extends Moloquent{
  protected $collection = 'Offices';
  protected $guarded = ['department'];

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

  public function employee()
  {
    return $this->hasMany('App\Models\Employee', 'deparment_id', '_id');
  }

}
