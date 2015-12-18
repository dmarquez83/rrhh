<?php namespace App\Models;

use Illuminate\Support\Facades\Session;
use Jenssegers\Mongodb\Model as Moloquent;

class BankAccount extends Moloquent{
  protected $collection = 'BankAccount';
  protected $guarded = ['bank', 'ledger_account'];

  public function  __construct(array $attributes = [])
  {
  	$userInfo = Session::get('userInformation');
    $this->attributes['user_id'] = $userInfo['systemUser']['_id'];
    parent::__construct($attributes);
  }

  public function bank()
  {
    return $this->belongsTo('App\Models\Bank', 'bank_id', '_id');
  }

  public function ledgerAccount()
  {
    return $this->belongsTo('App\Models\Statement', 'ledgerAccount_id', '_id');
  }
}
