<?php namespace App\Http\Controllers;

use App\Models\GeneralBalance;
use App\Helpers\ResultMsgMaker;
use App\Helpers\GeneralBalanceMaker;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for BillingTax Model
|--------------------------------------------------------------------------
*/

class IncomeStatementController extends Controller {

  public function forTable()
  {

    \DB::collection('GeneralBalance')->delete();

    $params = Input::all();
    $startDate = isset($params['startDate']) ? $params['startDate']: '';
    $endDate = isset($params['endDate']) ? $params['endDate']: '';

    if ($startDate != '' && $endDate != ''){
      GeneralBalanceMaker::generate([$startDate, $endDate]);
    }

    $ledgerAccounts = GeneralBalance::all();

    $salesAccount = GeneralBalance::where('accountName', '=', 'Ventas')->first()->toArray();
    $salesCost = GeneralBalance::where('accountName', '=', 'Costos de ventas')->first()->toArray();
    $salesDiscount = GeneralBalance::where('accountName', '=', 'Descuento en Ventas')->first();
    $salesReturn = GeneralBalance::where('accountName', '=', 'Devolución en Ventas')->first();

    $operationSales = GeneralBalance::where('accountAncestors', '=', ['RENTAS-INGRESOS', 'OPERATIVAS'])->get();



    $operationalExpenses = GeneralBalance::where('accountAncestors', '=', ['GASTOS-EGRESOS', 'OPERACIONALES'])->get();
    $marketingExpenses = GeneralBalance::where('accountAncestors', '=', ['GASTOS-EGRESOS', 'COMERCIALIZACION'])->get();

    $childOfExpenses = GeneralBalance::where('parent', '=', 'GASTOS-EGRESOS')->whereNotIn('name', ['OPERACIONALES', 'COMERCIALIZACION'])->get();
    $childOfRevenues = GeneralBalance::where('parent', '=', 'RENTAS-INGRESOS')->whereNotIn('name', ['OPERATIVAS'])->get();


    $otherExpensesParents = [];
    foreach ($childOfExpenses as $key => $expense) {
      array_push($otherExpensesParents, $expense['name']);
    }

    $otherRevenuesParents = [];
    foreach ($childOfRevenues as $revenue) {
      array_push($otherRevenuesParents, $revenue['name']);
    }

    $otherExpenses = [];
    $otherRevenues = [];
    if (count($otherExpensesParents) > 0) {
      $otherExpenses = GeneralBalance::whereIn('parent', '=', $otherExpensesParents)->get();
    }
    if (count($otherRevenuesParents) > 0) {
      $otherRevenues = GeneralBalance::whereIn('parent', '=', $otherRevenuesParents)->get();
    }
    
    $incomeStatement = [];
    

    $incomeStatement['operationalSales']['grossSales'] = round($salesAccount['creditBalance'], 2);
    $incomeStatement['operationalSales']['backInSales'] = round($salesReturn['debitBalance'], 2);
    $incomeStatement['operationalSales']['discountInSales'] = round($salesDiscount['debitBalance'], 2);
    $incomeStatement['operationalSales']['netSales'] = round($salesAccount['creditBalance'] - $salesDiscount['debitBalance'] - $salesReturn['debitBalance'], 2);

    $incomeStatement['operationalSales']['salesCost'] = round($salesCost['debitBalance'], 2);

    $incomeStatement['operationalSales']['grossProfitOnSales'] = round($incomeStatement['operationalSales']['netSales'] - $incomeStatement['operationalSales']['salesCost'], 2);

    $totalExtraOperationalSalesAccount = 0;
    foreach($operationSales  as $operationSaleAccount){
      $totalAccount = isset($expense['debitBalance']) ? $expense['debitBalance'] : $expense['creditBalance'];
      $totalExtraOperationalSalesAccount += $totalAccount;
      $incomeStatement['operationalSales']['extraAccounts'][$operationSaleAccount['accountName']] = $totalAccount;
    }

    $incomeStatement['operationalSales']['grossProfitOnSalesTotal'] = round($incomeStatement['operationalSales']['grossProfitOnSales'] - $totalExtraOperationalSalesAccount, 2);



    $incomeStatement['operationalExpenses']['total'] = 0;
    $incomeStatement['operationalExpenses']['administrativeExpenses'] = [];
    $incomeStatement['operationalExpenses']['administrativeExpenses']['total'] = 0;
    foreach ($operationalExpenses as $key => $account) {
      $totalAccount = isset($account['debitBalance']) ? $account['debitBalance'] : $account['creditBalance'];
      $incomeStatement['operationalExpenses']['administrativeExpenses']['total'] += $totalAccount;
      $incomeStatement['operationalExpenses']['administrativeExpenses']['extraAccounts'][$account['accountName']] = $totalAccount;
    }

    $incomeStatement['operationalExpenses']['marketingExpenses'] = [];
    $incomeStatement['operationalExpenses']['marketingExpenses']['total'] = 0;
    foreach ($marketingExpenses as $key => $account) {
      $totalAccount = isset($account['debitBalance']) ? $account['debitBalance'] : $account['creditBalance'];
      $incomeStatement['operationalExpenses']['marketingExpenses']['total'] += $totalAccount;
      $incomeStatement['operationalExpenses']['marketingExpenses']['extraAccounts'][$account['accountName']] = $totalAccount;
    }
    $incomeStatement['operationalExpenses']['total'] = $incomeStatement['operationalExpenses']['administrativeExpenses']['total'] + 
              $incomeStatement['operationalExpenses']['marketingExpenses']['total'];
    $incomeStatement['operationalProfit'] = $incomeStatement['operationalSales']['grossProfitOnSales'] - $incomeStatement['operationalExpenses']['total'];


    $incomeStatement['otherRenevuesAndExpenses'] = [];
    $incomeStatement['otherRenevuesAndExpenses']['total'] = 0;
    foreach ($otherExpenses as $expense) {
      $totalAccount = isset($expense['debitBalance']) ? $expense['debitBalance'] : $expense['creditBalance'];
      $incomeStatement['otherRenevuesAndExpenses']['total'] -= $totalAccount;
      $incomeStatement['otherRenevuesAndExpenses']['extraAccounts'][$expense['accountName']] = $totalAccount;
    }

    foreach ($otherRevenues as $revenue) {
      $totalAccount = isset($revenue['debitBalance']) ? $revenue['debitBalance'] : $revenue['creditBalance'];
      $incomeStatement['otherRenevuesAndExpenses']['total'] += $totalAccount;
      $incomeStatement['otherRenevuesAndExpenses']['extraAccounts'][$revenue['accountName']] = $totalAccount;
    }

    $incomeStatement['incomeBeforeWorkers'] = round($incomeStatement['operationalProfit'] + $incomeStatement['otherRenevuesAndExpenses']['total'], 2);
    $incomeStatement['percentEmployees'] = round($incomeStatement['incomeBeforeWorkers'] * 0.15, 2);
    $incomeStatement['incomeBeforeTax'] = round($incomeStatement['incomeBeforeWorkers'] - $incomeStatement['percentEmployees'], 2);
    $incomeStatement['percentRentTax'] = round($incomeStatement['incomeBeforeTax'] * 0.25, 2);
    $incomeStatement['netProfit'] = round($incomeStatement['incomeBeforeTax'] - $incomeStatement['percentRentTax']);

    
    return $incomeStatement;
  }

}