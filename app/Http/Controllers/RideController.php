<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RideController extends Controller {


	public function index()
	{
		$pdf = \PDF::loadView('pdf.ride', [])->setPaper('a4');
    return $pdf->stream('invoice');
		//return view('pdf.ride', []);
	}

}
