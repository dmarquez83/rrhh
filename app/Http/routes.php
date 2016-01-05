<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::controllers([
    'auth' => 'AuthController'
]);

Route::get('/', function()
{
    return View::make('auth.login');
});

Route::group(['middleware' => 'auth'], function()
{

    Route::get('ride', 'RideController@index');

    Route::post('excel', 'ExcelController@invoice');
    Route::get('html', 'ExcelController@html');

    Route::get('/dashboard', 'IndexController@index');
    Route::post('/dashboard', 'IndexController@index');

    /*
    |--------------------------------------------------------------------------
    | Customer Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('customer', 'CustomerController');
    Route::post('customer/getByParameterPost', 'CustomerController@getByParameterPost');
    Route::resource('customerCreditDocument', 'CustomerController@creditDocument');
    Route::resource('customerCreditFiles', 'CustomerController@uploadCreditFiles');
    Route::post('customerForTable', 'CustomerController@forTable');
    Route::post('customerforTableWithOutFinalCustomer', 'CustomerController@forTableWithOutFinalCustomer');
    Route::post('customers/withHaveSalesOrder', 'CustomerController@withHaveSalesOrder');
    Route::resource('customerPaymentHistory', 'CustomerPaymentHistoryController');
    Route::post('customers/masiveLoad', 'CustomerController@masiveLoad');
    Route::post('customerForTable', 'CustomerController@forTable');


    /*
    |--------------------------------------------------------------------------
    | Product Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('product', 'ProductController');
    Route::post('product/temporaryStock', 'ProductController@temporaryStock');
    Route::post('product/getBySupplier', 'ProductController@getBySupplier');
    Route::post('productsForTable', 'ProductController@forTable');
    Route::post('products/forSelectize', 'ProductController@forSelectize');

    Route::resource('productCategories', 'ProductCategoriesController');
    Route::post('productCategoryForTable', 'ProductCategoriesController@forTable');
    Route::resource('priceLists', 'PriceListsController');
    Route::post('priceListForTable', 'PriceListsController@forTable');
    Route::post('product/getByParameterPost', 'ProductController@getByParameterPost');
    Route::post('productForTable', 'ProductController@forTable');
    Route::post('product/productMassiveLoad', 'ProductController@massiveLoad');
    Route::post('product/productCategoryMassiveLoad', 'ProductCategoryController@massiveLoad');


    /*
      |--------------------------------------------------------------------------
      | Import Routes
      |--------------------------------------------------------------------------
      */

    Route::resource('tariffHeadings', 'TariffHeadingsController');
    Route::post('tariffHeadingForTable', 'tariffHeadingsController@forTable');

    Route::resource('importQuotation', 'ImportQuotationController');
    Route::post('importQuotation/searchByParameters', 'ImportQuotationController@searchByParameters');
    Route::post('importQuotation/getByParameterPost', 'ImportQuotationController@getByParameterPost');
    Route::post('importQuotation/getAllByParameterPostForConsolidation', 'ImportQuotationController@getAllByParameterPostForConsolidation');
    Route::post('importQuotation/getImportQuotationSuppliers', 'ImportQuotationController@getImportQuotationSuppliers');
    Route::post('importQuotationWithSuppliersForTable', 'ImportQuotationController@importQuotationWithSuppliersForTable');
    Route::post('importQuotation/generateFromSalesOrder', 'ImportQuotationController@generateFromSalesOrder');
    Route::post('importQuotation/forDistribution', 'ImportQuotationController@forDistribution');
    Route::post('importQuotation/saveDistribution', 'ImportQuotationController@saveDistribution');
    Route::post('importQuotationForTable', 'ImportQuotationController@forTable');

    Route::resource('importOrder', 'ImportOrderController');
    Route::post('importOrder/saveConsolidation', 'ImportOrderController@saveConsolidation');
    Route::post('importOrderForTable', 'ImportOrderController@forTable');
    Route::post('importOrderforApproval', 'ImportOrderController@forApproval');
    Route::post('importOrder/forDistribution', 'ImportOrderController@forDistribution');
    Route::post('importOrder/getByParameterPost', 'ImportOrderController@getByParameterPost');
    Route::post('importOrder/forSupplierInvoice', 'ImportOrderController@forSupplierInvoice');
    Route::post('importOrder/forSupplierInvoice/forTable', 'ImportOrderController@forSupplierInvoiceForTable');
    Route::put('approvalImportOrder/{id}', 'ImportOrderController@approval');
    Route::put('rejectedImportOrder/{id}', 'ImportOrderController@rejected');
    Route::post('generateImportOrder', 'ImportOrderController@generate');

    Route::resource('import', 'ImportController');

    /*
    |--------------------------------------------------------------------------
    | Accounting Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('generalJournal', 'GeneralJournalController');
    Route::post('/generalJournal/searchByParameters', 'GeneralJournalController@searchByParameters');
    Route::post('generalJournal/getByParameterPost', 'GeneralJournalController@getByParameterPost');
    Route::post('generalJournalForTable', 'GeneralJournalController@forTable');
    Route::resource('configuration', 'ConfigurationController');

    Route::resource('generalMajor', 'GeneralMajorController');
    Route::post('generalMajor/forTable', 'GeneralMajorController@forTable');
    Route::post('generalMajor/searchByParameters', 'GeneralMajorController@searchByParameters');

    Route::resource('generalBalance', 'GeneralBalanceController');
    Route::post('generalBalanceForTable', 'GeneralBalanceController@forTable');
    Route::post('generalBalance/searchByParameters', 'GeneralBalanceController@searchByParameters');

    Route::resource('statement', 'StatementController');
    Route::post('statement/getByParameterPost', 'StatementController@getByParameterPost');
    Route::post('statement/forSelectize', 'StatementController@forSelectize');
    Route::post('statementForTable', 'StatementController@forTable');

    Route::resource('modelAccountingEntries', 'ModelAccountingEntriesController');
    Route::post('modelAccountingEntriesForTable', 'ModelAccountingEntriesController@forTable');

    Route::post('icomeStatement/forTable', 'IncomeStatementController@forTable');


    /*
    |--------------------------------------------------------------------------
    | Audit Routes
    |--------------------------------------------------------------------------
    */
    Route::get('audit', 'AuditController@index');
    Route::get('auditSearch', 'AuditController@search');

    Route::resource('kardex', 'KardexController');
    Route::post('kardex/searchByParameters', 'KardexController@searchByParameters');
    Route::post('temporaryKardex/searchByParameters', 'TemporaryKardexController@searchByParameters');

    /*
    |--------------------------------------------------------------------------
    | Supplier Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('supplier', 'SupplierController');
    Route::post('supplier/forSelectize', 'SupplierController@forSelectize');
    Route::post('/supplier/specificData', 'SupplierController@specificData');
    Route::post('supplierForTable', 'SupplierController@forTable');
    Route::post('suppliers/forSelectize', 'SupplierController@forSelectize');
    Route::post('supplier/searchByParams', 'SupplierController@searchByParams');
    Route::post('suppliers/getByProductsIds', 'SupplierController@getByProductsIds');

    Route::post('supplierPays/ForTable', 'SupplierPaysController@forTable');
    Route::post('supplierPays/registerExtension', 'SupplierPaysController@registerExtension');
    Route::resource('supplierPaymentHistory', 'SupplierPaymentHistoryController');
    Route::post('suppliers/masiveLoad', 'SupplierController@masiveLoad');
    Route::post('supplierForTable', 'SupplierController@forTable');

    /*
    |--------------------------------------------------------------------------
    | Purchase Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('purchase', 'PurchaseController');
    Route::post('/purchase/searchByParameters', 'PurchaseController@searchByParameters');

    Route::resource('purchaseQuotation', 'PurchaseQuotationController');
    Route::post('purchaseQuotation/searchByParameters', 'PurchaseQuotationController@searchByParameters');
    Route::post('purchaseQuotation/getByParameterPost', 'PurchaseQuotationController@getByParameterPost');
    Route::post('purchaseQuotation/getAllByParameterPostForConsolidation', 'PurchaseQuotationController@getAllByParameterPostForConsolidation');
    Route::post('purchaseQuotation/getPurchaseQuotationSuppliers', 'PurchaseQuotationController@getPurchaseQuotationSuppliers');
    Route::post('purchaseQuotationWithSuppliersForTable', 'PurchaseQuotationController@purchaseQuotationWithSuppliersForTable');
    Route::post('purchaseQuotation/generateFromSalesOrder', 'PurchaseQuotationController@generateFromSalesOrder');
    Route::post('purchaseQuotation/forDistribution', 'PurchaseQuotationController@forDistribution');
    Route::post('purchaseQuotation/saveDistribution', 'PurchaseQuotationController@saveDistribution');
    Route::post('purchaseQuotationForTable', 'PurchaseQuotationController@forTable');
    Route::post('purchaseQuotation/specificData', 'PurchaseQuotationController@specificData');
    Route::post('purchaseQuotation/print', 'PurchaseQuotationController@printDocument');
    Route::post('purchaseQuotation/getByParameterPost', 'PurchaseQuotationController@getByParameterPost');

    Route::resource('purchaseOrder', 'PurchaseOrderController');
    Route::post('purchaseOrder/saveConsolidation', 'PurchaseOrderController@saveConsolidation');
    Route::post('purchaseOrderForTable', 'PurchaseOrderController@forTable');
    Route::post('purchaseOrderforApproval', 'PurchaseOrderController@forApproval');
    Route::post('purchaseOrder/forDistribution', 'PurchaseOrderController@forDistribution');
    Route::post('purchaseOrder/getByParameterPost', 'PurchaseOrderController@getByParameterPost');
    Route::post('purchaseOrder/forSupplierInvoice', 'PurchaseOrderController@forSupplierInvoice');
    Route::post('purchaseOrder/forSupplierInvoice/forTable', 'PurchaseOrderController@forSupplierInvoiceForTable');
    Route::put('approvalPurchaseOrder/{id}', 'PurchaseOrderController@approval');
    Route::put('rejectedPurchaseOrder/{id}', 'PurchaseOrderController@rejected');
    Route::post('generatePurchaseOrder', 'PurchaseOrderController@generate');
    Route::post('purchaseOrder/generateFromPurchaseQuotation', 'PurchaseOrderController@generateFromPurchaseQuotation');
    Route::post('purchaseOrder/specificData', 'PurchaseOrderController@specificData');
    Route::post('purchaseOrder/print', 'PurchaseOrderController@printDocument');


    Route::resource('goodsReceipt', 'GoodsReceiptController');
    Route::post('goodsReceiptForTable', 'GoodsReceiptController@forTable');
    Route::post('goodsReceipt/storeFromPurchaseOrder', 'GoodsReceiptController@storeFromPurchaseOrder');
    Route::post('goodsReceipt/unsubscribe', 'GoodsReceiptController@unsubscribe');
    Route::post('goodsReceipt/getByParameterPost', 'GoodsReceiptController@getByParameterPost');

    Route::resource('goodsReturn', 'GoodsReturnController');
    Route::post('goodsReturnForTable', 'GoodsReturnController@forTable');

    Route::resource('supplierInvoice', 'SupplierInvoiceController');
    Route::post('supplierInvoiceForTable', 'SupplierInvoiceController@forTable');
    Route::post('supplierInvoice/getColumnsByParameters', 'SupplierInvoiceController@getColumnsByParameters');
    Route::post('supplierInvoice/specificData', 'SupplierInvoiceController@specificData');
    Route::post('supplierInvoice/downloadFile', 'SupplierInvoiceController@downloadFile');
    Route::post('supplierInvoice/annul', 'SupplierInvoiceController@annul');
    Route::post('supplierInvoice/forDashboard', 'SupplierInvoiceController@forDashboard');
    Route::post('supplierInvoice/getByParameterPost', 'SupplierInvoiceController@getByParameterPost');

    Route::resource('purchaseRetention', 'PurchaseRetentionController');
    Route::post('purchaseRetention/forTable', 'PurchaseRetentionController@forTable');
    Route::post('purchaseRetention/forTablePending', 'PurchaseRetentionController@forTablePending');
    Route::post('purchaseRetention/specificData', 'PurchaseRetentionController@specificData');
    Route::post('purchaseRetention/downloadFile', 'PurchaseRetentionController@downloadFile');
    Route::post('purchaseRetention/resendElectronicDocument', 'PurchaseRetentionController@resendElectronicDocument');

    Route::resource('invoices', 'InvoicesController');
    Route::post('/invoices/searchByParameters', 'InvoicesController@searchByParameters');

    Route::resource('customerAccount', 'CustomerAccountController');
    Route::post('/customerAccount/searchByParameters', 'CustomerAccountController@searchByParameters');

    Route::resource('purchaseCreditNote', 'PurchaseCreditNoteController');
    Route::resource('purchaseDebitNote', 'PurchaseDebitNoteController');




    /*
    |--------------------------------------------------------------------------
    | Human Resource Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('employee', 'EmployeeController');
    Route::post('getEmployees', 'EmployeeController@getEmployees'); /*Desarrollo*/
    Route::post('employeeForTable', 'EmployeeController@forTable');
    Route::get('employeeBasicInfo', 'EmployeeController@basicInfo');

    Route::resource('departments', 'DepartmentsController');
    Route::post('departmentsForTable', 'DepartmentsController@forTable');
    Route::resource('offices', 'OfficesController');
    Route::post('officesForTable', 'OfficesController@forTable');

    Route::resource('bonus', 'BonusController');
    Route::post('bonus/forTable', 'BonusController@forTable');
    Route::resource('discounts', 'DiscountsController');
    Route::post('discounts/forTable', 'DiscountsController@forTable');

    Route::post('rolLiquidationForTable', 'RolLiquidationController@ForTable');

    Route::resource('humanResourcesConfiguration', 'HumanResourcesConfigurationController');

    Route::resource('bells', 'BellsController');
    Route::post('bellsForTable', 'BellsController@forTable');


    /*
    |--------------------------------------------------------------------------
    | Configuration Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('accountingConfiguration', 'AccountingConfigurationController');
    Route::post('accountingConfiguration/forTable', 'AccountingConfigurationController@forTable');
    Route::resource('companyInfo', 'CompanyInfoController');
    Route::post('companyInfo/validateDigitalSignature', 'CompanyInfoController@validateDigitalSignature');
    Route::post('companyInfo/newCompany', 'CompanyInfoController@newCompany');
    Route::resource('warehouse', 'WarehouseController');
    Route::resource('documentsConfiguration', 'DocumentsConfigurationController');
    Route::post('documentsConfiguration/contable', 'DocumentsConfigurationController@contable');
    Route::resource('document', 'DocumentController');
    Route::resource('modules', 'ModulesController');
    Route::resource('roles', 'RolesController');
    Route::resource('users', 'UsersController');
    Route::post('users/forTable', 'UsersController@forTable');
    Route::resource('generalParameters', 'GeneralParametersController');
    Route::resource('generalParameters/getByParameterPost', 'GeneralParametersController@getByParameterPost');
    Route::post('generalParameters/configParameters', 'GeneralParametersController@configParameters');
    Route::resource('taxTypes', 'TaxTypesController');
    Route::resource('taxTypes/getByParameterPost', 'TaxTypesController@getByParameterPost');
    Route::resource('state', 'StateController');

    Route::resource('documentsApprovalFlows', 'DocumentApprovalFlowController');
    Route::post('documentsApprovalFlowsForTable', 'DocumentApprovalFlowController@forTable');
    Route::post('documentsApprovalFlows/getByParameterPost', 'DocumentApprovalFlowController@getByParameterPost');

    Route::post('getWarehouses', 'UsersController@getWarehouses');
    Route::post('getCurrentWarehouse', 'UsersController@getCurrentWarehouse');
    Route::post('setSelectedWarehouse', 'UsersController@setSelectedWarehouse');

    Route::post('getCompanies', 'UsersController@getCompanies');
    Route::post('getCurrentCompany', 'UsersController@getCurrentCompany');
    Route::post('changeCompany', 'UsersController@changeCompany');

    Route::resource('paymentConditions', 'PaymentConditionsController');
    Route::post('paymentConditions/forTable', 'PaymentConditionsController@forTable');

    Route::resource('paymentWays', 'PaymentWaysController');
    Route::post('paymentWays/forTable', 'PaymentWaysController@forTable');

    Route::resource('paymentMethods', 'PaymentMethodsController');
    Route::post('paymentMethods/forTable', 'PaymentMethodsController@forTable');

    Route::resource('configurationTributationForms', 'ConfigurationTributationFormsController');
    Route::post('configurationTributationForms/forTable', 'ConfigurationTributationFormsController@forTable');




    /*
    |--------------------------------------------------------------------------
    | Bank Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('bank', 'BankController');
    Route::resource('bankAccount', 'BankAccountController');
    Route::resource('checkbookRegister', 'CheckbookRegisterController');
    Route::post('customerQuotasForTable', 'CustomerQuotasController@forTable');
    Route::resource('bankAccount', 'BankAccountController');
    Route::resource('creditCards', 'CreditCardsController');
    Route::post('creditCards/forTable', 'CreditCardsController@forTable');


    /*
    |--------------------------------------------------------------------------
    | Sales Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('salesOffer', 'SalesOfferController');
    Route::resource('salesOffer/getByParameterPost', 'SalesOfferController@getByParameterPost');
    Route::post('salesOfferForTable', 'SalesOfferController@forTable');
    Route::post('salesOfferforApproval', 'SalesOfferController@forApproval');
    Route::put('approvalSalesOffer/{id}', 'SalesOfferController@approval');
    Route::put('rejectedSalesOffer/{id}', 'SalesOfferController@rejected');
    Route::post('salesOffer/specificData', 'SalesOfferController@specificData');
    Route::post('salesOffer/print', 'SalesOfferController@printDocument');

    Route::resource('salesOrder', 'SalesOrderController');
    Route::resource('salesOrder/generateFromSalesOffer', 'SalesOrderController@generateFromSalesOffer');
    Route::post('salesOrder/getByParameterPost', 'SalesOrderController@getByParameterPost');
    Route::post('salesOrder/forTemporaryStockCustomerInvoice', 'SalesOrderController@forTemporaryStockCustomerInvoice');
    Route::post('salesOrder/forTemporaryStockCustomerInvoice/forTable', 'SalesOrderController@forTemporaryStockCustomerInvoiceForTable');
    Route::post('salesOrderForTable', 'SalesOrderController@forTable');
    Route::post('salesOrder/forCustomerInvoice', 'SalesOrderController@forCustomerInvoice');
    Route::post('salesOrder/forCustomerInvoice/forTable', 'SalesOrderController@forCustomerInvoiceForTable');
    Route::post('salesOrderforApproval', 'SalesOrderController@forApproval');
    Route::put('salesOrder/approval/{id}', 'SalesOrderController@approval');
    Route::put('salesOrder/rejected/{id}', 'SalesOrderController@rejected');
    Route::post('salesOrder/specificData', 'SalesOrderController@specificData');
    Route::post('salesOrder/print', 'SalesOrderController@printDocument');

    Route::resource('goodsDelivery', 'GoodsDeliveryController');
    Route::post('goodsDeliveryForTable', 'GoodsDeliveryController@forTable');
    Route::post('goodsDelivery/generateFromReferralGuide', 'GoodsDeliveryController@generateFromReferralGuide');
    Route::post('goodsDelivery/dispatch', 'GoodsDeliveryController@goodsDispatch');

    Route::resource('customerInvoice', 'CustomerInvoiceController');
    Route::post('customerInvoice/specificData', 'CustomerInvoiceController@specificData');
    Route::post('/customerInvoice/searchByParameters', 'CustomerInvoiceController@searchByParameters');
    Route::post('customerInvoiceForTable', 'CustomerInvoiceController@forTable');
    Route::post('customerInvoice/getByParameterPost', 'CustomerInvoiceController@getByParameterPost');
    Route::post('customerInvoiceforApproval', 'CustomerInvoiceController@forApproval');
    Route::put('customerInvoice/approval/{id}', 'CustomerInvoiceController@approval');
    Route::put('customerInvoice/rejected/{id}', 'CustomerInvoiceController@rejected');
    Route::post('customerInvoice/resendElectronicDocument', 'CustomerInvoiceController@resendElectronicDocument');
    Route::post('customerInvoice/forDashboard', 'CustomerInvoiceController@forDashboard');

    Route::post('customerInvoice/downloadFile', 'CustomerInvoiceController@downloadFile');

    Route::resource('salesRetention', 'SalesRetentionController');
    Route::post('salesRetention/forTablePending', 'SalesRetentionController@forTablePending');
    Route::post('salesRetention/forTable', 'SalesRetentionController@forTable');
    Route::post('salesRetention/specificData', 'SalesRetentionController@specificData');
    Route::post('salesRetention/getByParameterPost', 'SalesRetentionController@getByParameterPost');


    Route::resource('salesCreditNote', 'SalesCreditNoteController');
    Route::resource('salesDebitNote', 'SalesDebitNoteController');

    Route::resource('temporaryStockCustomerInvoice', 'TemporaryStockCustomerInvoiceController');
    Route::resource('temporaryStockCustomerInvoice/forTable', 'TemporaryStockCustomerInvoiceController@forTable');


    /*
    |--------------------------------------------------------------------------
    | General Data Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('incomeTaxTable', 'IncomeTaxTableController');
    Route::resource('province', 'ProvinceController');
    Route::resource('maritalStatus', 'MaritalStatusController');
    Route::resource('incomeSource', 'IncomeSourceController');
    Route::resource('payment', 'PaymentController');


    /*
    |--------------------------------------------------------------------------
    | Fixed Asset Data Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('fixedAsset', 'FixedAssetController');
    Route::post('fixedAssetForTable', 'FixedAssetController@forTable');
    Route::resource('fixedAssetCategories', 'FixedAssetCategoriesController');
    Route::post('fixedAssetCategoryForTable', 'FixedAssetCategoriesController@forTable');
    Route::resource('fixedAssetTypes', 'FixedAssetTypesController');
    Route::post('fixedAssetTypeForTable', 'FixedAssetTypesController@forTable');

    /*
    |--------------------------------------------------------------------------
    | Tributation Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('rentTaxes', 'RentTaxesController');
    Route::post('rentTaxes/forTable', 'RentTaxesController@forTable');
    Route::resource('withHoldingTax', 'WithHoldingTaxController');
    Route::post('withHoldingTax/forTable', 'WithHoldingTaxController@forTable');
    Route::resource('retentionTaxes', 'RetentionTaxesController');
    Route::resource('billingTaxes', 'BillingTaxesController');
    Route::resource('importTaxes', 'ImportTaxesController');

    Route::post('generateForms', 'GenerateFormsController@generate');
    Route::post('generateForms/update', 'GenerateFormsController@update');

    Route::post('tributationForms/forTable', 'TributationFormsController@forTable');
    Route::post('tributationForms/generate', 'TributationFormsController@generate');
    Route::resource('tributationForms', 'TributationFormsController');

    /*
    |--------------------------------------------------------------------------
    | Tributation Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('tables', 'TablesController');

    /*
    |--------------------------------------------------------------------------
    | Company Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('companies', 'CompaniesController');
    Route::post('companies/forTable', 'CompaniesController@forTable');

    /*
    |--------------------------------------------------------------------------
    | Logistics Data Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('transports', 'TransportsController');
    Route::post('transports/getByParameterPost', 'TransportsController@getByParameterPost');
    Route::post('transportForTable', 'TransportsController@forTable');

    Route::resource('drivers', 'DriversController');
    Route::post('drivers/getByParameterPost', 'DriversController@getByParameterPost');
    Route::post('driverForTable', 'DriversController@forTable');

    Route::resource('referralGuides', 'ReferralGuideController');
    Route::post('referralGuides/forTable', 'ReferralGuideController@forTable');
    Route::post('referralGuides/generateFromCustomerInvoice', 'ReferralGuideController@generateFromCustomerInvoice');
    Route::post('referralGuides/getByParameterPost', 'ReferralGuideController@getByParameterPost');
    Route::post('referralGuides/receivedCustomer', 'ReferralGuideController@receivedCustomer');


    /*
    |--------------------------------------------------------------------------
    | Inventory Data Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('putInitialInventory', 'PutInitialInventoryController');
    Route::resource('inventoryRemoval', 'InventoryRemovalController');

    Route::post('electronicDocuments/searchByParameters', 'ElectronicDocumentsController@searchByParameters');



});

Route::get('/logout', function(){
    Auth::logout();
    Session::flush();
    return redirect('/');
});
