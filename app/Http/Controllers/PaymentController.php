<?php namespace App\Http\Controllers;

use App\Helpers\DataValidator;
use App\Models\Payment;
use App\Helpers\ResultMsgMaker;
use Illuminate\Support\Facades\Input;

class PaymentController extends Controller {

	public function index()
	{
		$payments = Payment::all();
		return $payments;
	}

}