<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class CreditCard extends Moloquent{

  protected $collection = 'CreditCards';
  protected $guarded = ['employee'];

  public function  __construct(array $attributes = [])
  {
    $userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    parent::__construct($attributes);
  }

  public function employee()
  {
    return $this->belongsTo('App\Models\Employee', 'employee_id', '_id');
  }

  public function ledgerAccount()
  {
    return $this->belongsTo('App\Models\Statement', 'documentsLedgerAccount_id', '_id');
  }

}
