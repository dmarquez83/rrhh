<?php namespace App\Models;

use Jenssegers\Mongodb\Model as Moloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Company extends Moloquent {
  use SoftDeletes;

  protected $collection = 'Companies';
  protected $dates = ['deleted_at'];
  protected $guarded = array();
  protected $connection = 'sae';

}
