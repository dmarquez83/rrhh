<?php namespace App\Http\Controllers;


use App\Helpers\DataValidator;
use App\Helpers\ApprovalDocument;
use App\Models\DocumentConfiguration;
use App\Models\SalesOffer;
use App\Models\Customer;
use App\Models\CompanyInfo;
use Illuminate\Support\Facades\Session;
use App\Helpers\ResultMsgMaker;
use App\Helpers\SystemConfiguration;
use App\Helpers\BusinessPartner;
use MongoId;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;

class ExcelController extends Controller {

	private $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

	public function html()
	{
		$salesOffer = SalesOffer::warehouse()->where('number', '001-001-000000006')->first();
    $customer = Customer::find($salesOffer['customer_id']);
    $customerName = BusinessPartner::getName($customer->toArray());
    $companyInfo = CompanyInfo::first();
    $salesOfferDetails = $salesOffer->details ? $salesOffer->details : '';
    $seller = '';
    $data = [
      'companyInfo' => $companyInfo,
      'document' => $salesOffer->toArray(),
      'documentDetails' => $salesOfferDetails,
      'customer' => $customer->toArray(),
      'customerName' => $customerName,
      'seller' => $seller,
      'products' => $salesOffer['products']
    ];
		return view('pdf.salesOffer', $data);
	}

	public function invoice()
  {
      $data = Input::all();
			$fileName = $data['fileName'];
			$columnsFormats = $this->getColumnsFormat($data['head']);
			$filePath = \Excel::create($fileName, function($excel) use($data, $columnsFormats){
				$excel->setTitle($data['title']);
    		$excel->setCreator('sae-erp');
				$excel->sheet($data['title'], function($sheet) use($data, $columnsFormats) {
					$sheet->setColumnFormat($columnsFormats);
					$sheet->setOrientation('landscape');
					$sheet->fromArray($data['data'], null, 'A1', false, false);
					$sheet->prependRow($data['head']);
					$sheet->setAllBorders('solid');
					$sheet->row(1, function($row) {
					  $row->setBackground('#dddbdb');
						$row->setFontSize(14);
						$row->setFontWeight('bold');
						$row->setAlignment('center');
						$row->setValignment('middle');
					});
					$sheet->freezeFirstRow();
					$sheet->setAutoSize(true);

		    });
			})
			->store('xlsx', public_path('exports'), true);

			//return response()->download($filePath['full']);
			return $filePath['full'];
  }

	public function getColumnsFormat($head)
	{
		$columnFormats = [];
		foreach ($head as $key => $columTittle) {
			if (strpos($columTittle, 'fecha') !== false || strpos($columTittle, 'Fecha') !== false ){
				array_push($columnFormats, 'yyyy-mm-dd');
			} else if (strpos($columTittle, 'cantidad') !== false  || strpos($columTittle, 'Cantidad') !== false ){
				array_push($columnFormats, '0.00');
			} else if (strpos($columTittle, 'IVA') !== false  || strpos($columTittle, 'iva') !== false ){
				array_push($columnFormats, '"$"#,##0.00_-');
			}	else if (strpos($columTittle, 'total') !== false  || strpos($columTittle, 'Total') !== false) {
				array_push($columnFormats, '"$"#,##0.00_-');
			} else if (strpos($columTittle, 'subtotal') !== false  || strpos($columTittle, 'Subtotal') !== false ){
				array_push($columnFormats, '"$"#,##0.00_-');
			} else if (strpos($columTittle, 'descuento') !== false || strpos($columTittle, 'Descuento') !== false ){
				array_push($columnFormats, '0.00');
			} else {
				array_push($columnFormats, '@');
			}
		}

		$finalColumnsFormats = [];
		foreach ($columnFormats as $key => $format) {
			$finalColumnsFormats[$this->alphabet[$key]] = $format;
		}

		return $finalColumnsFormats;
	}

}
